<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">LINE 登録</h1>

        @if(session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-200 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-red-800 bg-red-200 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 mb-4 text-yellow-800 bg-yellow-200 rounded-lg">
                {{ session('info') }}
            </div>
        @endif

        <div class="text-center">
            <p class="text-lg">以下のURLをクリックしてLINEと連携してください。</p>
            <a href="{{ $lineLoginUrl }}" class="inline-block bg-green-500 text-white font-semibold py-2 px-4 rounded-md mt-4 hover:bg-green-600">
                LINE 連携
            </a>
        </div>

        <div class="text-center mt-6">
            <p class="text-lg">またはQRコードをスキャンして登録</p>
            <img src="https://qr-official.line.me/gs/M_831fqysy_GW.png?oat_content=qr" alt="LINE QRコード" class="mx-auto w-48">
        </div>
    </div>
</x-app-layout>
