<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        <h2 class="text-2xl font-bold mb-6">{{ $student->last_name }} {{ $student->first_name }} さんの志望校登録</h2>

        <form action="{{ route('aspirations.store', $student->id) }}" method="POST" class="space-y-6">
            @csrf

            <div>

                <label class="block mb-1 font-semibold">第1志望</label>
                <input type="text" name="first_choice" id="first_choice"
                    class="w-full border rounded px-3 py-2" list="first_choice_list" autocomplete="off">
                <datalist id="first_choice_list"></datalist>

                @error('first_choice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-semibold">第2志望（任意）</label>
                <input type="text" name="second_choice" id="second_choice"
                    class="w-full border rounded px-3 py-2" list="second_choice_list" autocomplete="off">
                <datalist id="second_choice_list"></datalist>
                @error('second_choice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-semibold">第3志望（任意）</label>
                <input type="text" name="third_choice" id="third_choice"
                    class="w-full border rounded px-3 py-2" list="third_choice_list" autocomplete="off">
                <datalist id="third_choice_list"></datalist>
                @error('third_choice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-semibold">第4志望（任意）県外など候補にない学校はここに入力</label>
                <input type="text" name="fourth_choice" id="fourth_choice"
                    class="w-full border rounded px-3 py-2" autocomplete="off">
                @error('fourth_choice') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                    保存する
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
    const BASE_URL = "{{ config('app.url') }}";
    const fields = ['first_choice', 'second_choice', 'third_choice'];

    fields.forEach(field => {
        const input = document.getElementById(field);
        const datalist = document.getElementById(`${field}_list`);
        let currentOptions = [];
        let selectedFromList = false;

        input.addEventListener('input', async function () {
            const keyword = this.value;
            if (keyword.length < 1) return;

            try {
                const res = await fetch(`${BASE_URL}/api/highschools?query=${encodeURIComponent(keyword)}`);
                const data = await res.json();
                datalist.innerHTML = '';
                currentOptions = [];

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.label;
                    datalist.appendChild(option);
                    currentOptions.push(item.label);
                });
            } catch (err) {
                console.error('取得エラー:', err);
            }
        });

        input.addEventListener('mousedown', () => {
            selectedFromList = true;
        });

        input.addEventListener('blur', function () {
            const entered = this.value.trim();
            if (entered === '') return;

            setTimeout(() => {
                if (selectedFromList) {
                    selectedFromList = false;
                    return;
                }
                if (!currentOptions.includes(entered)) {
                    alert("リストから選択してください");
                    this.value = '';
                }
            }, 200);
        });
    });
});
</script>