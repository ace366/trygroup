<x-app-layout>
    <div class="max-w-3xl mx-auto bg-white p-6 mt-10 shadow rounded">
        <h2 class="text-2xl font-bold mb-6 text-center">視聴時間ランキング（上位10人）</h2>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">順位</th>
                    <th class="py-2 px-4">名前</th>
                    <th class="py-2 px-4">合計視聴時間（秒）</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ranking as $i => $user)
                    <tr class="border-b">
                        <td class="py-2 px-4">{{ $i + 1 }}</td>
                        <td class="py-2 px-4">{{ $user->last_name }} {{ $user->first_name }}</td>
                        <td class="py-2 px-4">{{ number_format($user->total_playtime ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
