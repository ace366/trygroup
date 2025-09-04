<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの成績メニュー</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <a href="{{ route('regular-tests.index', $student->id) }}" class="block p-6 bg-blue-100 rounded shadow hover:bg-blue-200 text-center font-bold">
                📚 定期テスト成績
            </a>

            <a href="{{ route('hokushin-tests.index', $student->id) }}" class="block p-6 bg-green-100 rounded shadow hover:bg-green-200 text-center font-bold">
                📖 実力テスト成績
            </a>

            <a href="{{ route('report-cards.index', $student->id) }}" class="block p-6 bg-yellow-100 rounded shadow hover:bg-yellow-200 text-center font-bold">
                📝 内申点（通知表）
            </a>

            <a href="{{ route('aspirations.index', $student->id) }}" class="block p-6 bg-pink-100 rounded shadow hover:bg-pink-200 text-center font-bold">
                🎯 志望校
            </a>

        </div>

    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>