<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">年間スケジュール管理</h2>

        @if(auth()->user()->role !== 'user')
            <a href="{{ route('schedules.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">
                スケジュールを追加
            </a>
        @endif

        <table class="mt-4 w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">日付</th>
                    <th class="border px-4 py-2">イベント</th>
                    @if(auth()->user()->role !== 'user')
                        <th class="border px-4 py-2">操作</th> <!-- 👈 ここも消える -->
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($schedules as $schedule)
                    <tr>
                        <td class="border px-4 py-2">
                            @php
                                $carbonDate = \Carbon\Carbon::parse($schedule->date);
                                $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
                                $weekday = $weekdays[$carbonDate->dayOfWeek];
                            @endphp
                            {{ $carbonDate->format('m月d日') }}（{{ $weekday }}）
                        </td>
                        <td class="border px-4 py-2">{{ $schedule->event }}</td>

                        @if(auth()->user()->role !== 'user')
                            <td class="border px-4 py-2">
                                <a href="{{ route('schedules.edit', $schedule) }}" class="text-blue-500 hover:underline">編集</a>
                                <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('削除しますか？');">削除</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>   
        </table>
    </div>
</x-app-layout>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
