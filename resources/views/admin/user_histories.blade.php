<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">? ユーザー別視聴履歴</h2>

        <table class="table-auto w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">ユーザー名</th>
                    <th class="border px-3 py-2">動画タイトル</th>
                    <th class="border px-3 py-2">教科</th>
                    <th class="border px-3 py-2">視聴秒数</th>
                    <th class="border px-3 py-2">最終視聴日時</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $history)
                    <tr>
                        <td class="border px-3 py-2">{{ $history->user->last_name }}{{ $history->user->first_name }}</td>
                        <td class="border px-3 py-2">{{ $history->video->unit }}</td>
                        <td class="border px-3 py-2">{{ $history->video->subject }}</td>
                        <td class="border px-3 py-2">{{ $history->watched_seconds }} 秒</td>
                        <td class="border px-3 py-2">{{ $history->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $histories->links() }}
        </div>
    </div>
</x-app-layout>
