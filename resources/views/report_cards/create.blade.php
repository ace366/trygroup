<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの内申点登録</h2>

        <form action="{{ route('report-cards.store', $student->id) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block mb-1 font-semibold">学年</label>
                <select name="grade" class="w-full border rounded px-3 py-2">
                    @for($i = 1; $i <= 3; $i++)
                        <option value="{{ $i }}" {{ old('grade') == $i ? 'selected' : '' }}>{{ $i }}年生</option>
                    @endfor
                </select>
                @error('grade') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-semibold">学期</label>
                <select name="semester" class="w-full border rounded px-3 py-2">
                    <option value="1" {{ old('semester') == 1 ? 'selected' : '' }}>1学期</option>
                    <option value="2" {{ old('semester') == 2 ? 'selected' : '' }}>2学期</option>
                    <option value="3" {{ old('semester') == 3 ? 'selected' : '' }}>3学期</option>
                    <option value="4" {{ old('semester') == 4 ? 'selected' : '' }}>学年</option>
                </select>
                @error('semester') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            @foreach([
                'japanese' => '国語', 
                'math' => '数学', 
                'english' => '英語', 
                'science' => '理科', 
                'social' => '社会',
                'pe' => '体育',
                'music' => '音楽',
                'home_economics' => '家庭科',
                'technology' => '技術'
            ] as $field => $label)
                <div>
                    <label class="block mb-1 font-semibold">{{ $label }}（5段階評価）</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field) }}" class="w-full border rounded px-3 py-2" min="1" max="5">
                    @error($field) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                    保存する
                </button>
                <a href="{{ route('report-cards.index', $student->id) }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">
                    戻る
                </a>
            </div>
        </form>

    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>