<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの実力テスト成績一覧</h2>

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
                <a href="{{ route('hokushin-tests.create', $student->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    ➕ 実力テスト成績を追加
                </a>
            @else
                <button class="bg-gray-300 text-gray-600 px-4 py-2 rounded cursor-not-allowed" disabled>
                    ➕ 実力テスト成績を追加（権限なし）
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
                        <th class="p-2 border">国語点数</th>
                        <th class="p-2 border">国語偏差値</th>
                        <th class="p-2 border">数学点数</th>
                        <th class="p-2 border">数学偏差値</th>
                        <th class="p-2 border">英語点数</th>
                        <th class="p-2 border">英語偏差値</th>
                        <th class="p-2 border">理科点数</th>
                        <th class="p-2 border">理科偏差値</th>
                        <th class="p-2 border">社会点数</th>
                        <th class="p-2 border">社会偏差値</th>
                        <th class="p-2 border">3教科合計</th>
                        <th class="p-2 border">5教科合計</th>
                        <th class="p-2 border">3教科偏差値</th>
                        <th class="p-2 border">5教科偏差値</th>
                        <th class="p-2 border">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $test)
                        <tr>
                            <td class="p-2 border">{{ $test->grade }}</td>
                            <td class="p-2 border">{{ $test->exam_number }}回</td>

                            <!-- 国語 -->
                            <td class="p-2 border">{{ $test->japanese }}</td>
                            <td class="p-2 border text-blue-600">{{ $test->japanese_deviation ?? '-' }}</td>

                            <!-- 数学 -->
                            <td class="p-2 border">{{ $test->math }}</td>
                            <td class="p-2 border text-blue-600">{{ $test->math_deviation ?? '-' }}</td>

                            <!-- 英語 -->
                            <td class="p-2 border">{{ $test->english }}</td>
                            <td class="p-2 border text-blue-600">{{ $test->english_deviation ?? '-' }}</td>

                            <!-- 理科 -->
                            <td class="p-2 border">{{ $test->science }}</td>
                            <td class="p-2 border text-blue-600">{{ $test->science_deviation ?? '-' }}</td>

                            <!-- 社会 -->
                            <td class="p-2 border">{{ $test->social }}</td>
                            <td class="p-2 border text-blue-600">{{ $test->social_deviation ?? '-' }}</td>

                            <!-- 合計系 -->
                            <td class="p-2 border">{{ $test->three_subjects_total }}</td>
                            <td class="p-2 border">{{ $test->five_subjects_total }}</td>
                            <td class="p-2 border text-blue-600">{{ $test->three_subjects_deviation ?? '-' }}</td>
                            <td class="p-2 border text-blue-600">{{ $test->five_subjects_deviation ?? '-' }}</td>

                            <!-- 操作 -->
                            <td class="p-2 border flex gap-2">
                                <a href="{{ route('hokushin-tests.edit', [$student->id, $test->id]) }}" class="text-blue-600 hover:underline">編集</a>
                                <form action="{{ route('hokushin-tests.destroy', [$student->id, $test->id]) }}" method="POST" onsubmit="return confirm('削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="p-4 text-center">実力テストデータがありません</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
        <div class="mt-8">
            <canvas id="hokushinChart"></canvas>
        </div>
    </div>
</x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
<script>
    // テスト回（第1回〜第8回）をラベルにする
    const hokushinLabels = @json($tests->pluck('exam_number')->map(fn($num) => $num . '回'));

    const hokushinData = {
        labels: hokushinLabels,
        datasets: [
            {
                label: '国語',
                data: @json($tests->pluck('japanese')),
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.3
            },
            {
                label: '数学',
                data: @json($tests->pluck('math')),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3
            },
            {
                label: '英語',
                data: @json($tests->pluck('english')),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.3
            },
            {
                label: '理科',
                data: @json($tests->pluck('science')),
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                tension: 0.3
            },
            {
                label: '社会',
                data: @json($tests->pluck('social')),
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                tension: 0.3
            }
        ]
    };

    const hokushinConfig = {
        type: 'line',
        data: hokushinData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: '{{ $student->last_name }} {{ $student->first_name }} さんの実力テスト点数推移'
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    title: {
                        display: true,
                        text: '点数'
                    }
                }
            }
        },
    };

    const myHokushinChart = new Chart(
        document.getElementById('hokushinChart'),
        hokushinConfig
    );
</script>
