<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- ファビコン設定 -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>行政事業部</title>
    
    <!--{{-- Laravel Mix/Viteではなく、ハッシュ付きファイルを手動で読み込む --}}-->
    <link rel="stylesheet" href="{{ asset('build/assets/app-Cqj_IzKR.css') }}">
    <script src="{{ asset('build/assets/app-CAkSn3BF.js') }}" defer></script>

    <!--{{-- Viteの読み込みは無効化 --}}-->
    <!--{{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}-->
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot ?? '' }}
        </main>
    </div>
<footer class="footer-padding text-sm text-gray-500 mt-5">
    <div class="footer-inner">
        <img src="{{ asset('images/logo.JPG') }}" alt="よりE土曜塾ロゴ" class="footer-logo">
        <span class="footer-text">© トライグループ　行政事業部 / Trygroup Inc. All rights reserved.</span>
    </div>
</footer>
<style>
.footer-padding {
    padding-bottom: 100px;
}

.footer-inner {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
}

.footer-logo {
    width: 32px;
    height: 32px;
}

.footer-text {
    font-size: 14px;
    color: #6b7280;
    text-align: center;
}
</style>
</body>
</html>
