<x-app-layout>
<div class="container mx-auto p-6">

    <h1 class="text-2xl font-bold text-center mb-6">ユーザーカード印刷ページ</h1>

    <!-- ✅ チェック操作ボタン -->
    <div class="flex justify-center gap-4 mb-4 no-print">
        <button onclick="checkAll(true)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">全てチェック</button>
        <button onclick="checkAll(false)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">全てのチェックを外す</button>
    </div>

    <div class="flex flex-wrap justify-start gap-4 print:gap-0">
        @foreach($users as $user)
            <div class="card-container border p-2 rounded shadow-md text-center bg-white w-[91mm] h-[55mm] flex flex-col justify-start print:w-[91mm] print:h-[55mm] relative overflow-hidden" data-user-id="{{ $user->id }}">
                
                <!-- ✅ チェックボックス -->
                <div class="absolute top-1 right-1 no-print">
                    <input type="checkbox" class="card-checkbox" data-user-id="{{ $user->id }}">
                </div>

                <!-- ロゴ -->
                <img src="{{ asset('images/logo.JPG') }}" alt="Logo" class="absolute top-2 left-2 w-10 h-10 object-contain">

                <!-- QRコード -->
                <div class="flex justify-center items-center flex-grow">
                    <div id="qrcode-{{ $user->id }}"></div>
                </div>

                <!-- ユーザー情報 -->
                <div class="flex flex-col items-center space-y-0.5 mt-1 mb-1">
                    <p class="font-bold text-sm">{{ $user->last_name }} {{ $user->first_name }}</p>
                    <p class="text-[10px] text-gray-600">({{ $user->last_name_kana }} {{ $user->first_name_kana }})</p>
                    <p class="text-[10px]">{{ $user->school }}</p>
                    <p class="text-[10px]">{{ $user->grade }}</p>
                    <p class="text-[10px]">{{ $user->eiken }}</p>
                    <p class="text-[10px]">{{ $user->lesson_time }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 印刷ボタン -->
    <div class="mt-6 text-center no-print">
        <button onclick="printSelectedCards()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            チェックしたカードのみ印刷
        </button>
    </div>
<!-- チェックしたQRコードをスマホ風に表示 -->
<div class="mt-3 text-center no-print">
    <button onclick="showSelectedCards()" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
        チェックしたQRコードを画面表示
    </button>
</div>
</div>
<!-- ✅ QRコード表示モーダル -->
<div id="selected-modal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden overflow-auto">
    <div id="selected-content" class="bg-white rounded-lg shadow-2xl p-6 max-w-full w-full h-full flex flex-wrap justify-center content-start gap-6 overflow-auto">
        <!-- JavaScriptで選択カード複製が入る -->
    </div>
</div>


<!-- qrcode.min.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    // QRコード生成
    document.addEventListener('DOMContentLoaded', function () {
        @foreach($users as $user)
            new QRCode(document.getElementById("qrcode-{{ $user->id }}"), {
                text: "{{ $user->id }}",
                width: 70,
                height: 70
            });
        @endforeach
    });

    // 全チェック・解除
    function checkAll(flag) {
        document.querySelectorAll('.card-checkbox').forEach(cb => cb.checked = flag);
    }

    // 印刷対象だけ表示
    function printSelectedCards() {
        // 全カードを一旦非表示
        document.querySelectorAll('.card-container').forEach(card => card.classList.add('print-hide'));

        // チェックされたカードのみ表示
        document.querySelectorAll('.card-checkbox:checked').forEach(cb => {
            const userId = cb.dataset.userId;
            const card = document.querySelector(`.card-container[data-user-id="${userId}"]`);
            if (card) card.classList.remove('print-hide');
        });

        window.print();

        // 印刷後すべて再表示
        setTimeout(() => {
            document.querySelectorAll('.card-container').forEach(card => card.classList.remove('print-hide'));
        }, 100);
    }
// ✅ チェックされたカードだけを画面にモーダル表示
function showSelectedCards() {
    const modal = document.getElementById('selected-modal');
    const content = document.getElementById('selected-content');
    content.innerHTML = ''; // 初期化

    // 選択されたカードだけ複製表示
    document.querySelectorAll('.card-checkbox:checked').forEach(cb => {
        const userId = cb.dataset.userId;
        const card = document.querySelector(`.card-container[data-user-id="${userId}"]`);
        if (card) {
            const clone = card.cloneNode(true);

            clone.classList.remove('print-hide');
            clone.classList.add('shadow-md', 'bg-white');
            clone.querySelector('.card-checkbox')?.remove(); // チェックボックスは消す

            modal.querySelector('#selected-content').appendChild(clone);
        }
    });

    modal.classList.remove('hidden');
}

// ✅ モーダルをクリックで閉じる
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('selected-modal').addEventListener('click', function (e) {
        if (e.target.id === 'selected-modal') {
            this.classList.add('hidden');
        }
    });
});

</script>

<!-- 印刷専用CSS -->

<style>
@media print {
    .no-print {
        display: none !important;
    }
    nav, header, footer {
        display: none !important;
    }

    /* ✅ A4に収まるように余白調整 */
    @page {
        size: A4 portrait;
        margin: 5mm;
    }

    body {
        margin: 0;
        padding: 0;
    }

    .card-container.print-hide {
        display: none !important;
    }

    .container {
        max-width: none !important;
        padding: 0 !important;
        margin: 0 auto !important;
    }

    .card-container {
        page-break-inside: avoid;
    }
}
</style>

</x-app-layout>
