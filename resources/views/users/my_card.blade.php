<x-app-layout>
<div class="container mx-auto p-6">

    <h1 class="text-2xl font-bold text-center mb-6">マイQRカード</h1>

    <div class="flex justify-center">
        <div class="border p-2 rounded shadow-md text-center bg-white w-[91mm] h-[60mm] flex flex-col justify-start items-center print:w-[91mm] print:h-[55mm] relative overflow-hidden">
            
            <!-- 左上にロゴ -->
            <img src="{{ asset('images/logo.JPG') }}" alt="Logo" class="absolute top-2 left-2 w-10 h-10 object-contain">

            <!-- 中央にQRコード（モーダル用ボタン追加） -->
            <div class="flex flex-col justify-center items-center flex-grow relative">
                <div id="qrcode-mycard" class="cursor-pointer"></div>

                <!-- スマホのみ表示の拡大ボタン -->
                <button onclick="showQRCodeModal()" class="mt-2 text-sm text-blue-600 underline md:hidden">
                    🔍 QRコードを拡大表示
                </button>
            </div>

            <!-- 下にユーザー情報 -->
            <div class="flex flex-col items-center space-y-0.5 mt-1 mb-1">
                <p class="font-bold text-sm">{{ $user->last_name }} {{ $user->first_name }}</p>
                <p class="text-[10px] text-gray-600">({{ $user->last_name_kana }} {{ $user->first_name_kana }})</p>
                <p class="text-[10px]">{{ $user->school }}</p>
                <p class="text-[10px]">{{ $user->grade }}</p>
                <p class="text-[10px]">{{ $user->eiken }}</p>
                <p class="text-[10px]">{{ $user->lesson_time }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 text-center no-print">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            このページを印刷
        </button>
    </div>

</div>

<!-- QRモーダル -->
<div id="qr-modal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-4 rounded shadow-lg translate-y-[-30px]">
        <div id="qrcode-modal" class="w-[250px] h-[250px]"></div>
        <p class="text-center text-sm mt-2 text-gray-700">タップで閉じる</p>
    </div>
</div>

<!-- スクリプト -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 通常サイズQR
        new QRCode(document.getElementById("qrcode-mycard"), {
            text: "{{ $user->id }}",
            width: 70,
            height: 70
        });

        // 拡大サイズQR
        new QRCode(document.getElementById("qrcode-modal"), {
            text: "{{ $user->id }}",
            width: 250,
            height: 250
        });

        // モーダル閉じる処理
        document.getElementById("qr-modal").addEventListener("click", function () {
            this.classList.add("hidden");
        });
    });

    function showQRCodeModal() {
        document.getElementById("qr-modal").classList.remove("hidden");
    }
</script>

<!-- 印刷専用CSS -->
<style>
@media print {
    .no-print {
        display: none !important;
    }
    nav, header, footer, #qr-modal {
        display: none !important;
    }
    body {
        margin: 0;
        padding: 0;
    }
}
</style>
</x-app-layout>
