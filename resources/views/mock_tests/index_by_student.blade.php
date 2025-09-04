<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの模試成績一覧</h2>

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
                <a href="{{ route('students.mock_tests.create', $student->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    ➕ 模試成績を追加
                </a>
            @else
                <button class="bg-gray-300 text-gray-600 px-4 py-2 rounded cursor-not-allowed" disabled>
                    ➕ 模試成績を追加（権限なし）
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
                        <th class="p-2 border">回</th>
                        <th class="p-2 border">国語</th>
                        <th class="p-2 border">国語偏差値</th>
                        <th class="p-2 border">数学</th>
                        <th class="p-2 border">数学偏差値</th>
                        <th class="p-2 border">英語</th>
                        <th class="p-2 border">英語偏差値</th>
                        <th class="p-2 border">3教科合計</th>
                        <th class="p-2 border">3教科偏差値</th>
                        <th class="p-2 border">5教科合計</th>
                        @if(auth()->user()->role === 'user')
                            <th class="p-2 border">操作</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($mockTests as $mock)
                        <tr>
                            <td class="p-2 border">{{ $mock->grade }}</td>
                            <td class="p-2 border">{{ $mock->exam_number }}回</td>
                            <td class="p-2 border">{{ $mock->japanese }}</td>
                            <td class="p-2 border text-blue-600">{{ $mock->japanese_deviation ?? '-' }}</td>
                            <td class="p-2 border">{{ $mock->math }}</td>
                            <td class="p-2 border text-blue-600">{{ $mock->math_deviation ?? '-' }}</td>
                            <td class="p-2 border">{{ $mock->english }}</td>
                            <td class="p-2 border text-blue-600">{{ $mock->english_deviation ?? '-' }}</td>
                            <td class="p-2 border">{{ $mock->three_subjects_total }}</td>
                            <td class="p-2 border text-blue-600">{{ $mock->three_subjects_deviation ?? '-' }}</td>
                            <td class="p-2 border">{{ $mock->five_subjects_total ?? '-' }}</td>
                            @if(auth()->user()->role === 'user')
                                <td class="p-2 border flex gap-2">
                                    <a href="{{ route('mock_tests.edit', [$student->id, $mock->id]) }}" class="text-blue-600 hover:underline">編集</a>
                                    <form action="{{ route('mock_tests.destroy', [$student->id, $mock->id]) }}" method="POST" onsubmit="return confirm('削除しますか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">削除</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="p-4 text-center">模試成績が登録されていません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            <canvas id="mockChart"></canvas>
        </div>
    </div>
</x-app-layout>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
<script>
    const labels = @json($mockTests->pluck('exam_number')->map(fn($n) => $n . '回'));

    const mockChartData = {
        labels: labels,
        datasets: [
            {
                label: '国語',
                data: @json($mockTests->pluck('japanese')),
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.3
            },
            {
                label: '数学',
                data: @json($mockTests->pluck('math')),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3
            },
            {
                label: '英語',
                data: @json($mockTests->pluck('english')),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.3
            },
            {
                label: '3教科合計',
                data: @json($mockTests->pluck('three_subjects_total')),
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                tension: 0.3
            },
            {
                label: '5教科合計',
                data: @json($mockTests->pluck('five_subjects_total')),
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                tension: 0.3
            }
        ]
    };

    const mockChartConfig = {
        type: 'line',
        data: mockChartData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: '{{ $student->last_name }} {{ $student->first_name }} さんの模試点数推移'
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 500,
                    title: {
                        display: true,
                        text: '点数'
                    }
                }
            }
        },
    };

    new Chart(
        document.getElementById('mockChart'),
        mockChartConfig
    );
</script>
