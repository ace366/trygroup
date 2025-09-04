<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ 追加
use App\Models\Video;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\PlayHistory;
use Illuminate\Support\Facades\DB;


class VideoController extends Controller
{
/////////////////////////////////////////////////////////
public function testDashboard(Request $request)
{
    $selected = $request->input('subject', '英語');

    $definedSubjects = ['英語', '数学', '国語', '理科', '社会', '英検'];
    $videos = in_array($selected, $definedSubjects)
        ? Video::where('subject', $selected)->get()
        : Video::whereNotIn('subject', $definedSubjects)->get();

    $subject = in_array($selected, $definedSubjects) ? $selected : 'その他';

    // ✅ ログインしていれば視聴履歴を取得、なければ空配列
    if (Auth::check()) {
        $viewedVideoIds = PlayHistory::where('user_id', Auth::id())
            ->where('watched_seconds', '>=', 30)
            ->pluck('video_id')
            ->toArray();
    } else {
        $viewedVideoIds = [];
    }

    // ✅ ランキング用の全ユーザー取得
    $users = \App\Models\User::select('id', 'last_name', 'first_name', 'total_playtime')
        ->whereNotNull('total_playtime')
        ->orderByDesc(DB::raw('CAST(total_playtime AS INTEGER)'))
        ->get();

    $currentUser = Auth::user();
    $rawSeconds = ($currentUser && is_numeric($currentUser->total_playtime))
        ? (int) $currentUser->total_playtime
        : 0;

    $seconds = $rawSeconds;
    $rank = null;

    if ($currentUser) {
        foreach ($users as $i => $user) {
            if ($user->id === $currentUser->id) {
                $rank = $i + 1;
                break;
            }
        }
    }

    $levelMaxSeconds = 6 * 60 * 60;
    $currentLevelSeconds = $seconds % $levelMaxSeconds;

    $levelData = [
        'hours' => floor($seconds / 3600),
        'minutes' => floor(($seconds % 3600) / 60),
        'level' => floor($seconds / $levelMaxSeconds) + 1,
        'percent' => min(100, round(($currentLevelSeconds / $levelMaxSeconds) * 100)),
        'nextLevelRemaining' => $levelMaxSeconds - $currentLevelSeconds,
    ];

    return view('videos.dashboard_ts', [
        'videos' => $videos,
        'subject' => $subject,
        'rank' => $rank,
        'total' => $users->count(),
        'levelData' => $currentUser ? $levelData : null,  // ゲストには非表示
        'viewedVideoIds' => $viewedVideoIds,
    ]);
}

   public function testList(Request $request)
{
    $subject = $request->input('subject');
    $grade = $request->input('grade');

    $definedSubjects = ['英語', '数学', '国語', '理科', '社会', '英検'];

    $query = Video::query()->where('grade', $grade);

    if ($subject === 'その他') {
        $query->whereNotIn('subject', $definedSubjects);
    } else {
        $query->where('subject', $subject);
    }

    // ✅ 登録が新しい順に並び替え
    $paginatedVideos = (clone $query)->orderByDesc('id')->paginate(10);

    // ✅ 月別グループ（スマホ用）
    $groupedVideos = $query->orderByDesc('id')->get()->groupBy(function ($video) {
        return \Carbon\Carbon::parse($video->created_at)->format('Y年m月');
    });

    // ✅ ゲスト対応：ログイン時のみ視聴履歴取得
    if (Auth::check()) {
        $viewedVideoIds = \App\Models\PlayHistory::where('user_id', Auth::id())
            ->where('watched_seconds', '>=', 30)
            ->pluck('video_id')
            ->toArray();
    } else {
        $viewedVideoIds = [];
    }

    // ✅ ランキングとレベル情報
    $users = \App\Models\User::select('id', 'last_name', 'first_name', 'total_playtime')
        ->whereNotNull('total_playtime')
        ->orderByDesc(DB::raw('CAST(total_playtime AS INTEGER)'))
        ->get();

    $currentUser = Auth::user();

    $rawSeconds = ($currentUser && is_numeric($currentUser->total_playtime))
        ? (int) $currentUser->total_playtime
        : 0;

    $rank = null;
    if ($currentUser) {
        foreach ($users as $i => $user) {
            if ($user->id === $currentUser->id) {
                $rank = $i + 1;
                break;
            }
        }
    }

    $levelMaxSeconds = 6 * 60 * 60;
    $currentLevelSeconds = $rawSeconds % $levelMaxSeconds;

    $levelData = [
        'hours' => floor($rawSeconds / 3600),
        'minutes' => floor(($rawSeconds % 3600) / 60),
        'level' => floor($rawSeconds / $levelMaxSeconds) + 1,
        'percent' => min(100, round(($currentLevelSeconds / $levelMaxSeconds) * 100)),
        'nextLevelRemaining' => $levelMaxSeconds - $currentLevelSeconds,
    ];

    return view('videos.list_ts', [
        'videos' => $paginatedVideos,
        'groupedVideos' => $groupedVideos,
        'subject' => $subject,
        'grade' => $grade,
        'total' => $users->count(),
        'rank' => $rank,
        'levelData' => $currentUser ? $levelData : null,
        'viewedVideoIds' => $viewedVideoIds,
    ]);
}

    public function savePlaytime(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'guest'], 403);
        }

        $seconds = intval($request->input('seconds', 0));
        $videoId = intval($request->input('video_id'));

        if ($seconds <= 0 || !$videoId) {
            return response()->json(['status' => 'invalid'], 400);
        }

        DB::beginTransaction();
        try {
            $history = PlayHistory::firstOrNew([
                'user_id' => $user->id,
                'video_id' => $videoId
            ]);

            // 既存秒数と比較して上回る場合のみ更新
            $previousSeconds = $history->watched_seconds ?? 0;
            if ($seconds > $previousSeconds) {
                $diff = $seconds - $previousSeconds;
                $history->watched_seconds = $seconds;
                $history->save();

                // total_playtime も差分のみ加算
                $user->total_playtime = ($user->total_playtime ?? 0) + $diff;
                $user->save();
            }

            DB::commit();
            return response()->json(['status' => 'success', 'added' => $diff ?? 0]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('再生時間保存エラー: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
    public function history()
    {
        $user = Auth::user();

        $histories = PlayHistory::with('video')
            ->where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->get();

        return view('videos.history', compact('histories'));
    }
    public function userHistories()
    {
        $histories = \App\Models\PlayHistory::with(['user', 'video'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.user_histories', compact('histories'));
    }
    public function videoRanking()
    {
        $ranking = \App\Models\PlayHistory::select('video_id', DB::raw('SUM(watched_seconds) as total_seconds'), DB::raw('COUNT(*) as view_count'))
            ->groupBy('video_id')
            ->orderByDesc('total_seconds')
            ->with('video')
            ->take(10)
            ->get();

        return view('admin.video_ranking', compact('ranking'));
    }
    public function userRanking()
    {
        $ranking = \App\Models\User::select('id', 'last_name', 'first_name', 'total_playtime')
            ->whereNotNull('total_playtime')
            ->orderByDesc(DB::raw('CAST(total_playtime AS INTEGER)'))
            ->take(10)
            ->get();

        return view('admin.user_ranking', compact('ranking'));
    }
/////////////////////////////////////////////////////////////////
    public function dashboard()
    {
        return view('videos.dashboard', [
            'user' => Auth::user(),
            'videos' => Video::all(), // 適宜変更
        ]);
    }
    public function index(Request $request)
    {
        // クエリパラメータを取得（学年・科目）
        $grade = $request->input('grade');
        $subject = $request->input('subject');

        // クエリビルダーでフィルタリング
        $query = Video::query();

        if (!empty($grade)) {
            $query->where('grade', $grade);
        }

        if (!empty($subject)) {
            $query->where('subject', $subject);
        }

        // フィルタリング後にページネーションを適用
        $videos = $query->paginate(4);

        // 学年・科目一覧を取得（フィルタリング時も選択肢を維持するため）
        $grades = Video::distinct()->pluck('grade');
        $subjects = Video::distinct()->pluck('subject');

        return view('videos.dashboard', compact('videos', 'grades', 'subjects'));
    }

    public function create()
    {
        $videos = Video::orderBy('created_at', 'desc')->get(); // ← 新しい順で取得
        return view('videos.create', compact('videos'));
    }
    // 表示用
    public function edit($id)
    {
        $video = Video::findOrFail($id);
        return view('videos.edit', compact('video'));
    }
    public function destroy($id)
    {
        $video = \App\Models\Video::findOrFail($id);
        $video->delete();
    
        return redirect()->route('videos.create')->with('success', '動画を削除しました。');
    }
    // 更新用
    public function update(Request $request, $id)
    {
        $request->validate([
            'grade' => 'required|string',
            'subject' => 'required|string',
            'unit' => 'required|string',
            'youtube_url' => 'required|url',
        ]);

        $video = Video::findOrFail($id);
        $video->update($request->only('grade', 'subject', 'unit', 'youtube_url'));

        return redirect()->route('videos.create')->with('success', '動画情報を更新しました');
    }
    public function store(Request $request)
    {
        $request->validate([
            'grade' => 'required|string',
            'subject' => 'required|string',
            'unit' => 'required|string',
            'youtube_url' => 'required|url',
        ]);

        // YouTube URL の変換
        $youtubeURL = $this->convertToEmbedURL($request->youtube_url);

        // データ保存
        Video::create([
            'grade' => $request->grade,
            'subject' => $request->subject,
            'unit' => $request->unit,
            'youtube_url' => $youtubeURL,
        ]);

        return redirect()->route('videos.create')->with('success', '動画を登録しました！');
    }

    /**
     * YouTube のショート URL を埋め込み URL に変換する
     */
    private function convertToEmbedURL($url)
    {
        if (strpos($url, 'youtu.be/') !== false) {
            // "https://youtu.be/X0-xqqS8Itk" → "https://www.youtube.com/embed/X0-xqqS8Itk"
            $videoID = str_replace('https://youtu.be/', '', $url);
            return "https://www.youtube.com/embed/" . $videoID;
        }
        return $url;
    }

}
