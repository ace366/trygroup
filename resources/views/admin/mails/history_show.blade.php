<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">配信詳細</h1>

        <div class="mb-4 flex gap-2">
            <a href="{{ route('admin.mails.history') }}" class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">一覧に戻る</a>
            <a href="{{ route('admin.mails.reuse', $batch->id) }}" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">この内容で作成</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white border rounded p-4">
                <div class="text-sm text-gray-500">日時</div>
                <div class="font-medium">{{ \Illuminate\Support\Carbon::parse($batch->created_at)->format('Y-m-d H:i') }}</div>
            </div>
            <div class="bg-white border rounded p-4">
                <div class="text-sm text-gray-500">作成者</div>
                <div class="font-medium">{{ $batch->creator_name }}</div>
            </div>
            <div class="bg-white border rounded p-4">
                <div class="text-sm text-gray-500">件名</div>
                <div class="font-medium break-words">{{ $batch->subject }}</div>
            </div>
            <div class="bg-white border rounded p-4">
                <div class="text-sm text-gray-500">送信数</div>
                <div class="font-medium">
                    送信: <span class="font-mono">{{ (int)$counts->sent }}</span> /
                    合計: <span class="font-mono">{{ (int)$counts->total }}</span>
                    <span class="text-xs text-gray-500 ml-1">失敗: {{ (int)$counts->failed }}</span>
                </div>
            </div>
            <div class="bg-white border rounded p-4 md:col-span-2">
                <div class="text-sm text-gray-500">使用フィルタ</div>
                @php $filters = json_decode($batch->filters_json ?? '[]', true) ?: []; @endphp
                <div class="mt-1 text-sm">
                    <span class="inline-block mr-3">中学校: <span class="font-medium">{{ $filters['school'] ?? '' }}</span></span>
                    <span class="inline-block mr-3">学年: <span class="font-medium">{{ $filters['grade'] ?? '' }}</span></span>
                    <span class="inline-block">英検: <span class="font-medium">{{ $filters['eiken'] ?? '' }}</span></span>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="text-sm text-gray-500 mb-2">本文</div>
            <pre class="whitespace-pre-wrap break-words text-sm leading-6">{{ $batch->body_plain }}</pre>
        </div>
    </div>

    <!-- Tailwind CDN（開発用） -->
    <script src="https://cdn.tailwindcss.com"></script>
</x-app-layout>
