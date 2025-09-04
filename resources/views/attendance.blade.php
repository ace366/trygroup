<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">出席管理</h1>
        <p class="text-lg mb-6 text-center">こんにちは、{{ auth()->user()->last_name }} さん</p>

        <!-- 出席登録フォーム -->
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4 text-center">出席を登録</h2>

            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">出席方法を選択:</label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="attendance_type" value="online" checked class="form-radio">
                        <span class="ml-2">オンライン出席</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="radio" name="attendance_type" value="physical" class="form-radio">
                        <span class="ml-2">教室で出席</span>
                    </label>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    出席を記録
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mt-4 p-4 bg-green-200 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
    </div>
</x-app-layout>
