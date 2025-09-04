<x-app-layout>
    <div class="container mx-auto px-4 py-6 max-w-2xl">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの模試成績 新規登録</h2>

        <form method="POST" action="{{ route('students.mock_tests.store', ['student' => $student->id]) }}" class="space-y-6">
            @csrf

            <!-- 学年 -->
            <div>
                <label class="block mb-1 font-semibold">学年</label>
                <select name="grade" class="w-full border rounded px-3 py-2">
                    @for($i = 1; $i <= 3; $i++)
                        <option value="{{ $i }}" {{ old('grade') == $i ? 'selected' : '' }}>{{ $i }}年生</option>
                    @endfor
                </select>
                @error('grade') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- 回数 -->
            <div>
                <label class="block mb-1 font-semibold">回数</label>
                <select name="exam_number" class="w-full border rounded px-3 py-2">
                    @for($i = 1; $i <= 3; $i++)
                        <option value="{{ $i }}" {{ old('exam_number') == $i ? 'selected' : '' }}>{{ $i }}回</option>
                    @endfor
                </select>
                @error('exam_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- 各教科点数 -->
            @foreach(['japanese' => '国語', 'math' => '数学', 'english' => '英語'] as $field => $label)
                <div>
                    <label class="block mb-1 font-semibold">{{ $label }}点数</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field) }}" class="w-full border rounded px-3 py-2" min="0" max="100">
                    @error($field) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <!-- 各教科偏差値（任意） -->
            @foreach(['japanese_deviation' => '国語偏差値', 'math_deviation' => '数学偏差値', 'english_deviation' => '英語偏差値'] as $field => $label)
                <div>
                    <label class="block mb-1 font-semibold">{{ $label }}（任意）</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field) }}" class="w-full border rounded px-3 py-2" min="20" max="80" step="0.1">
                    @error($field) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <!-- 合計点/偏差値（任意） -->
            @foreach([
                'three_subjects_total' => '3教科合計点',
                'three_subjects_deviation' => '3教科偏差値',
                'five_subjects_total' => '5教科合計点',
                'five_subjects_deviation' => '5教科偏差値',
            ] as $field => $label)
                <div>
                    <label class="block mb-1 font-semibold">{{ $label }}（任意）</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field) }}" class="w-full border rounded px-3 py-2" step="1">
                    @error($field) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <!-- ボタン -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded hover:bg-blue-700">
                    登録
                </button>
                <a href="{{ route('students.mock_tests.index', $student->id) }}"
                class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">
                    戻る
                </a>
            </div>

        </form>
    </div>
</x-app-layout>

<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
<!-- 合計自動計算スクリプト -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fields = {
            japanese: 0,
            math: 0,
            english: 0,
        };

        const fiveFields = {
            ...fields,
            science: 0,
            social: 0,
        };

        const updateTotals = () => {
            let threeSum = 0;
            let fiveSum = 0;

            for (let key in fields) {
                const val = parseInt(document.querySelector(`input[name="${key}"]`)?.value || 0);
                fields[key] = val;
                threeSum += val;
            }

            for (let key in fiveFields) {
                const val = parseInt(document.querySelector(`input[name="${key}"]`)?.value || 0);
                fiveFields[key] = val;
                fiveSum += val;
            }

            // 値をセット
            document.querySelector('input[name="three_subjects_total"]').value = threeSum;
            document.querySelector('input[name="five_subjects_total"]').value = fiveSum;
        };

        // 各フィールドにリスナー追加
        Object.keys(fiveFields).forEach(key => {
            const input = document.querySelector(`input[name="${key}"]`);
            if (input) {
                input.addEventListener('input', updateTotals);
            }
        });

        updateTotals(); // 初回計算
    });
</script>
