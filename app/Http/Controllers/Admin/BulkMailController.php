<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BulkNotificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BulkMailController extends Controller
{
    /** 作成画面 */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        // 絞り込み候補
        $schools = User::query()
            ->whereNotNull('school')->where('school', '!=', '')
            ->distinct()->orderBy('school')->pluck('school');

        $grades = User::query()
            ->whereNotNull('grade')->where('grade', '!=', '')
            ->distinct()->orderBy('grade')->pluck('grade');

        $eikens = User::query()
            ->whereNotNull('eiken')->where('eiken', '!=', '')
            ->distinct()->orderBy('eiken')->pluck('eiken');

        return view('admin.mails.index', [
            'schools'        => $schools,
            'grades'         => $grades,
            'eikens'         => $eikens,
            'recipients'     => collect(),
            'subject'        => '',
            'body'           => '',
            'filters'        => ['school' => '', 'grade' => '', 'eiken' => ''],
            'manual_emails'  => '',
        ]);
    }

    /** プレビュー（受信者抽出） */
    public function preview(Request $request)
    {
        $this->ensureAdmin();

        [$filters, $subject, $body, $manualEmails] = $this->validatePayload($request, preview: true);

        // フィルタ有無
        $hasFilter = !empty($filters['school']) || !empty($filters['grade']) || !empty($filters['eiken']);
        // 「手入力のみ」（= フィルタなし & 手入力あり）
        $manualOnly = (count($manualEmails) > 0) && !$hasFilter;

        $recipients = $manualOnly
            ? collect()
            : $this->buildRecipientQuery($filters)->get([
                'users.id as id',
                'users.last_name as last_name',
                'users.first_name as first_name',
                'users.email as email',
                'users.school as school',
                'users.grade as grade',
                'users.eiken as eiken',
            ]);

        // 手入力メールを仮受信者として加える（重複排除は mergeRecipients で実施）
        $manual = collect($manualEmails)->map(fn ($e) => [
            'id'         => null,
            'last_name'  => '[手入力]',
            'first_name' => '',
            'email'      => $e,
            'school'     => '',
            'grade'      => '',
            'eiken'      => '',
        ]);
        $merged = $this->mergeRecipients($recipients, $manual);

        $schools = User::whereNotNull('school')->where('school','!=','')->distinct()->orderBy('school')->pluck('school');
        $grades  = User::whereNotNull('grade')->where('grade','!=','')->distinct()->orderBy('grade')->pluck('grade');
        $eikens  = User::whereNotNull('eiken')->where('eiken','!=','')->distinct()->orderBy('eiken')->pluck('eiken');

        return view('admin.mails.index', [
            'schools'        => $schools,
            'grades'         => $grades,
            'eikens'         => $eikens,
            'recipients'     => $merged,
            'subject'        => $subject,
            'body'           => $body,
            'filters'        => $filters,
            'manual_emails'  => implode("\n", $manualEmails),
        ]);
    }

    /** 送信（同期送信） */
    public function send(Request $request)
    {
        $this->ensureAdmin();

        [$filters, $subject, $body, $manualEmails, $selectedIds] = $this->validatePayload($request, preview: false);

        $hasSelected = !empty($selectedIds);
        $hasFilter   = !empty($filters['school']) || !empty($filters['grade']) || !empty($filters['eiken']);
        // 「手入力のみ」（= チェックなし & フィルタなし & 手入力あり）
        $manualOnly  = (count($manualEmails) > 0) && !$hasSelected && !$hasFilter;

        // 抽出
        if ($manualOnly) {
            $dbRecipients = collect(); // DBからは一切取得しない（誤爆防止）
        } else {
            $query = $this->buildRecipientQuery($filters);
            if ($hasSelected) {
                // 個別チェックがあればそれを優先
                $query->whereIn('users.id', $selectedIds);
            }
            $dbRecipients = $query->get([
                'users.id as id',
                'users.email as email',
            ]);
        }

        // 手入力のうち解除済みを除外
        $manualRaw = collect($manualEmails)->map(fn ($e) => mb_strtolower(trim($e)))->filter();
        $unsubs    = DB::table('mail_unsubscribes')->whereIn('email', $manualRaw)->pluck('email')->all();
        $manual    = $manualRaw->reject(fn ($e) => in_array($e, $unsubs, true))
                               ->map(fn ($e) => (object) ['id' => null, 'email' => $e]);

        $merged = $this->mergeRecipients($dbRecipients, $manual)->map(fn ($r) => (object) $r);

        if ($merged->isEmpty()) {
            return back()->withErrors(['recipients' => '送信先が選択されていません。'])->withInput();
        }

        // バッチ作成（本文は暗号化して保存）
        $batchId = DB::table('bulk_mail_batches')->insertGetId([
            'subject'        => $subject,
            'body_encrypted' => Crypt::encryptString($body),
            'filters_json'   => json_encode($filters, JSON_UNESCAPED_UNICODE),
            'created_by'     => Auth::id(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 受信者ログ作成
        DB::transaction(function () use ($merged, $batchId) {
            $rows = [];
            foreach ($merged as $r) {
                $rows[] = [
                    'batch_id'   => $batchId,
                    'user_id'    => data_get($r, 'id'),
                    'email'      => (string) data_get($r, 'email', ''),
                    'status'     => 'queued',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('bulk_mail_recipients')->insert($chunk);
            }
        });

        // 同期送信
        foreach ($merged as $r) {
            $email = (string) data_get($r, 'email', '');
            if ($email === '') {
                continue;
            }
            try {
                Mail::mailer('smtp')->to($email)->send(
                    new BulkNotificationMail($subject, $body, $email, data_get($r, 'id'), $batchId)
                );

                DB::table('bulk_mail_recipients')
                    ->where('batch_id', $batchId)
                    ->where('email', $email)
                    ->update([
                        'status'     => 'sent',
                        'sent_at'    => now(),
                        'updated_at' => now(),
                    ]);
            } catch (\Throwable $e) {
                DB::table('bulk_mail_recipients')
                    ->where('batch_id', $batchId)
                    ->where('email', $email)
                    ->update([
                        'status'        => 'failed',
                        'error_message' => mb_substr($e->getMessage(), 0, 1000),
                        'updated_at'    => now(),
                    ]);

                Log::error('Bulk mail send failed', [
                    'batch_id' => $batchId,
                    'email'    => $email,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.mails.index')->with('status', '送信処理が完了しました。');
    }

    /** 管理者チェック */
    private function ensureAdmin(): void
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== 'admin') {
            abort(403);
        }
    }

    /** フィルタに応じた受信者クエリ（email必須／解除済み除外／重複排除） */
    private function buildRecipientQuery(array $filters)
    {
        $q = User::query()
            ->where('role', 'user')
            ->whereNotNull('users.email')
            ->where('users.email', '!=', '')
            // 解除済み除外：サブクエリで曖昧さを回避
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('mail_unsubscribes as mu')
                    ->whereColumn('mu.email', 'users.email');
            });

        if (!empty($filters['school'])) {
            $q->where('users.school', $filters['school']);
        }
        if (!empty($filters['grade'])) {
            $q->where('users.grade', $filters['grade']);
        }
        if (!empty($filters['eiken'])) {
            $q->where('users.eiken', $filters['eiken']);
        }

        // 同一メール重複を避ける（家庭兄弟など）
        $q->groupBy('users.email');

        return $q;
    }

    /** 入力検証＋整形 */
    private function validatePayload(Request $request, bool $preview = false): array
    {
        $rules = [
            'subject'        => ['required', 'string', 'max:150'],
            'body'           => ['required', 'string', 'max:20000'],
            'school'         => ['nullable', 'string', 'max:255'],
            'grade'          => ['nullable', 'string', 'max:255'],
            'eiken'          => ['nullable', 'string', 'max:255'],
            'manual_emails'  => ['nullable', 'string', 'max:5000'],
            'selected_ids'   => ['nullable', 'array'],
            'selected_ids.*' => ['integer'],
        ];

        $v = Validator::make($request->all(), $rules);
        $v->after(function ($v) use ($request, $preview) {
            // 手入力メール形式チェック
            $emails = $this->parseEmails((string) $request->input('manual_emails', ''));
            foreach ($emails as $e) {
                if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
                    $v->errors()->add('manual_emails', "無効なメールアドレスです: {$e}");
                }
            }
            if (!$preview) {
                $hasSelected = is_array($request->input('selected_ids')) && count($request->input('selected_ids')) > 0;
                $hasManual   = count($emails) > 0;
                $hasFilter   = $request->filled('school') || $request->filled('grade') || $request->filled('eiken');
                if (!$hasSelected && !$hasManual && !$hasFilter) {
                    $v->errors()->add('recipients', '宛先が指定されていません（フィルタ／個別選択／手入力のいずれかが必要です）。');
                }
            }
        })->validate();

        $filters = [
            'school' => trim((string) $request->input('school', '')),
            'grade'  => trim((string) $request->input('grade', '')),
            'eiken'  => trim((string) $request->input('eiken', '')),
        ];

        $subject = trim((string) $request->input('subject', ''));
        $body    = trim((string) $request->input('body', ''));
        $manual  = $this->parseEmails((string) $request->input('manual_emails', ''));
        $ids     = array_map('intval', $request->input('selected_ids', []));

        return [$filters, $subject, $body, $manual, $ids];
    }

    /** 手入力メールを分割・整形・重複排除 */
    private function parseEmails(?string $raw): array
    {
        if ($raw === null || $raw === '') return [];
        $parts = preg_split('/[\s,;]+/u', $raw);
        $parts = array_filter(array_map('trim', $parts));
        $parts = array_values(array_unique($parts));
        return $parts;
    }

    /** 2つの受信者集合を email でユニーク結合 */
    private function mergeRecipients($a, $b)
    {
        $map = [];
        foreach (collect($a) as $r) {
            $email = is_array($r) ? ($r['email'] ?? null) : ($r->email ?? null);
            if ($email) {
                $map[strtolower($email)] = [
                    'id'         => is_array($r) ? ($r['id'] ?? null) : ($r->id ?? null),
                    'last_name'  => is_array($r) ? ($r['last_name'] ?? '') : ($r->last_name ?? ''),
                    'first_name' => is_array($r) ? ($r['first_name'] ?? '') : ($r->first_name ?? ''),
                    'email'      => $email,
                    'school'     => is_array($r) ? ($r['school'] ?? '') : ($r->school ?? ''),
                    'grade'      => is_array($r) ? ($r['grade'] ?? '') : ($r->grade ?? ''),
                    'eiken'      => is_array($r) ? ($r['eiken'] ?? '') : ($r->eiken ?? ''),
                ];
            }
        }
        foreach (collect($b) as $r) {
            $email = is_array($r) ? ($r['email'] ?? null) : ($r->email ?? null);
            if ($email) {
                $map[strtolower($email)] = [
                    'id'         => is_array($r) ? ($r['id'] ?? null) : ($r->id ?? null),
                    'last_name'  => is_array($r) ? ($r['last_name'] ?? '') : ($r->last_name ?? ''),
                    'first_name' => is_array($r) ? ($r['first_name'] ?? '') : ($r->first_name ?? ''),
                    'email'      => $email,
                    'school'     => is_array($r) ? ($r['school'] ?? '') : ($r->school ?? ''),
                    'grade'      => is_array($r) ? ($r['grade'] ?? '') : ($r->grade ?? ''),
                    'eiken'      => is_array($r) ? ($r['eiken'] ?? '') : ($r->eiken ?? ''),
                ];
            }
        }
        return collect(array_values($map))->sortBy('email')->values();
    }
    /** 履歴一覧 */
    public function history(Request $request)
    {
        $this->ensureAdmin();

        $rows = DB::table('bulk_mail_batches as b')
            ->leftJoin('bulk_mail_recipients as r', 'r.batch_id', '=', 'b.id')
            ->leftJoin('users as u', 'u.id', '=', 'b.created_by')
            ->selectRaw("
                b.id,
                b.subject,
                b.filters_json,
                b.created_at,
                COALESCE(u.last_name || ' ' || u.first_name, '管理者') as creator_name,
                COUNT(r.id) as total,
                SUM(CASE WHEN r.status='sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN r.status='failed' THEN 1 ELSE 0 END) as failed
            ")
            ->groupBy('b.id')
            ->orderByDesc('b.created_at')
            ->paginate(20);

        return view('admin.mails.history', compact('rows'));
    }

    /** 履歴詳細（復号して表示） */
    public function historyShow(int $batch)
    {
        $this->ensureAdmin();

        $rec = DB::table('bulk_mail_batches as b')
            ->leftJoin('users as u', 'u.id', '=', 'b.created_by')
            ->select('b.*', DB::raw("COALESCE(u.last_name || ' ' || u.first_name, '管理者') as creator_name"))
            ->where('b.id', $batch)
            ->first();

        if (!$rec) abort(404);

        $rec->body_plain = '';
        try {
            $rec->body_plain = Crypt::decryptString($rec->body_encrypted);
        } catch (\Throwable $e) {
            $rec->body_plain = '[本文の復号に失敗しました]';
        }

        // 件数集計
        $counts = DB::table('bulk_mail_recipients')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status='sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status='failed' THEN 1 ELSE 0 END) as failed
            ")
            ->where('batch_id', $batch)
            ->first();

        return view('admin.mails.history_show', [
            'batch'  => $rec,
            'counts' => $counts,
        ]);
    }

    /** 履歴から再利用して新規作成画面へ */
    public function reuse(int $batch)
    {
        $this->ensureAdmin();

        $rec = DB::table('bulk_mail_batches')->where('id', $batch)->first();
        if (!$rec) abort(404);

        try {
            $body = Crypt::decryptString($rec->body_encrypted);
        } catch (\Throwable $e) {
            $body = '';
        }

        // コンポーズ画面用データ（候補リスト）
        $schools = User::query()->whereNotNull('school')->where('school','!=','')
            ->distinct()->orderBy('school')->pluck('school');
        $grades  = User::query()->whereNotNull('grade')->where('grade','!=','')
            ->distinct()->orderBy('grade')->pluck('grade');
        $eikens  = User::query()->whereNotNull('eiken')->where('eiken','!=','')
            ->distinct()->orderBy('eiken')->pluck('eiken');

        // 以前のフィルタを復元（存在しなければ空）
        $filters = ['school'=>'','grade'=>'','eiken'=>''];
        $oldFilters = json_decode((string)($rec->filters_json ?? ''), true);
        if (is_array($oldFilters)) {
            $filters = array_merge($filters, array_intersect_key($oldFilters, $filters));
        }

        return view('admin.mails.index', [
            'schools'        => $schools,
            'grades'         => $grades,
            'eikens'         => $eikens,
            'recipients'     => collect(), // 再利用時はプレビュー前なので空
            'subject'        => (string) $rec->subject,
            'body'           => (string) $body,
            'filters'        => $filters,
            'manual_emails'  => '',
        ])->with('status', '過去の配信内容を読み込みました。必要に応じて編集して送信してください。');
    }

}
