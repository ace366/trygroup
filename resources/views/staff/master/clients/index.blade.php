{{-- resources/views/staff/master/clients/index.blade.php --}}
<x-staff-layout>
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">受託元一覧</h1>

            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('staff.master.clients') }}" class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ $q }}"
                           class="w-64 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="受託元名で検索">
                    <button type="submit"
                            class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                        検索
                    </button>
                </form>

                <button type="button" id="openCreate"
                        class="px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                    新規作成
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        {{-- 一覧表 --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3">受託元コード</th>
                        <th class="px-4 py-3">受託元名</th>
                        <th class="px-4 py-3">拠点</th>
                        <th class="px-4 py-3 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($clients as $c)
                        <tr class="text-sm text-gray-700">
                            <td class="px-4 py-2 font-mono">{{ $c->client_code }}</td>
                            <td class="px-4 py-2">{{ $c->client_name }}</td>
                            <td class="px-4 py-2">
                                @if($c->base)
                                    [{{ $c->base->base_code }}] {{ $c->base->base_name }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{-- 追って編集/削除を実装予定 --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">データがありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    </div>

    {{-- 新規作成モーダル --}}
    <div id="modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>
        <div class="relative mx-auto mt-20 w-full max-w-2xl">
            <div class="bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">新しい受託元を作成</h2>
                    <button id="closeCreate" class="text-gray-400 hover:text-gray-600" aria-label="閉じる">
                        ✕
                    </button>
                </div>

                <form method="POST" action="{{ route('staff.master.clients.store') }}" class="px-6 py-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- 受託元コード（自動採番・入力不可） --}}
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">受託元コード（※自動採番）</label>
                            <input type="text" value="自動採番" disabled
                                   class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-500">
                        </div>

                        {{-- 受託元名 --}}
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">受託元名 <span class="text-red-500">*</span></label>
                            <input type="text" name="client_name" value="{{ old('client_name') }}" required
                                   maxlength="100"
                                   class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('client_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 拠点 --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-600 mb-1">拠点 <span class="text-red-500">*</span></label>
                            <select name="base_id" required
                                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">選択してください</option>
                                @foreach($bases as $b)
                                    <option value="{{ $b->id }}"
                                        @selected(old('base_id') == $b->id)>
                                        [{{ $b->base_code }}] {{ $b->base_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('base_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if($bases->isEmpty())
                                <p class="mt-2 text-sm text-amber-600">
                                    拠点が未登録です。先に「システム系マスタ」で拠点（client_bases）を登録してください。
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                        <button type="button" id="closeCreate2"
                                class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                            キャンセル
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700"
                                @if($bases->isEmpty()) disabled @endif>
                            保存
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const modal = document.getElementById('modal');
            const openBtn = document.getElementById('openCreate');
            const closeBtn = document.getElementById('closeCreate');
            const closeBtn2 = document.getElementById('closeCreate2');

            function open()  { modal.classList.remove('hidden'); modal.setAttribute('aria-hidden', 'false'); }
            function close() { modal.classList.add('hidden');    modal.setAttribute('aria-hidden', 'true');  }

            if (openBtn)  openBtn.addEventListener('click', open);
            if (closeBtn) closeBtn.addEventListener('click', close);
            if (closeBtn2) closeBtn2.addEventListener('click', close);
            modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        })();
    </script>
    @endpush
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
</x-staff-layout>