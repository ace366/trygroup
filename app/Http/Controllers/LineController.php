<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class LineController extends Controller
{
        /**
     * LINE 登録ページを表示
     */
    public function showRegisterForm()
    {
        $lineLoginUrl = "https://access.line.me/oauth2/v2.1/authorize?"
            . "response_type=code"
            . "&client_id=" . env('LINE_LOGIN_CHANNEL_ID')
            . "&redirect_uri=" . urlencode(config('services.line.callback_url'))  // ✅ URLエンコード適用
            . "&state=" . csrf_token()
            . "&scope=openid%20profile";

        return view('line.register', compact('lineLoginUrl'));
    }

    /**
     * LINEからコールバックされ、ユーザーを登録
     */
    public function handleLineCallback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('line.register.form')->with('error', 'LINE認証に失敗しました。');
        }

        $client = new Client();

        try {
            // LINEからアクセストークンを取得
            $response = $client->post('https://api.line.me/oauth2/v2.1/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $request->get('code'),
                    'redirect_uri' => route('line.callback'),
                    'client_id' => env('LINE_LOGIN_CHANNEL_ID'),
                    'client_secret' => env('LINE_LOGIN_CHANNEL_SECRET'),
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            $accessToken = $body['access_token'];

            // LINEプロフィール情報を取得
            $profileResponse = $client->get('https://api.line.me/v2/profile', [
                'headers' => ['Authorization' => "Bearer {$accessToken}"],
            ]);

            $profile = json_decode($profileResponse->getBody(), true);
            $lineUserId = $profile['userId'];

            // ログイン中のユーザーに `line_user_id` を登録
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('line.register.form')->with('error', 'ログインしてください。');
            }

            // 既に登録済みか確認
            if ($user->line_user_id) {
                return redirect()->route('line.register.form')->with('info', 'すでに登録されています。');
            }

            // ユーザーの `line_user_id` を更新
            DB::table('users')->where('id', $user->id)->update(['line_user_id' => $lineUserId]);

            return redirect()->route('line.register.form')->with('success', 'LINE連携が完了しました！');

        } catch (\Exception $e) {
            Log::error('LINE連携エラー: ' . $e->getMessage());
            return redirect()->route('line.register.form')->with('error', 'LINE連携に失敗しました。');
        }
    }
    /**
     * LINE メッセージ送信フォームを表示
     */
    public function showForm()
    {
        return view('line.send'); // ✅ `resources/views/line/send.blade.php` を表示
    }

    /**
     * LINE メッセージを送信
     */
    public function sendLineMessage(Request $request)
    {
        $request->validate([
            'grade' => 'required|string',
            'message' => 'required|string',
        ]);

        // .env から LINE のアクセストークンを取得
        $accessToken = config('services.line.channel_access_token');

        // 学年ごとのユーザーIDを取得（DBの `users` テーブルに `grade` フィールドがあると仮定）
        $userIds = DB::table('users')
            ->where('grade', $request->input('grade'))
            ->whereNotNull('line_user_id')
            ->pluck('line_user_id')
            ->toArray();

        if (empty($userIds)) {
            return redirect()->route('line.form')->with('error', '選択した学年にLINE IDが登録されている生徒がいません。');
        }

        $client = new Client([
            'base_uri' => 'https://api.line.me/',
            'timeout'  => 5.0,
            'verify'   => config('services.guzzle.verify'), // ✅ `cacert.pem` を適用
        ]);

        try {
            $response = $client->post('v2/bot/message/multicast', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'to' => $userIds,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $request->input('message'),
                        ]
                    ]
                ],
            ]);

            return redirect()->route('line.form')->with('success', 'メッセージが送信されました！');

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('LINE API Request Error: ' . $e->getMessage());

            return redirect()->route('line.form')->with('error', 'メッセージの送信に失敗しました。');
        }
    }
}
