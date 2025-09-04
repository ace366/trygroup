<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">スケジュール編集</h2>

        <form action="{{ route('schedules.update', $schedule) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-bold">日付:</label>
                <input type="date" name="date" value="{{ $schedule->date }}" required class="p-2 border rounded w-full">
            </div>

            <div class="mb-4">
                <label class="block font-bold">イベント:</label>
                <input type="text" name="event" value="{{ $schedule->event }}" required class="p-2 border rounded w-full">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">
                更新する
            </button>
        </form>
    </div>
</x-app-layout>
