{{-- resources/views/staff/projects/index.blade.php --}}
<x-staff-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- 見出し行：右側に「新規作成」ボタン --}}
        <h1 class="text-2xl font-bold mb-4 flex items-center justify-between">
            <span>事業一覧</span>
            <button type="button" id="open-create-modal"
                    class="inline-flex items-center px-2 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                新規作成
            </button>
        </h1>

        {{-- 追加：登録完了メッセージ --}}
        @if(session('status'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 text-green-800 px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        {{-- フィルタ／検索 --}}
        <form method="GET" action="{{ route('staff.projects.index') }}"
              class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- 事業名・受託元・外部コード 検索 --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">事業名検索</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ e($filters['q']) }}"
                               placeholder="事業名／受託元／外部コードを検索"
                               class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit"
                                class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1 text-sm bg-indigo-600 text-white rounded">
                            検索
                        </button>
                    </div>
                </div>

                {{-- 年度 --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">年度</label>
                    <select name="fiscal_year"
                            class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">すべて</option>
                        @foreach($fiscalYears as $fy)
                            <option value="{{ $fy->id }}"
                                @selected(
                                    (string)$fy->id === (string)($filters['fiscal_year'] ?? $currentFiscalYearId)
                                )>
                                {{ $fy->year }}（{{ $fy->start_date->format('Y/m/d') }}〜{{ $fy->end_date->format('Y/m/d') }}）
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 期間（yyyymmdd） --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">期間（契約期間と交差）</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="date_from" inputmode="numeric" pattern="\d{8}"
                            placeholder="YYYYMMDD"
                            value="{{ e($filters['date_from'] ?? $currentFiscalYearStart) }}"
                            class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="text-gray-500">〜</span>
                        <input type="text" name="date_to" inputmode="numeric" pattern="\d{8}"
                            placeholder="YYYYMMDD"
                            value="{{ e($filters['date_to'] ?? $currentFiscalYearEnd) }}"
                            class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">例）20250401 〜 20260331</p>
                </div>

                {{-- 事業タイプ --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">事業タイプ</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="type[]" value="venue"
                                @checked(in_array('venue', $filters['type'] ?? ['venue']))>
                            <span>会場型</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="type[]" value="individual"
                                @checked(in_array('individual', $filters['type']))>
                            <span>個別対応型</span>
                        </label>

                        <div class="ml-auto flex items-center gap-2">
                            <label class="text-sm text-gray-600">表示件数</label>
                            <select name="per_page"
                                    class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach([10,20,50,100] as $pp)
                                    <option value="{{ $pp }}" @selected($filters['per_page']===$pp)>{{ $pp }}</option>
                                @endforeach
                            </select>

                            <label class="text-sm text-gray-600 ml-3">並び順</label>
                            <select name="sort"
                                    class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="recent" @selected($filters['sort']==='recent')>契約開始（新しい順）</option>
                                <option value="oldest" @selected($filters['sort']==='oldest')>契約開始（古い順）</option>
                                <option value="name_asc" @selected($filters['sort']==='name_asc')>事業名（昇順）</option>
                                <option value="name_desc" @selected($filters['sort']==='name_desc')>事業名（降順）</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 flex items-end gap-3">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        この条件で検索
                    </button>
                    <a href="{{ route('staff.projects.index') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        条件をクリア
                    </a>
                </div>
            </div>
        </form>

        {{-- 一覧テーブル --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">事業</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">外部コード</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">契約期間</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">受託元</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">公開状況</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">事業タイプ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($projects as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ e($p->name) }}</div>
                                    <div class="text-xs text-gray-500">
                                        年度：{{ $p->fiscalYear?->year ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ e($p->external_code ?? '-') }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                    {{ $p->contract_start?->format('Y/m/d') }} 〜 {{ $p->contract_end?->format('Y/m/d') }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ e($p->client?->client_name ?? '-') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($p->is_published)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">公開</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">非公開</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($p->type === 'venue')
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">会場型</span>
                                    @elseif($p->type === 'individual')
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">個別対応型</span>
                                    @else
                                        <span class="text-xs text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                    条件に合致する事業がありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ページネーション --}}
            <div class="px-4 py-3 bg-gray-50 border-t">
                {{ $projects->onEachSide(1)->links() }}
            </div>
        </div>
    </div>

    <!-- Tailwind CDN（開発用） -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>

    {{-- 新規作成モーダル：開閉 --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal   = document.getElementById('create-project-modal');
        const openBtn = document.getElementById('open-create-modal');
        const closeBtn= document.getElementById('close-create-modal');
        const cancel  = document.getElementById('cancel-create');

        if (!modal || !openBtn) return;

        const open = () => {
            // 表示&中央寄せのため flex を付与
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };
        const close = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        openBtn.addEventListener('click', open);
        closeBtn?.addEventListener('click', close);
        cancel?.addEventListener('click', close);

        // 背景クリックで閉じる
        modal.addEventListener('click', (e) => {
            if (e.target.id === 'create-project-modal') {
                close();
            }
        });
    });
    </script>

    {{-- 受託元モーダル：検索/ページング/選択 --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const clientModal = document.getElementById('client-modal');
    const createModal = document.getElementById('create-project-modal');
    const rows  = document.getElementById('client-rows');
    const pager = document.getElementById('client-pager');
    const total = document.getElementById('client-total');
    const qBox  = document.getElementById('client-q');
    const openBtn = document.getElementById('open-client-modal');
    const okBtn   = document.getElementById('client-ok');
    const searchBtn = document.getElementById('client-search');
    const clearBtn  = document.getElementById('client-clear');

    if (!clientModal || !rows || !pager || !total || !qBox || !openBtn || !okBtn) return;

    let selected = null;
    let currentPage = 1;
    let lastPage = 1;
    const perPage = 10;

    const open  = () => {
        if (createModal) createModal.classList.add('hidden'); // 作成モーダルを隠す
        clientModal.classList.remove('hidden');
    };
    const close = () => {
        clientModal.classList.add('hidden');
        if (createModal) {
        createModal.classList.remove('hidden'); // 作成モーダルを再表示
        createModal.classList.add('flex');
        }
    };

    openBtn.addEventListener('click', () => {
        currentPage = 1;
        qBox.value = '';
        fetchAndRender().then(open);
    });

    clientModal.addEventListener('click', (e) => {
        if (e.target.dataset.close === '1') close();
    });

    searchBtn?.addEventListener('click', () => {
        currentPage = 1;
        fetchAndRender();
    });
    clearBtn?.addEventListener('click', () => {
        qBox.value = '';
        currentPage = 1;
        fetchAndRender();
    });

    function renderRows(list) {
        rows.innerHTML = '';
        list.forEach(c => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';
        tr.innerHTML = `
            <td class="px-3 py-2">
            <input type="radio" name="client_pick" value="${c.id}">
            </td>
            <td class="px-3 py-2">
            <div class="font-medium">[${c.client_code}] ${c.client_name}</div>
            </td>
            <td class="px-3 py-2 text-sm text-gray-600">[${String(c.base_id).padStart(4,'0')}]</td>
        `;
        tr.querySelector('input[type="radio"]').addEventListener('change', () => {
            selected = { id:c.id, name:c.client_name, code:c.client_code };
        });
        rows.appendChild(tr);
        });
    }

    function renderPager() {
        pager.innerHTML = '';
        const mk = (p, label) => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'px-2 py-1 border rounded ' + (p===currentPage ? 'bg-indigo-600 text-white' : 'bg-white');
        b.textContent = label ?? p;
        b.addEventListener('click', () => {
            if (p<1 || p>lastPage || p===currentPage) return;
            currentPage = p;
            fetchAndRender();
        });
        return b;
        };
        pager.appendChild(mk(currentPage-1, '‹'));
        for (let p=1; p<=lastPage; p++) pager.appendChild(mk(p));
        pager.appendChild(mk(currentPage+1, '›'));
    }

    async function fetchAndRender() {
        rows.innerHTML = `<tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">読み込み中...</td></tr>`;
        try {
        const url = new URL(`{{ route('staff.api.clients.index') }}`);
        url.searchParams.set('page', currentPage);
        url.searchParams.set('per_page', perPage);
        if (qBox.value.trim() !== '') url.searchParams.set('q', qBox.value.trim());

        const res = await fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        renderRows(data.data ?? []);
        currentPage = data.current_page ?? 1;
        lastPage    = data.last_page ?? 1;
        total.textContent = `${data.total ?? 0} 件`;
        renderPager();
        } catch (e) {
        rows.innerHTML = `<tr><td colspan="3" class="px-3 py-4 text-center text-red-600">読み込みに失敗しました</td></tr>`;
        console.error(e);
        }
    }

    okBtn.addEventListener('click', () => {
        if (selected) {
        const idInput = document.getElementById('client_id');
        const disp    = document.getElementById('client_display');
        if (idInput && disp) {
            idInput.value = selected.id;
            disp.value = `[${selected.code}] ${selected.name}`;
        }
        }
        close(); // 選択確定後もキャンセル後も作成モーダルに戻す
    });
    });
    </script>


    {{-- 受託元 選択モーダル --}}
    <div id="client-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40" data-close="1"></div>
        <div class="relative mx-auto mt-16 w-[90%] max-w-4xl bg-white rounded-lg shadow-lg">
            <div class="flex items-center justify-between px-4 py-3 border-b">
                <h2 class="font-semibold">受託元を選択</h2>
                <button type="button" class="text-gray-500 hover:text-gray-700" data-close="1">×</button>
            </div>

            <div class="p-4">
                <div class="flex gap-2 mb-3">
                    <input id="client-q" type="text" class="flex-1 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="検索">
                    <button id="client-search" type="button" class="px-3 py-2 rounded-md bg-indigo-600 text-white">検索</button>
                    <button id="client-clear" type="button" class="px-3 py-2 rounded-md bg-gray-100">クリア</button>
                </div>

                <div class="overflow-x-auto border rounded-md">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-3 py-2 w-10"></th>
                            <th class="px-3 py-2 text-left">受託元</th>
                            <th class="px-3 py-2 text-left">拠点</th>
                        </tr>
                        </thead>
                        <tbody id="client-rows" class="divide-y"></tbody>
                    </table>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <div class="text-sm text-gray-500" id="client-total"></div>
                    <div class="flex items-center gap-1" id="client-pager"></div>
                </div>
            </div>

            <div class="flex justify-end gap-2 px-4 py-3 border-t">
                <button type="button" class="px-4 py-2 bg-gray-100 rounded-md" data-close="1">キャンセル</button>
                <button type="button" id="client-ok" class="px-4 py-2 bg-indigo-600 text-white rounded-md">OK</button>
            </div>
        </div>
    </div>

    {{-- 新規作成モーダル --}}
    <div id="create-project-modal"
         class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-3xl rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h2 class="text-lg font-semibold">新しい事業を作成</h2>
                <button type="button" id="close-create-modal" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <form method="POST" action="{{ route('staff.projects.store') }}" class="p-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- 事業名 --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">事業名 <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" required
                               value="{{ old('name') }}"
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 受託元 --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">受託元 <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <input type="text" id="client_display" class="w-full rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="（選択してください。）" readonly>
                            <input type="hidden" name="client_id" id="client_id" required>
                            <button type="button" id="open-client-modal"
                                    class="px-3 py-2 rounded-r-md border border-l-0 bg-gray-50 hover:bg-gray-100">...</button>
                        </div>
                    </div>

                    {{-- 年度 --}}
                    <div>
                        <label for="fiscal_year_id" class="block text-sm font-medium text-gray-700">年度 <span class="text-red-500">*</span></label>
                        <select id="fiscal_year_id" name="fiscal_year_id" required
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">（選択してください）</option>
                            @foreach($fiscalYears as $fy)
                                <option value="{{ $fy->id }}" @selected(old('fiscal_year_id') == $fy->id)>
                                    {{ $fy->year }}（{{ $fy->start_date->format('Y/m/d') }}〜{{ $fy->end_date->format('Y/m/d') }}）
                                </option>
                            @endforeach
                        </select>
                        @error('fiscal_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 契約開始 --}}
                    <div>
                        <label for="contract_start" class="block text-sm font-medium text-gray-700">契約年月日</label>
                        <input id="contract_start" name="contract_start" type="date"
                               value="{{ old('contract_start') }}"
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('contract_start') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 契約終了 --}}
                    <div>
                        <label for="contract_end" class="block text-sm font-medium text-gray-700">終了年月日</label>
                        <input id="contract_end" name="contract_end" type="date"
                               value="{{ old('contract_end') }}"
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('contract_end') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 種別 --}}
                    <div class="md:col-span-2">
                        <span class="block text-sm font-medium text-gray-700 mb-1">事業タイプ <span class="text-red-500">*</span></span>
                        <label class="inline-flex items-center mr-6">
                            <input type="radio" name="type" value="venue" class="mr-2" {{ old('type','venue')==='venue' ? 'checked' : '' }}>
                            会場型
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="type" value="individual" class="mr-2" {{ old('type')==='individual' ? 'checked' : '' }}>
                            個別対応型
                        </label>
                        @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 説明 --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">説明</label>
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="任意で入力">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2 border-t pt-4">
                    <button type="button" id="cancel-create"
                            class="px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">キャンセル</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">保存</button>
                </div>
            </form>
        </div>
    </div>
</x-staff-layout>
