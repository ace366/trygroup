<x-app-layout>
    <div class="max-w-2xl mx-auto py-8 px-4">

        <!-- タイトル -->
        <h2 class="text-3xl font-bold text-center text-indigo-600 mb-6">
            🏫 高校入試 判定フォーム
        </h2>
        <p class="text-center text-gray-600 mb-8">
            志望校や北辰テストの最新データが自動で入力されます。<br>
            必要に応じて修正してください。
        </p>

        <!-- 入力フォーム -->
        <form method="POST" action="{{ route('exam.analyze') }}" class="space-y-6 bg-white shadow-lg rounded-lg p-6">
            @csrf

            <!-- 学校名 -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">🏫 学校名</label>
                <input type="text" name="school"
                       value="{{ old('school', $preFill['school'] ?? '') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring focus:ring-indigo-200" required>
            </div>

            <!-- 学科 -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">📘 学科</label>
                <input type="text" name="department"
                       value="{{ old('department', $preFill['department']) }}"
                       class="w-full rounded-lg border-gray-300 focus:ring focus:ring-indigo-200" required>
            </div>

            <!-- 科目点数 -->
            <h3 class="text-lg font-bold text-indigo-500 mt-4">✍ 各科目の点数 (北辰テスト最新の情報)</h3>
            <div class="grid grid-cols-2 gap-4">
                <div><label>国語</label><input type="number" name="japanese" min="0" max="100" value="{{ old('japanese', $preFill['japanese']) }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label>数学</label><input type="number" name="math" min="0" max="100" value="{{ old('math', $preFill['math']) }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label>英語</label><input type="number" name="english" min="0" max="100" value="{{ old('english', $preFill['english']) }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label>理科</label><input type="number" name="science" min="0" max="100" value="{{ old('science', $preFill['science']) }}" class="w-full rounded-lg border-gray-300"></div>
                <div><label>社会</label><input type="number" name="social" min="0" max="100" value="{{ old('social', $preFill['social']) }}" class="w-full rounded-lg border-gray-300"></div>
            </div>

            <!-- 内申点 -->
            <h3 class="text-lg font-bold text-indigo-500 mt-4">📖 内申点 (9科合計)</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label>1年</label>
                    <input type="number" name="naishin_1" min="0" max="45"
                           value="{{ old('naishin_1', $preFill['naishin_1']) }}"
                           class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label>2年</label>
                    <input type="number" name="naishin_2" min="0" max="45"
                           value="{{ old('naishin_2', $preFill['naishin_2']) }}"
                           class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label>3年</label>
                    <input type="number" name="naishin_3" min="0" max="45"
                           value="{{ old('naishin_3', $preFill['naishin_3']) }}"
                           class="w-full rounded-lg border-gray-300">
                </div>
            </div>

            <!-- 判定ボタン -->
            <div class="text-center mt-6">
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg text-xl transition">
                    🚀 判定する
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
