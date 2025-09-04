<div class="fixed bottom-0 left-0 w-full bg-red-100 border-t border-gray-200 shadow-md md:hidden" style="z-index: 9999;">
    <div class="flex justify-around items-center py-3 text-sm">

        {{-- ホーム --}}
        <div class="flex flex-col items-center">
            <a href="{{ route('welcome') }}" class="text-blue-600 font-bold text-sm">
                ホーム
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
            </a>
        </div>

        {{-- オンライン授業 --}}
        <div class="flex flex-col items-center">
            <a href="{{ route('online.classes') }}" class="text-blue-600 font-bold text-sm">
                オンライン
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </a>
        </div>

        {{-- 学習動画 --}}
        <div class="flex flex-col items-center">
            <a href="{{ route('videos.dashboard_ts') }}" class="text-blue-600 font-bold text-sm">
                動画
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
            </a>
        </div>

        {{-- QRコード --}}
        <div class="flex flex-col items-center">
            <a href="{{ route('users.my_card') }}" class="text-blue-600 font-bold text-sm">
                QRコード
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zM10 4h4v4h-4V4zM16 4h4v4h-4V4zM4 10h4v4H4v-4zM16 10h4v4h-4v-4zM4 16h4v4H4v-4zM10 16h4v4h-4v-4zM16 16h4v4h-4v-4z" />
                </svg>
            </a>
        </div>

        {{-- 成績登録（ログイン者のロールに応じて切替） --}}
        <div class="flex flex-col items-center">
            <a href="{{ in_array(auth()->user()->role, ['admin', 'teacher']) ? route('students.index') : route('students.dashboard', auth()->id()) }}"
               class="text-blue-600 font-bold text-sm">
                成績登録
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
                </svg>
            </a>
        </div>

        {{-- ログアウト --}}
        <div class="flex flex-col items-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-blue-600 font-bold text-sm">
                    ログアウト
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>

    </div>
</div>
