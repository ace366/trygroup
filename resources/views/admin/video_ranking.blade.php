<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">? ��İ��󥭥� TOP10</h2>

        <table class="table-auto w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">���</th>
                    <th class="border px-3 py-2">ư�西���ȥ�</th>
                    <th class="border px-3 py-2">����</th>
                    <th class="border px-3 py-2">��׻�İ�ÿ�</th>
                    <th class="border px-3 py-2">��İ�Ϳ�</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ranking as $i => $item)
                    <tr>
                        <td class="border px-3 py-2 text-center">{{ $i + 1 }}</td>
                        <td class="border px-3 py-2">{{ $item->video->unit }}</td>
                        <td class="border px-3 py-2">{{ $item->video->subject }}</td>
                        <td class="border px-3 py-2">{{ $item->total_seconds }} ��</td>
                        <td class="border px-3 py-2">{{ $item->view_count }} ��</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
