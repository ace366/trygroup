<x-app-layout>
    <div class="p-6 max-w-3xl mx-auto">
        <h2 class="text-xl font-bold mb-4">模試結果 編集</h2>

        <form method="POST" action="{{ route('mock_tests.update', $mockTest) }}">
            @csrf
            @method('PUT')

            @include('mock_tests.form', ['mockTest' => $mockTest])

            <div class="mt-4">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    更新
                </button>
                <a href="{{ route('mock_tests.index') }}" class="ml-4 text-gray-600 hover:underline">戻る</a>
            </div>
        </form>
    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>