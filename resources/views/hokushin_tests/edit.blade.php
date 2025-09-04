<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの実力テスト成績編集</h2>

        <form action="{{ route('hokushin-tests.update', [$student->id, $test->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 font-semibold">学年</label>
                <select name="grade" class="w-full border rounded px-3 py-2">
                    @for($i = 1; $i <= 3; $i++)
                        <option value="{{ $i }}" {{ old('grade', $test->grade) == $i ? 'selected' : '' }}>{{ $i }}年生</option>
                    @endfor
                </select>
                @error('grade') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-semibold">回数</label>
                <select name="exam_number" class="w-full border rounded px-3 py-2">
                    @for($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ old('exam_number', $test->exam_number) == $i ? 'selected' : '' }}>{{ $i }}回</option>
                    @endfor
                </select>
                @error('exam_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            @foreach(['japanese' => '国語', 'math' => '数学', 'english' => '英語', 'science' => '理科', 'social' => '社会'] as $field => $label)
                <div>
                    <label class="block mb-1 font-semibold">{{ $label }}点数</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field, $test->$field) }}" class="w-full border rounded px-3 py-2" min="0" max="100">
                    @error($field) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            @foreach(['japanese_deviation' => '国語偏差値', 'math_deviation' => '数学偏差値', 'english_deviation' => '英語偏差値', 'science_deviation' => '理科偏差値', 'social_deviation' => '社会偏差値'] as $field => $label)
                <div>
                    <label class="block mb-1 font-semibold">{{ $label }}（任意）</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field, $test->$field) }}" class="w-full border rounded px-3 py-2" min="20" max="80" step="0.1">
                    @error($field) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <div class="flex gap-4">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded">
                    更新する
                </button>
                <a href="{{ route('hokushin-tests.index', $student->id) }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">
                    戻る
                </a>
            </div>
        </form>

    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>