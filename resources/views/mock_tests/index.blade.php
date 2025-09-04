<x-app-layout>
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">模試一覧</h2>
        <a href="{{ route('students.mock_tests.create', ['student' => $student->id]) }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">新規登録</a>

        @foreach($mockTests as $mock)
            <div class="border p-4 mb-2">
                <div>{{ $mock->student->last_name }} {{ $mock->student->first_name }} - 第{{ $mock->exam_number }}回</div>
                <div>国語: {{ $mock->japanese }} / 数学: {{ $mock->math }} / 英語: {{ $mock->english }}</div>
                <div class="mt-2">
                    <a href="{{ route('mock_tests.edit', $mock) }}" class="text-blue-600 underline">編集</a>
                    <form action="{{ route('mock_tests.destroy', $mock) }}" method="POST" class="inline-block" onsubmit="return confirm('削除しますか？');">
                        @csrf @method('DELETE')
                        <button class="text-red-500 ml-2">削除</button>
                    </form>
                </div>
            </div>
        @endforeach

        {{ $mockTests->links() }}
    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>