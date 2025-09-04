<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6">生徒一覧（管理者専用）</h2>
        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('students.index', ['filter' => 'all']) }}"
            class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                全ての生徒を表示
            </a>
            <a href="{{ route('students.index', ['filter' => 'scored']) }}"
            class="px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                成績がある生徒のみ表示
            </a>
            <a href="{{ route('students.index', ['filter' => 'today']) }}"
            class="px-3 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                本日の出席者を表示
            </a>
            {{-- ▼ 置換：リンク → 「指導PDF一括」トグルボタン（他ボタンと統一デザイン） --}}
            {{-- 既存の a タグを丸ごと以下に差し替え --}}
            <div class="relative">
            <button type="button" id="pdf-bulk-btn"
                    class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                指導PDF一括
            </button>

            {{-- ▼ 追加：カレンダーポップオーバー（デフォルト非表示） --}}
            <div id="pdf-bulk-popover"
                class="hidden absolute z-50 mt-2 bg-white border rounded shadow p-3 w-72 right-0">
                <div class="flex items-center justify-between mb-2">
                    <button id="cal-prev" type="button" class="px-2 py-1 border rounded hover:bg-gray-100">＜</button>
                    <div id="cal-title" class="font-semibold"></div>
                    <button id="cal-next" type="button" class="px-2 py-1 border rounded hover:bg-gray-100">＞</button>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-xs text-gray-600 mb-1">
                    <div>日</div><div>月</div><div>火</div><div>水</div><div>木</div><div>金</div><div>土</div>
                </div>
                <div id="cal-grid" class="grid grid-cols-7 gap-1 text-sm"></div>
                <div class="mt-2 text-xs text-gray-500">● 印の日付をクリックすると一括PDFを表示</div>
            </div>
            </div>
            <form method="GET" action="{{ route('students.index') }}" class="flex gap-2 items-end">
                <input type="hidden" name="filter" value="custom">
                <div>
                    <label for="grade" class="text-sm font-semibold">学年</label>
                    <select name="grade" id="grade" class="border rounded px-2 py-1">
                        <option value="">すべて</option>
                        <option value="3年生" {{ request('grade') == '3年生' ? 'selected' : '' }}>3年生</option>
                        <option value="2年生" {{ request('grade') == '2年生' ? 'selected' : '' }}>2年生</option>
                        <option value="1年生" {{ request('grade') == '1年生' ? 'selected' : '' }}>1年生</option>
                    </select>
                </div>
                <div>
                    <label for="school" class="text-sm font-semibold">学校</label>
                    <select name="school" id="school" class="border rounded px-2 py-1">
                        <option value="">すべて</option>
                        @foreach($schoolOptions as $schoolName)
                            <option value="{{ $schoolName }}" {{ request('school') == $schoolName ? 'selected' : '' }}>
                                {{ $schoolName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">絞り込み</button>
                </div>
            </form>
        </div>
        {{-- モバイル表示（カード形式） --}}
        <div class="md:hidden space-y-4">
            @foreach($students as $student)
                @php
                    $latestRegular = $student->regularTests->sortByDesc('grade')->sortByDesc('semester')->first();
                    $latestHokushin = $student->hokushinTests->sortByDesc('grade')->sortByDesc('exam_number')->first();
                    $hasMemo = !empty(trim($student->memo ?? ''));
                @endphp
                <div class="border rounded p-4 shadow bg-white text-sm space-y-1">
                    <div><strong>氏名:</strong> {{ $student->last_name }} {{ $student->first_name }}</div>
                    <div><strong>学校:</strong> {{ $student->school }}</div>
                    <div><strong>学年:</strong> {{ $student->grade }}</div>
                    <div><strong>定期テスト:</strong> 国: {{ $latestRegular?->japanese ?? '—' }}, 数: {{ $latestRegular?->math ?? '—' }}, 英: {{ $latestRegular?->english ?? '—' }}</div>
                    <div><strong>北辰テスト:</strong> 国: {{ $latestHokushin?->japanese ?? '—' }}, 数: {{ $latestHokushin?->math ?? '—' }}, 英: {{ $latestHokushin?->english ?? '—' }}</div>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <a href="{{ route('students.dashboard', $student->id) }}" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 underline">成績</a>
                        <a href="{{ route('guidances.history', $student->id) }}" class="px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 underline">指導</a>
                        <button onclick='openMemoModal({{ $student->id }}, {!! json_encode($student->memo ?? '') !!}, "{{ route('students.memo', $student->id) }}")'
                            class="px-3 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 underline"
                            @if($hasMemo) title="メモ登録あり" @endif>
                            メモ
                            @if($hasMemo)
                                <span class="ml-1 inline-block w-2 h-2 rounded-full bg-red-500 align-middle" aria-label="メモ登録あり"></span>
                            @endif
                        </button>
                        <a href="{{ route('guidances.report', $student->id) }}"
                            class="px-3 py-2 bg-indigo-500 text-white underline ml-4" target="_blank">PDF出力
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        {{-- PC表示（テーブル形式） --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border  w-[8.5rem]" rowspan="2">氏名</th>
                        <th class="p-2 border  w-[6rem]" rowspan="2">学校</th>
                        <th class="p-2 border  w-[3.5rem]" rowspan="2">学年</th>
                        <th class="p-2 border" colspan="3">定期テスト</th>
                        <th class="p-2 border" colspan="3">北辰テスト</th>
                        <th class="p-2 border  w-[8.5rem]" rowspan="2">操作</th>
                    </tr>
                    <tr>
                        <th class="p-2 border  w-[3.5rem]">国語</th>
                        <th class="p-2 border  w-[3.5rem]">数学</th>
                        <th class="p-2 border  w-[3.5rem]">英語</th>
                        <th class="p-2 border  w-[3.5rem]">国語</th>
                        <th class="p-2 border  w-[3.5rem]">数学</th>
                        <th class="p-2 border  w-[3.5rem]">英語</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        @php
                            $latestRegular = $student->regularTests->sortByDesc('grade')->sortByDesc('semester')->first();
                            $latestHokushin = $student->hokushinTests->sortByDesc('grade')->sortByDesc('exam_number')->first();
                            $hasMemo = !empty(trim($student->memo ?? ''));
                            // ✅ 学年に応じて背景色を変える
                            $gradeColorClass = match($student->grade) {
                                '1年生' => 'bg-blue-50',
                                '2年生' => 'bg-green-50',
                                '3年生' => 'bg-yellow-50',
                                default => 'bg-white',
                            };
                        @endphp
                        <tr class="{{ $gradeColorClass }}">
                            <td class="p-2 border">{{ $student->last_name }} {{ $student->first_name }}</td>
                            <td class="p-2 border">{{ $student->school }}</td>
                            <td class="p-2 border">{{ $student->grade }}</td>
                            <td class="p-2 border">{{ $latestRegular?->japanese ?? '—' }}</td>
                            <td class="p-2 border">{{ $latestRegular?->math ?? '—' }}</td>
                            <td class="p-2 border">{{ $latestRegular?->english ?? '—' }}</td>
                            <td class="p-2 border">{{ $latestHokushin?->japanese ?? '—' }}</td>
                            <td class="p-2 border">{{ $latestHokushin?->math ?? '—' }}</td>
                            <td class="p-2 border">{{ $latestHokushin?->english ?? '—' }}</td>
                            <td class="p-2 border">
                                <a href="{{ route('students.dashboard', $student->id) }}" class="text-blue-600 hover:underline">
                                    成績を見る
                                </a>

                                <button onclick='openMemoModal({{ $student->id }}, {!! json_encode($student->memo ?? '') !!}, "{{ route('students.memo', $student->id) }}")'
                                    class="ml-2 text-sm text-gray-600 hover:text-blue-500 underline relative"
                                    @if($hasMemo) title="メモ登録あり" @endif>
                                    メモ
                                    @if($hasMemo)
                                        <span class="ml-1 inline-block w-2 h-2 rounded-full bg-red-500 align-middle" aria-label="メモ登録あり"></span>
                                    @endif
                                </button>

                                <a href="{{ route('guidances.history', $student->id) }}"
                                class="ml-2 text-sm text-green-600 hover:underline">
                                    指導報告
                                </a>
                                <a href="{{ route('guidances.report', $student->id) }}"
                                    class="text-indigo-600 underline ml-4" target="_blank">PDF出力</a>

                            </td>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <!-- メモモーダル -->
    <div id="memo-modal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow-md w-full max-w-lg relative">
            <h3 class="text-lg font-semibold mb-2">生徒メモ</h3>
            <textarea id="memo-text" rows="10" class="w-full border rounded p-2 text-sm"></textarea>
            <div class="flex justify-end mt-4">
                <button onclick="saveMemo()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">更新</button>
                <button onclick="closeMemoModal()" class="ml-2 text-gray-600 hover:underline">キャンセル</button>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
let currentStudentId = null;
let currentMemoUrl = null;

function openMemoModal(studentId, memo, memoUrl) {
    currentStudentId = studentId;
    currentMemoUrl = memoUrl;
    document.getElementById('memo-text').value = memo || '';
    document.getElementById('memo-modal').classList.remove('hidden');
    document.getElementById('memo-modal').classList.add('flex');
}

function closeMemoModal() {
    currentStudentId = null;
    currentMemoUrl = null;
    document.getElementById('memo-modal').classList.add('hidden');
    document.getElementById('memo-modal').classList.remove('flex');
}

function saveMemo() {
    const memo = document.getElementById('memo-text').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(currentMemoUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ memo })
    })
    .then(res => {
        if (res.ok) {
            alert("メモを保存しました。");
            closeMemoModal();
        } else {
            alert("保存に失敗しました。");
        }
    });
}

</script>
<!-- ▼ 追加：カレンダー表示トグル＋描画（既存の <script> 群の“下”、Tailwind CDN の“上”） -->
<script>
(function(){
  const apiUrl  = "{{ route('guidances.available_dates') }}";
  const bulkUrl = "{{ route('guidances.report.today') }}"; // ?date=YYYY-MM-DD

  const btn   = document.getElementById('pdf-bulk-btn');
  const pop   = document.getElementById('pdf-bulk-popover');
  const title = document.getElementById('cal-title');
  const grid  = document.getElementById('cal-grid');
  const prev  = document.getElementById('cal-prev');
  const next  = document.getElementById('cal-next');

  if (!btn || !pop || !title || !grid) return;

  let view = new Date();      // 表示中の月
  let open = false;           // ポップオーバー開閉状態
  let loadedYm = null;        // 直近に読み込んだ YYYY-MM（無駄な再取得防止）

  function ymd(d){const y=d.getFullYear(),m=('0'+(d.getMonth()+1)).slice(-2),dd=('0'+d.getDate()).slice(-2);return `${y}-${m}-${dd}`;}
  function ym(d){const y=d.getFullYear(),m=('0'+(d.getMonth()+1)).slice(-2);return `${y}-${m}`;}

  function openPDF(dateStr){
    window.open(`${bulkUrl}?date=${encodeURIComponent(dateStr)}`,'_blank');
  }

  async function render(){
    const year=view.getFullYear(), month=view.getMonth();
    title.textContent = `${year}年 ${month+1}月`;
    grid.innerHTML = '';

    const first = new Date(year, month, 1);
    const startW = first.getDay();
    const days   = new Date(year, month+1, 0).getDate();

    // 有データ日の取得（同月はキャッシュ）
    const key = ym(view);
    let marks = new Set();
    if (loadedYm !== key) {
      try{
        const res = await fetch(`${apiUrl}?month=${key}`, {credentials:'same-origin'});
        if (res.ok){
          const json = await res.json();
          (json.dates||[]).forEach(it => marks.add(it.date));
          loadedYm = key;
        }
      }catch(e){}
    } else {
      // キャッシュ時もサーバー依存を減らすため再取得はしない
      try{
        const res = await fetch(`${apiUrl}?month=${key}`, {credentials:'same-origin'});
        if (res.ok){
          const json = await res.json();
          (json.dates||[]).forEach(it => marks.add(it.date));
        }
      }catch(e){}
    }

    for (let i=0;i<startW;i++){
      const ph = document.createElement('div'); ph.className='h-8'; grid.appendChild(ph);
    }
    for (let d=1; d<=days; d++){
      const date = new Date(year, month, d);
      const dateStr = ymd(date);
      const has = marks.has(dateStr);

      const cell = document.createElement('button');
      cell.type = 'button';
      cell.className = 'h-10 border rounded hover:bg-gray-100 flex flex-col items-center justify-center ' +
                       (has ? 'border-indigo-300' : 'border-gray-200 opacity-50 cursor-not-allowed');
      cell.innerHTML = `<span class="leading-none">${d}</span>` +
                       (has ? `<span class="block w-1.5 h-1.5 rounded-full mt-0.5 bg-indigo-600"></span>` : '');

      if (has){
        cell.addEventListener('click', ()=> openPDF(dateStr));
        cell.title = 'この日の指導PDFを表示';
      } else {
        cell.disabled = true;
      }
      grid.appendChild(cell);
    }
  }

  function show(){
    if (open) return;
    pop.classList.remove('hidden');
    open = true;
    render();
    // 外側クリックで閉じる
    setTimeout(()=>{
      document.addEventListener('click', onDocClick);
      document.addEventListener('keydown', onKey);
    },0);
  }
  function hide(){
    if (!open) return;
    pop.classList.add('hidden');
    open = false;
    document.removeEventListener('click', onDocClick);
    document.removeEventListener('keydown', onKey);
  }
  function onDocClick(e){
    if (!pop.contains(e.target) && e.target !== btn) hide();
  }
  function onKey(e){
    if (e.key === 'Escape') hide();
  }

  btn.addEventListener('click', ()=> open ? hide() : show());
  prev?.addEventListener('click', ()=>{ view.setMonth(view.getMonth()-1); render(); });
  next?.addEventListener('click', ()=>{ view.setMonth(view.getMonth()+1); render(); });
})();
</script>


<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
