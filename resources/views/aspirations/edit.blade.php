<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの志望校編集</h2>

        <form action="{{ route('aspirations.update', [$student->id, $aspiration->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 font-semibold">第1志望（必須）</label>
                <input type="text" name="first_choice" id="first_choice" list="first_choice_list"
                    value="{{ old('first_choice', $aspiration->first_choice) }}" class="w-full border rounded px-3 py-2" required>
                <datalist id="first_choice_list"></datalist>
                @error('first_choice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-semibold">第2志望（任意）</label>
                <input type="text" name="second_choice" id="second_choice" list="second_choice_list"
                    value="{{ old('second_choice', $aspiration->second_choice) }}" class="w-full border rounded px-3 py-2">
                <datalist id="second_choice_list"></datalist>
                @error('second_choice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-semibold">第3志望（任意）</label>
                <input type="text" name="third_choice" id="third_choice" list="third_choice_list"
                    value="{{ old('third_choice', $aspiration->third_choice) }}" class="w-full border rounded px-3 py-2">
                <datalist id="third_choice_list"></datalist>
                @error('third_choice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-semibold">第4志望（県外の高校はこちらに入力）</label>
                <input type="text" name="fourth_choice" value="{{ old('fourth_choice', $aspiration->fourth_choice) }}" class="w-full border rounded px-3 py-2">
                @error('fourth_choice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded">
                    更新する
                </button>
                <a href="{{ route('aspirations.index', $student->id) }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">
                    戻る
                </a>
            </div>
        </form>

    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fields = ['first_choice', 'second_choice', 'third_choice'];

    fields.forEach(field => {
        const input = document.getElementById(field);
        const datalist = document.getElementById(`${field}_list`);

        input.addEventListener('input', async function () {
            const keyword = this.value;
            if (keyword.length < 1) return;

            try {
                const res = await fetch(`/r7yorii_ts/laravel/public/api/highschools?query=${encodeURIComponent(keyword)}`);
                const data = await res.json();
                datalist.innerHTML = '';

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.label;
                    datalist.appendChild(option);
                });
            } catch (err) {
                console.error('取得エラー:', err);
            }
        });

        input.addEventListener('change', function () {
            const entered = this.value;
            const options = Array.from(datalist.options);
            const isMatch = options.some(opt => opt.value === entered);

            if (!isMatch && entered !== '') {
                alert("リストから選択してください");
                this.value = '';
            }
        });
    });
});
</script>