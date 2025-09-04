<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- ファビコン設定 -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <!-- <title>{{ config('app.name', 'Laravel') }}</title> -->
        <title>行政事業部</title>
        <!-- Fonts -->
        <!--<link rel="preconnect" href="https://fonts.bunny.net">-->
        <!--<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />-->

        <!-- Scripts -->
        <!--@vite(['resources/css/app.css', 'resources/js/app.js'])-->
        <link rel="stylesheet" href="{{ asset('build/assets/app-Cqj_IzKR.css') }}">
        <script src="{{ asset('build/assets/app-CAkSn3BF.js') }}" defer></script>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex flex-col mt-2 items-center bg-white min-h-screen justify-center"> {{-- ✅ 高さを中央に固定 --}}
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-5 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
