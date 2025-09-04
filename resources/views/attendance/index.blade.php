<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">出席簿</h1>

        <!-- フィルタリングフォーム -->
        <form method="GET" action="{{ route('attendance.index') }}" class="mb-6 flex flex-wrap justify-center items-center gap-4">
            <!-- 日付フィルタ -->
            <div>
                <label for="date" class="font-semibold text-gray-700">日付:</label>
                <input type="date" id="date" name="date" value="{{ request('date') }}" class="border-gray-300 rounded-md p-2">
            </div>

            <!-- 出席タイプフィルタ -->
            <div>
                <label for="attendance_type" class="font-semibold text-gray-700">出席タイプ:</label>
                <select name="attendance_type" id="attendance_type" class="border-gray-300 rounded-md p-2">
                    <option value="">すべて</option>
                    <option value="online" {{ request('attendance_type') == 'online' ? 'selected' : '' }}>オンライン</option>
                    <option value="physical" {{ request('attendance_type') == 'physical' ? 'selected' : '' }}>対面</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md shadow-md hover:bg-blue-600 transition">
                フィルタを適用
            </button>
        </form>
        <!-- 一覧表をエクスポートして表示 -->
        <!-- Excelダウンロード＆分析ボタン -->
        <div class="flex flex-wrap justify-center items-center gap-4 mt-4">
            <form action="{{ route('attendance.download') }}" method="GET">
                <input type="hidden" name="date" value="{{ request('date') }}">
                <input type="hidden" name="attendance_type" value="{{ request('attendance_type') }}">
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    📥 Excelをダウンロード
                </button>
            </form>

            <a href="{{ route('attendance.analysis') }}"
            class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                📊 分析
            </a>
        </div>

        <div class="flex flex-col items-center mt-8 space-y-4">


            <p class="text-sm text-gray-600">※ Excel をダウンロード後、手動で添付して送信してください。</p>

        </div>
        <!-- 出席簿一覧 -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2">日付</th>
                        <th class="border border-gray-300 px-4 py-2">氏名</th>
                        <th class="border border-gray-300 px-4 py-2">学校</th>
                        <th class="border border-gray-300 px-4 py-2">学年</th>
                        <th class="border border-gray-300 px-4 py-2">クラス</th> <!-- クラスを追加 -->
                        <th class="border border-gray-300 px-4 py-2">英検</th>
                        <th class="border border-gray-300 px-4 py-2">出席タイプ</th>
                        <th class="border border-gray-300 px-4 py-2">入室時間</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $attendance)
                        <tr class="text-center">
                            <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($attendance->attendance_time)->format('Y-m-d') }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $attendance->last_name }} {{ $attendance->first_name }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $attendance->school }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $attendance->grade }} </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $attendance->class ?? '未設定' }}</td> <!-- クラス表示 -->
                            <td class="border border-gray-300 px-4 py-2">{{ $attendance->eiken }} </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="px-3 py-1 rounded-md text-white 
                                    {{ $attendance->attendance_type == 'online' ? 'bg-green-500' : 'bg-blue-500' }}">
                                    {{ $attendance->attendance_type == 'online' ? 'オンライン' : '対面' }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ \Carbon\Carbon::parse($attendance->attendance_time)->format('H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">出席記録がありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>

