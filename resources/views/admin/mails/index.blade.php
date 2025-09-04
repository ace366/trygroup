<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4 flex items-center gap-3">
            <span>一括メール配信</span>
            <a href="{{ route('admin.mails.history') }}" class="text-sm px-3 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-700">
                配信履歴を見る
            </a>
        </h1>

        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.mails.preview') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">中学校で絞り込み</label>
                    <select name="school" class="w-full border rounded px-3 py-2">
                        <option value="">（指定なし）</option>
                        @foreach ($schools as $s)
                            <option value="{{ $s }}" {{ (old('school', $filters['school'] ?? '') === $s) ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">学年で絞り込み</label>
                    <select name="grade" class="w-full border rounded px-3 py-2">
                        <option value="">（指定なし）</option>
                        @foreach ($grades as $g)
                            <option value="{{ $g }}" {{ (old('grade', $filters['grade'] ?? '') === $g) ? 'selected' : '' }}>
                                {{ $g }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">英検で絞り込み</label>
                    <select name="eiken" class="w-full border rounded px-3 py-2">
                        <option value="">（指定なし）</option>
                        @foreach ($eikens as $e)
                            <option value="{{ $e }}" {{ (old('eiken', $filters['eiken'] ?? '') === $e) ? 'selected' : '' }}>
                                {{ $e }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">件名</label>
                <input type="text" name="subject" value="{{ old('subject', $subject) }}" maxlength="150"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">本文（プレーンテキスト）</label>
                <textarea name="body" rows="10" maxlength="20000" class="w-full border rounded px-3 py-2" required>{{ old('body', $body) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">※ HTMLは使用できません。本文内のURL/メールは自動リンク化されます。</p>
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">個別（手入力メールアドレス）</label>
                <textarea name="manual_emails" rows="3" class="w-full border rounded px-3 py-2"
                          placeholder="カンマ・改行区切りで複数指定可">{{ old('manual_emails', $manual_emails ?? '') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    プレビュー
                </button>
                @if(isset($recipients) && $recipients->count() > 0)
                    <button form="send-form" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        送信（実行）
                    </button>
                @endif
            </div>
        </form>

        @if(isset($recipients) && $recipients->count() > 0)
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-2">送信先プレビュー（<span id="recipient-total">{{ $recipients->count() }}</span> 件）</h2>

                <form id="send-form" method="POST" action="{{ route('admin.mails.send') }}">
                    @csrf
                    {{-- 再送信用に入力値を保持 --}}
                    <input type="hidden" name="subject" value="{{ old('subject', $subject) }}">
                    <input type="hidden" name="body" value="{{ old('body', $body) }}">
                    <input type="hidden" name="school" value="{{ old('school', $filters['school'] ?? '') }}">
                    <input type="hidden" name="grade" value="{{ old('grade', $filters['grade'] ?? '') }}">
                    <input type="hidden" name="eiken" value="{{ old('eiken', $filters['eiken'] ?? '') }}">
                    <input type="hidden" name="manual_emails" value="{{ old('manual_emails', $manual_emails ?? '') }}">

                    <div class="mb-2 text-sm text-gray-600">
                        ※ チェックした宛先のみ送信します（未チェック時はフィルタ該当全員＋手入力）。<br>
                        ※ 「全選択」にチェックすると一覧のすべてにチェックします（手入力は常に送信対象）。
                    </div>

                    <div class="border rounded max-h-[420px] overflow-y-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="p-2 w-14 text-center">
                                        <input id="check-all" type="checkbox" class="w-4 h-4" />
                                        <div class="text-[10px] text-gray-500">全選択</div>
                                    </th>
                                    <th class="p-2 text-left">名前</th>
                                    <th class="p-2 text-left">メールアドレス</th>
                                    <th class="p-2 text-left">中学校</th>
                                    <th class="p-2 text-left">学年</th>
                                    <th class="p-2 text-left">英検</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($recipients as $r)
                                    @php
                                        $rid    = data_get($r, 'id');
                                        $lname  = data_get($r, 'last_name', '');
                                        $fname  = data_get($r, 'first_name', '');
                                        $email  = data_get($r, 'email', '');
                                        $school = data_get($r, 'school', '');
                                        $grade  = data_get($r, 'grade', '');
                                        $eiken  = data_get($r, 'eiken', '');
                                        $isManual = empty($rid);
                                    @endphp
                                    <tr data-recipient-row data-recipient-manual="{{ $isManual ? '1' : '0' }}">
                                        <td class="p-2 text-center">
                                            @if(!$isManual)
                                                <input type="checkbox" name="selected_ids[]" value="{{ $rid }}" class="w-4 h-4 recipient-check">
                                            @else
                                                <span class="text-xs text-gray-400">手入力</span>
                                            @endif
                                        </td>
                                        <td class="p-2">{{ trim($lname.' '.$fname) }}</td>
                                        <td class="p-2">{{ $email }}</td>
                                        <td class="p-2">{{ $school }}</td>
                                        <td class="p-2">{{ $grade }}</td>
                                        <td class="p-2">{{ $eiken }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        @endif
    </div>
    <!-- Tailwind CDN（開発用） -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
    <script>
        // 全選択トグル & 件数確認ダイアログ
        document.addEventListener('DOMContentLoaded', function () {
            const checkAll = document.getElementById('check-all');
            const checks = () => Array.from(document.querySelectorAll('input.recipient-check'));

            if (checkAll) {
                checkAll.addEventListener('change', () => {
                    checks().forEach(ch => ch.checked = checkAll.checked);
                });
            }

            const sendBtn  = document.querySelector('button[form="send-form"]');
            const sendForm = document.getElementById('send-form');
            if (sendBtn && sendForm) {
                sendBtn.addEventListener('click', (e) => {
                    const checked = checks().filter(ch => ch.checked).length;
                    const manualCount = document.querySelectorAll('tr[data-recipient-manual="1"]').length;
                    const totalRows = document.querySelectorAll('tr[data-recipient-row]').length;
                    const count = checked > 0 ? (checked + manualCount) : totalRows;
                    if (!confirm(count + '件配信します。よろしいですか？')) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });
    </script>
</x-app-layout>
