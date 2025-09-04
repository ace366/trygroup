<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">? �桼�����̻�İ����</h2>

        <table class="table-auto w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">�桼����̾</th>
                    <th class="border px-3 py-2">ư�西���ȥ�</th>
                    <th class="border px-3 py-2">����</th>
                    <th class="border px-3 py-2">��İ�ÿ�</th>
                    <th class="border px-3 py-2">�ǽ���İ����</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $history)
                    <tr>
                        <td class="border px-3 py-2">{{ $history->user->last_name }}{{ $history->user->first_name }}</td>
                        <td class="border px-3 py-2">{{ $history->video->unit }}</td>
                        <td class="border px-3 py-2">{{ $history->video->subject }}</td>
                        <td class="border px-3 py-2">{{ $history->watched_seconds }} ��</td>
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
