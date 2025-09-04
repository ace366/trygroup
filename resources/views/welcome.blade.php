
<x-app-layout>
    <!-- ヘッダーの右上にログイン・新規登録ボタンを追加 -->
    <!--<div class="absolute top-20 right-1/3 flex space-x-4 flex-wrap w-full md:w-auto justify-end">-->
    <div class="absolute top-20 right-10 flex space-x-4 flex-wrap md:w-auto justify-end">
        @auth
            <!-- ログイン済みのとき -->
            @if(Route::has('videos.dashboard_ts'))
                <a href="{{ route('videos.dashboard_ts') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 hidden md:block">
                    メインページへ
                </a>
            @endif
        @else
            <!-- 未ログインのとき -->
            @if(Route::has('login'))
                <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700 hidden md:block">
                    ログイン
                </a>
            @endif
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-700 hidden md:block">
                    新規登録
                </a>
            @endif
        @endauth

    </div>
    @auth
        <div class="text-center text-lg font-semibold text-gray-700 sm:mt-16 md:mt-3">
            {{ auth()->user()->last_name }} さん、こんにちは！<br><br>
            <!-- あなたは <span class="text-blue-500">{{ auth()->user()->login_count }}</span> 回ログインしています。 -->
        

<a href="https://calendar.app.google/pu6oN3tjEbL45Nhd6" class="text-red-500 hover:underline">進路相談・学習相談の予約はこちらから</a>
</div>
    @endauth

    <div class="container mx-auto p-6 sm:mt-16 md:mt-1">
        <h1 class="text-3xl font-bold text-center mb-6">行政事業部</h1>

        <!-- メインコンテンツ -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- 📅 左ブロック: 1年間のスケジュール -->
            <div class="bg-white shadow-lg p-6 rounded-lg">
                <h2 class="text-2xl font-semibold mb-4">年間スケジュール</h2>
                    @if($schedules->count())
                        @php
                            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
                        @endphp
                        @foreach($schedules as $schedule)
                            @php
                                $carbonDate = \Carbon\Carbon::parse($schedule->date);
                                $weekday = $weekdays[$carbonDate->dayOfWeek];
                            @endphp
                            <p>{{ $carbonDate->format('m月d日') }}（{{ $weekday }}） - {{ $schedule->event }}</p>
                        @endforeach
                    @else
                        <p>スケジュールはまだ登録されていません。</p>
                    @endif

                <div class="mt-4">
                    <a href="{{ route('schedules.index') }}" class="text-blue-500 hover:underline">年間スケジュールを見る →</a>
                </div>
            </div>

            <!-- 📝 右ブロック: 掲示板 -->
            <div class="bg-white shadow-lg p-6 rounded-lg">
                <h2 class="text-2xl font-semibold mb-4">掲示板</h2>
                <ul class="space-y-3">
                    @if(!empty($posts) && $posts->count())
                        @foreach($posts as $post)
                            <li class="p-3 bg-gray-200 rounded-md">
                                <strong>{{ $post->title }}</strong>
                                <button class="toggle-details text-blue-500 underline ml-2" data-id="{{ $post->id }}">詳細</button>
                                
                                <div id="details-{{ $post->id }}" class="hidden mt-2 break-all">
                                    {!! nl2br(linkify($post->content)) !!}
                                    <span class="text-sm text-gray-500">({{ $post->created_at->format('Y-m-d H:i') }})</span>
                                </div>
                            </li>
                        @endforeach
                    @else
                        <p>おしらせはまだ登録されていません。</p>
                    @endif
                </ul>
                <div class="mt-4">
                    @if(Route::has('posts.index'))
                        <a href="{{ route('posts.index') }}" class="text-blue-500 hover:underline">掲示板を見る</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
<div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-md md:hidden">
    <div class="flex justify-around items-center py-3 text-sm">
        <!-- オンライン（Zoom風） -->
        <div class="flex flex-col items-center">
            <a href="{{ route('online.classes') }}" class="text-blue-600 font-bold text-sm">
                オンライン
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </a>
        </div>

        <!-- 動画 -->
        <div class="flex flex-col items-center">
            <a href="{{ route('videos.dashboard_ts') }}" class="text-blue-600 font-bold text-sm">
                動画
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
            </a>
        </div>

        <!-- QRコード -->
        <div class="flex flex-col items-center">
            <a href="{{ route('users.my_card') }}" class="text-blue-600 font-bold text-sm">
                QRコード
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zM10 4h4v4h-4V4zM16 4h4v4h-4V4zM4 10h4v4H4v-4zM16 10h4v4h-4v-4zM4 16h4v4H4v-4zM10 16h4v4h-4v-4zM16 16h4v4h-4v-4z" />
                </svg>
            </a>
        </div>
        @auth
            <div class="flex flex-col items-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-blue-600 font-bold">
                        ログアウト
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <div class="flex flex-col items-center">
                <a href="{{ route('login') }}" class="text-blue-600 font-bold">
                    ログイン
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                    </svg>
                </a>
            </div>
            <div class="flex flex-col items-center">
                <a href="{{ route('register') }}" class="text-green-600 font-bold">
                    登録
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </a>
            </div>
        @endauth
    </div>
</div>

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
<!-- ✅ JavaScriptで詳細を表示/非表示 -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".toggle-details").forEach(button => {
            button.addEventListener("click", function () {
                let details = document.getElementById("details-" + this.dataset.id);
                if (details.classList.contains("hidden")) {
                    details.classList.remove("hidden");
                } else {
                    details.classList.add("hidden");
                }
            });
        });
    });
</script>
</x-app-layout>
