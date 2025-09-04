<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの内申点一覧</h2>

        <div class="mb-4 flex gap-x-4">
            @if(in_array(auth()->user()->role, ['admin', 'teacher']))
                <a href="{{ route('students.dashboard', $student->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    ダッシュボードに戻る
                </a>
            @else
                <a href="{{ route('students.dashboard', auth()->id()) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    ダッシュボードに戻る
                </a>
            @endif
            @if(auth()->user()->role === 'user')
                <a href="{{ route('report-cards.create', $student->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    ➕ 内申点を追加
                </a>
            @else
                <button class="bg-gray-300 text-gray-600 px-4 py-2 rounded cursor-not-allowed" disabled>
                    ➕ 内申点を追加（権限なし）
                </button>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">学年</th>
                        <th class="p-2 border">学期</th>
                        <th class="p-2 border">国語</th>
                        <th class="p-2 border">数学</th>
                        <th class="p-2 border">英語</th>
                        <th class="p-2 border">理科</th>
                        <th class="p-2 border">社会</th>
                        <th class="p-2 border">体育</th>
                        <th class="p-2 border">音楽</th>
                        <th class="p-2 border">家庭科</th>
                        <th class="p-2 border">技術</th>
                        <th class="p-2 border">3科合計</th>
                        <th class="p-2 border">5科合計</th>
                        <th class="p-2 border">9科合計</th>
                        <th class="p-2 border">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr class="{{ $report->semester == 4 ? 'border-2 border-black bg-yellow-100' : '' }}">
                            <td class="p-2 border">{{ $report->grade }}</td>
                            <td class="p-2 border">
                                @if($report->semester == 4)
                                    学年
                                @else
                                    {{ $report->semester }}学期
                                @endif
                            </td>
                            <td class="p-2 border">{{ $report->japanese }}</td>
                            <td class="p-2 border">{{ $report->math }}</td>
                            <td class="p-2 border">{{ $report->english }}</td>
                            <td class="p-2 border">{{ $report->science }}</td>
                            <td class="p-2 border">{{ $report->social }}</td>
                            <td class="p-2 border">{{ $report->pe }}</td>
                            <td class="p-2 border">{{ $report->music }}</td>
                            <td class="p-2 border">{{ $report->home_economics }}</td>
                            <td class="p-2 border">{{ $report->technology }}</td>
                            <td class="p-2 border">{{ $report->three_subjects_total }}</td>
                            <td class="p-2 border">{{ $report->five_subjects_total }}</td>
                            <td class="p-2 border">{{ $report->nine_subjects_total }}</td>
                            <td class="p-2 border flex gap-2">
                                <a href="{{ route('report-cards.edit', [$student->id, $report->id]) }}" class="text-blue-600 hover:underline">編集</a>

                                <form action="{{ route('report-cards.destroy', [$student->id, $report->id]) }}" method="POST" onsubmit="return confirm('削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="p-4 text-center">内申点データがありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>