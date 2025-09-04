<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの志望校一覧</h2>

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
                @if ($aspiration)
                    <a href="{{ route('aspirations.edit', [$student->id, $aspiration->id]) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        ✏️ 志望校を編集
                    </a>
                @else
                    <a href="{{ route('aspirations.create', $student->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        ➕ 志望校を登録
                    </a>
                @endif
            @else
                <button class="bg-gray-300 text-gray-600 px-4 py-2 rounded cursor-not-allowed" disabled>
                    {{ $aspiration ? '✏️ 志望校を編集（権限なし）' : '➕ 志望校を登録（権限なし）' }}
                </button>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($aspiration)
            <div class="bg-white shadow-md rounded p-6 space-y-4">
                <div><strong>第1志望:</strong> {{ $aspiration->first_choice }}</div>
                @if($aspiration->second_choice)
                    <div><strong>第2志望:</strong> {{ $aspiration->second_choice }}</div>
                @endif
                @if($aspiration->third_choice)
                    <div><strong>第3志望:</strong> {{ $aspiration->third_choice }}</div>
                @endif
                @if($aspiration->fourth_choice)
                    <div><strong>第4志望:</strong> {{ $aspiration->fourth_choice }}</div>
                @endif
            </div>
        @else
            <div class="text-gray-500 text-center">
                志望校データがまだ登録されていません
            </div>
        @endif

    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>