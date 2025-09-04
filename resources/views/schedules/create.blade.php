<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">スケジュール登録</h2>

        <form action="{{ route('schedules.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block font-bold">日付:</label>
                <input type="date" name="date" required class="p-2 border rounded w-full">
            </div>

            <div class="mb-4">
                <label class="block font-bold">イベント選択:</label>
                <div class="flex space-x-4">
                    <label><input type="checkbox" class="event-checkbox" value="よりE土曜塾"> よりE土曜塾</label>
                    <label><input type="checkbox" class="event-checkbox" value="英検対策講座"> 英検対策講座</label>
                    <label><input type="checkbox" class="event-checkbox" value="理解度確認テスト"> 理解度確認テスト</label>
                    <label><input type="checkbox" class="event-checkbox" value="講演会"> 講演会</label>
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-bold">イベント:</label>
                <input type="text" id="event-input" name="event" required class="p-2 border rounded w-full">
            </div>

            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-700">
                登録する
            </button>
        </form>
    </div>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.event-checkbox');
            const eventInput = document.getElementById('event-input');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    let selectedEvents = [];
                    checkboxes.forEach(cb => {
                        if (cb.checked) {
                            selectedEvents.push(cb.value);
                        }
                    });
                    eventInput.value = selectedEvents.join(', '); // カンマ区切りで表示
                });
            });
        });
    </script>
</x-app-layout>
