<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">一括メール配信 履歴</h1>

        <div class="mb-4">
            <a href="{{ route('admin.mails.index') }}" class="text-sm px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                新規作成へ
            </a>
        </div>

        <div class="bg-white border rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-2 text-left w-40">日時</th>
                        <th class="p-2 text-left">件名</th>
                        <th class="p-2 text-left w-40">作成者</th>
                        <th class="p-2 text-right w-48">送信数</th>
                        <th class="p-2 text-center w-56">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($rows as $r)
                        @php
                            $filters = json_decode($r->filters_json ?? '[]', true) ?: [];
                            $chips = [];
                            if (!empty($filters['school'])) $chips[] = '中:'.$filters['school'];
                            if (!empty($filters['grade']))  $chips[] = '学:'.$filters['grade'];
                            if (!empty($filters['eiken']))  $chips[] = '英:'.$filters['eiken'];
                        @endphp
                        <tr>
                            <td class="p-2 align-top">{{ \Illuminate\Support\Carbon::parse($r->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="p-2 align-top">
                                <a class="text-blue-600 hover:underline" href="{{ route('admin.mails.history.show', $r->id) }}">
                                    {{ $r->subject }}
                                </a>
                                @if($chips)
                                    <div class="mt-1 text-xs text-gray-500">
                                        @foreach($chips as $c)
                                            <span class="inline-block px-2 py-0.5 bg-gray-100 rounded mr-1">{{ $c }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="p-2 align-top">{{ $r->creator_name }}</td>
                            <td class="p-2 align-top text-right">
                                <span class="font-mono">{{ (int)$r->sent }}/{{ (int)$r->total }}</span>
                                <span class="text-xs text-gray-500 ml-1">失敗: {{ (int)$r->failed }}</span>
                            </td>
                            <td class="p-2 align-top text-center">
                                <a href="{{ route('admin.mails.history.show', $r->id) }}" class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">詳細</a>
                                <a href="{{ route('admin.mails.reuse', $r->id) }}" class="ml-2 px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">この内容で作成</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="p-4 text-center text-gray-500" colspan="5">履歴がありません。</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    </div>

    <!-- Tailwind CDN（開発用） -->
    <script src="https://cdn.tailwindcss.com"></script>
</x-app-layout>
