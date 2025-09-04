<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">📜 視聴履歴</h2>

        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">動画タイトル</th>
                    <th class="px-4 py-2 border">教科</th>
                    <th class="px-4 py-2 border">学年</th>
                    <th class="px-4 py-2 border">視聴秒数</th>
                    <th class="px-4 py-2 border">最終視聴日時</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($histories as $history)
                    <tr>
                        <td class="border px-4 py-2">{{ $history->video->unit }}</td>
                        <td class="border px-4 py-2">{{ $history->video->subject }}</td>
                        <td class="border px-4 py-2">{{ $history->video->grade }}</td>
                        <td class="border px-4 py-2">{{ $history->watched_seconds }} 秒</td>
                        <td class="border px-4 py-2">{{ $history->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">視聴履歴がありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
