<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6">
            {{ $student->last_name }} {{ $student->first_name }} さんの指導・宿題履歴
        </h2>
        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('guidances.create', ['student_id' => $student->id]) }}"
            {{-- class="text-blue-600 hover:underline"> --}}
            class="px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                新規指導報告
            </a>
            {{-- ✅ ここに戻るボタンを追加 --}}
            <a href="{{ route('students.index') }}"
               {{-- class="ml-6 text-gray-600 hover:underline"> --}}
               class="px-3 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
               戻る
            </a>
        </div><br>
        @if($guidances->isEmpty())
            <p class="text-gray-600">記録はまだありません。</p>
        @else
            {{-- スマホ：カード形式 / PC：テーブル --}}
            <div class="space-y-4 md:hidden"> {{-- ✅ モバイル表示（カード） --}}
                @foreach($guidances as $guidance)
                    <div class="border rounded p-4 shadow text-sm space-y-2">
                        <div><strong>登録日:</strong> {{ $guidance->registered_at }}</div>
                        <div><strong>講師:</strong> {{ $guidance->teacher->last_name ?? '' }}{{ $guidance->teacher->first_name ?? '' }}</div>
                        <div><strong>教科:</strong> {{ $guidance->subject }}</div>
                        <div><strong>単元:</strong> {{ $guidance->unit }}</div>
                        <div><strong>指導内容:</strong><br><span class="whitespace-pre-line">{{ $guidance->content }}</span></div>
                        <div><strong>授業態度・雰囲気:</strong><br><span class="whitespace-pre-line">{{ $guidance->attitude }}</span></div>
                        <div><strong>宿題内容:</strong><br><span class="whitespace-pre-line">{{ $guidance->homework }}</span></div>
                        <div><strong>宿題確認:</strong>
                            @if(!is_null($guidance->homework_flag))
                                <span class="font-bold text-blue-600">{{ $guidance->homework_flag ? '●' : '×' }}</span>
                            @else
                                <form method="POST" action="{{ route('guidances.updateHomeworkFlag', $guidance->id) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="homework_flag" value="1">
                                    <button type="submit" class="text-blue-600 underline">●</button>
                                </form>
                                /
                                <form method="POST" action="{{ route('guidances.updateHomeworkFlag', $guidance->id) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="homework_flag" value="0">
                                    <button type="submit" class="text-blue-600 underline">×</button>
                                </form>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <button onclick="openDetailModal({{ $guidance->id }}, '{{ $student->last_name }}{{ $student->first_name }}')"
                                class="text-blue-600 underline hover:text-blue-800">詳細</button>
                                @if(auth()->user()->role === 'admin' || auth()->id() === $guidance->teacher_id)
                                    <a href="{{ route('guidances.edit', $guidance->id) }}"
                                    class="text-indigo-600 underline hover:text-indigo-800">修正</a>
                                    <form method="POST" action="{{ route('guidances.destroy', $guidance->id) }}"
                                        onsubmit="return confirm('この記録を削除します。よろしいですか？');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 underline hover:text-red-800">削除</button>
                                    </form>
                                @endif
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="hidden md:block overflow-x-auto"> {{-- ✅ PC用テーブル表示 --}}
                <table class="min-w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">登録日</th>
                            <th class="border px-2 py-1">講師</th>
                            <th class="border px-2 py-1">教科</th>
                            <th class="border px-2 py-1">単元</th>
                            <th class="border px-2 py-1">指導内容</th>
                            <th class="border px-2 py-1">宿題内容</th>
                            <th class="border px-2 py-1">宿題確認</th>
                            <th class="border px-2 py-1">詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guidances as $guidance)
                            <tr class="h-[43px]">
                                <td class="border px-2 py-1">{{ $guidance->registered_at }}</td>
                                <td class="border px-2 py-1">{{ $guidance->teacher->last_name ?? '' }}{{ $guidance->teacher->first_name ?? '' }}</td>
                                <td class="border px-2 py-1">{{ $guidance->subject }}</td>
                                <td class="border px-2 py-1">{{ $guidance->unit }}</td>
                                <td class="border px-2 py-1 whitespace-pre-line">{{ $guidance->content }}</td>
                                <td class="border px-2 py-1 whitespace-pre-line">{{ $guidance->homework }}</td>
                                <td class="border px-1 py-1 text-center">
                                    @if(!is_null($guidance->homework_flag))
                                        <span class="text-blue-600 font-bold">{{ $guidance->homework_flag ? '●' : '×' }}</span>
                                    @else
                                        <div class="flex justify-center space-x-2">
                                            <form method="POST" action="{{ route('guidances.updateHomeworkFlag', $guidance->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="homework_flag" value="1">
                                                <button class="text-blue-600">●</button>
                                            </form>
                                            <form method="POST" action="{{ route('guidances.updateHomeworkFlag', $guidance->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="homework_flag" value="0">
                                                <button class="text-blue-600">×</button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                                <td class="border px-2 py-1 text-center">
                                    <button onclick="openDetailModal({{ $guidance->id }}, '{{ $student->last_name }}{{ $student->first_name }}')"
                                        class="text-sm text-blue-600 underline hover:text-blue-800">詳細</button>
                                        @if(auth()->user()->role === 'admin' || auth()->id() === $guidance->teacher_id)
                                            <a href="{{ route('guidances.edit', $guidance->id) }}"
                                            class="text-indigo-600 underline hover:text-indigo-800">修正</a>
                                            <form method="POST" action="{{ route('guidances.destroy', $guidance->id) }}"
                                                onsubmit="return confirm('この記録を削除します。よろしいですか？');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 underline hover:text-red-800">削除</button>
                                            </form>
                                        @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        @endif
    </div>
    <script>
        const guidances = @json($guidances->keyBy('id'));

        function openDetailModal(id, studentName) {
            const data = guidances[id];

            // 生徒名を表示
            document.getElementById('modal-student-name').innerText = studentName;

            let html = `
                <p><strong>登録日:</strong> ${data.registered_at}</p>
                <p><strong>講師:</strong> ${data.teacher?.last_name ?? ''}${data.teacher?.first_name ?? ''}</p>
                <p><strong>講座:</strong> ${data.course_type}</p>
                <p><strong>時間区分:</strong> ${data.time_zone}</p>
                <p><strong>グループ:</strong> ${data.group}</p>
                <p><strong>教科:</strong> ${data.subject}</p>
                <p><strong>単元:</strong> ${data.unit ?? ''}</p>
                <p><strong>理解度:</strong> ${data.understanding_level ?? '-'}</p>
                <p><strong>集中度:</strong> ${data.concentration_level ?? '-'}</p>
                <p><strong>指導内容:</strong><br>${data.content ?? ''}</p>
                <p><strong>宿題内容:</strong><br>${data.homework ?? ''}</p>
                <p><strong>宿題フラグ:</strong> ${data.homework_flag === 1 ? '●' : data.homework_flag === 0 ? '×' : '未設定'}</p>
            `;
            document.getElementById('modal-content').innerHTML = html;
            document.getElementById('detail-modal').classList.remove('hidden');
            document.getElementById('detail-modal').classList.add('flex');


             // ✅ 背景のスクロールを固定
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
            document.getElementById('detail-modal').classList.remove('flex');
            // ✅ 背景のスクロールを復元
            document.body.style.overflow = '';
        }
        function nl2br(str) {
            return str?.replace(/\n/g, "<br>") ?? '';
        }
    </script>
<!-- モーダル全体 -->
<div id="detail-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">

    <!-- モーダルボックス -->
    <div class="bg-white w-[90%] max-w-xl mx-auto rounded shadow-md relative max-h-[70vh] flex flex-col overflow-hidden">
        
        <!-- ✅ 固定ヘッダー -->
        <div class="flex items-start justify-between p-4 border-b">
            <div>
                <h3 class="text-xl font-bold">指導詳細</h3>
                <p id="modal-student-name" class="text-sm text-gray-700"></p> <!-- 生徒名表示 -->
            </div>
            <button onclick="closeDetailModal()"
                    class="text-gray-600 hover:text-red-600 text-xl">&times;</button>
        </div>

        <!-- ✅ スクロールボディ -->
        <div id="modal-content" class="p-4 overflow-y-auto text-sm space-y-2 whitespace-pre-line flex-1">
            <!-- JavaScriptで挿入 -->
        </div>
    </div>
</div>


</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>