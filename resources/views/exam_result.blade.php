<x-app-layout>
    <div class="container mx-auto px-4 py-6 space-y-6">

        <!-- タイトル -->
        <h2 class="text-3xl font-bold text-center mb-4 text-indigo-700">判定結果</h2>

        <!-- ChatGPTの回答 -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-400 shadow rounded p-6">
            <h3 class="text-xl font-semibold mb-3 flex items-center">
                <span class="text-indigo-600 mr-2">💡</span> AIからのアドバイス
            </h3>
            <p class="whitespace-pre-line text-gray-800 leading-relaxed">{{ $chatgpt_answer }}</p>
        </div>

        <!-- 集計結果 -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-700">📊 集計データ</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded shadow">
                    <p class="text-sm text-gray-500">件数</p>
                    <p class="text-lg font-bold">{{ $summary['count'] }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded shadow">
                    <p class="text-sm text-gray-500">平均開示点</p>
                    <p class="text-lg font-bold">{{ round($summary['avg_score'], 1) }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded shadow">
                    <p class="text-sm text-gray-500">平均内申</p>
                    <p class="text-lg font-bold">
                        1年: {{ round($summary['avg_naishin']['1年'],1) }},
                        2年: {{ round($summary['avg_naishin']['2年'],1) }},
                        3年: {{ round($summary['avg_naishin']['3年'],1) }}
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded shadow">
                    <p class="text-sm text-gray-500">合格率</p>
                    <p class="text-lg font-bold text-green-600">{{ $summary['passed_ratio'] }} %</p>
                </div>
                <div class="bg-gray-50 p-4 rounded shadow col-span-1 md:col-span-2">
                    <p class="text-sm text-gray-500">検定保持者</p>
                    <p class="text-lg font-bold">
                        英検: {{ $summary['検定']['英検あり'] }},
                        漢検: {{ $summary['検定']['漢検あり'] }},
                        数検: {{ $summary['検定']['数検あり'] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- 不合格者の開示点数一覧 -->
        @if(!empty($summary['failed_scores']))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded shadow">
                <h3 class="font-semibold mb-2">⚠ 注意</h3>
                <p class="mb-1">過去の不合格者の開示点数は以下の通りです：</p>
                <p class="font-bold">{{ implode('点, ', $summary['failed_scores']) }}点</p>
                <p class="mt-2 text-sm">これらを下回ると合格が難しい可能性があります。</p>
            </div>
        @endif

        <!-- アクションボタン -->
        <div class="flex flex-col md:flex-row justify-center gap-4 mt-6">
            <a href="{{ route('exam.form') }}"
               class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold px-6 py-3 rounded shadow text-center transition">
                ✍ もう一度入力して判定する
            </a>
            <a href="{{ url('/') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-6 py-3 rounded shadow text-center transition">
                🏠 ホームに戻る
            </a>
        </div>

    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>