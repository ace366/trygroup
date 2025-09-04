<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">LINE メッセージ送信</h1>

        @if(session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-200 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-red-800 bg-red-200 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('line.send') }}" class="bg-white p-6 rounded-lg shadow-md max-w-xl mx-auto">
            @csrf

            <!-- 学年選択 -->
            <label class="block font-semibold text-gray-700">学年</label>
            <select name="grade" class="w-full p-2 border rounded-md mt-2">
                <option value="1年生">1年生</option>
                <option value="2年生">2年生</option>
                <option value="3年生">3年生</option>
            </select>

            <!-- メッセージ入力 -->
            <label class="block font-semibold text-gray-700 mt-4">メッセージ</label>
            <textarea name="message" class="w-full p-2 border rounded-md mt-2" rows="4"></textarea>

            <!-- 送信ボタン -->
            <button type="submit" class="w-full bg-blue-500 text-white font-semibold py-2 rounded-md hover:bg-blue-600 transition mt-4">
                送信
            </button>
        </form>
    </div>
    <!-- Tailwind CDN（開発用） -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
</x-app-layout>
