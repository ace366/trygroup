{{-- resources/views/layouts/navigation.blade.php --}}
<nav class="bg-blue-600 border-b border-gray-100 z-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- ロゴ -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('welcome') }}" class="h-16 flex items-center">
                        <x-application-logo class="block h-9 w-auto fill-current text-white" />
                    </a>
                </div>

                <!-- ナビゲーションリンク -->
                <div class="hidden space-x-1 sm:-my-px sm:ml-10 sm:flex">
                    {{-- トップ画面 --}}
                    <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')" class="h-16 flex items-center">
                        <span class="flex flex-col items-center text-center leading-tight gap-1
                            {{ request()->routeIs('welcome')
                                ? 'bg-white text-black px-3 py-2 rounded-md'
                                : 'text-white hover:text-white/90' }}">
                            <span class="whitespace-nowrap">トップ</span>
                            <span>画面</span>
                        </span>
                    </x-nav-link>

                    {{-- 学習動画 --}}
                    <x-nav-link :href="route('videos.dashboard_ts')" :active="request()->routeIs('videos.dashboard_ts')" class="h-16 flex items-center">
                        <span class="flex flex-col items-center text-center leading-tight gap-1
                            {{ request()->routeIs('videos.dashboard_ts')
                                ? 'bg-white text-black px-3 py-2 rounded-md'
                                : 'text-white hover:text-white/90' }}">
                            <span class="whitespace-nowrap">学習</span>
                            <span>動画</span>
                        </span>
                    </x-nav-link>

                    @if(auth()->check())
                        {{-- オンライン授業 --}}
                        <x-nav-link :href="route('online.classes')" :active="request()->routeIs('online.classes')" class="h-16 flex items-center">
                            <span class="flex flex-col items-center text-center leading-tight gap-1
                                {{ request()->routeIs('online.classes')
                                    ? 'bg-white text-black px-3 py-2 rounded-md'
                                    : 'text-white hover:text-white/90' }}">
                                <span class="whitespace-nowrap">オンライン</span>
                                <span>授業</span>
                            </span>
                        </x-nav-link>

                        {{-- QRコード --}}
                        <x-nav-link :href="route('users.my_card')" :active="request()->routeIs('users.my_card')" class="h-16 flex items-center">
                            <span class="flex flex-col items-center text-center leading-tight gap-1
                                {{ request()->routeIs('users.my_card')
                                    ? 'bg-white text-black px-3 py-2 rounded-md'
                                    : 'text-white hover:text-white/90' }}">
                                <span class="whitespace-nowrap">QR</span>
                                <span>コード</span>
                            </span>
                        </x-nav-link>

                        {{-- 教えて！彩さん！ --}}
                        <x-nav-link :href="route('ask.ai')" :active="request()->routeIs('ask.ai')" class="h-16 flex items-center">
                            <span class="flex flex-col items-center text-center leading-tight gap-1
                                {{ request()->routeIs('ask.ai')
                                    ? 'bg-white text-black px-3 py-2 rounded-md'
                                    : 'text-white hover:text-white/90' }}">
                                <span class="whitespace-nowrap">教えて！</span>
                                <span>彩さん！</span>
                            </span>
                        </x-nav-link>

                        @if(auth()->user()->role === 'admin')
                            <!-- 成績登録（単独） -->
                            <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.index')" class="h-16 flex items-center">
                                <span class="{{ request()->routeIs('students.index') ? 'bg-white text-black px-3 py-2 rounded-md' : 'text-white hover:text-white/90' }}">
                                    {{ __('成績登録') }}
                                </span>
                            </x-nav-link>

                            <!-- 一括メール（配信／履歴） -->
                            @php
                                $bulkActive = request()->routeIs('admin.mails.index')
                                              || request()->routeIs('admin.mails.history')
                                              || request()->routeIs('admin.mails.history.show');
                            @endphp
                            <div class="relative">
                                <button id="bulk-menu-btn" type="button"
                                    class="h-16 inline-flex items-center px-4 rounded-md text-sm font-medium transition
                                        {{ $bulkActive ? 'bg-white text-black' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="bulk-menu">
                                    配信
                                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                    </svg>
                                </button>
                                <div id="bulk-menu" class="absolute right-0 mt-2 w-44 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                    <a href="{{ route('admin.mails.index') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('admin.mails.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">メール配信</a>
                                    <a href="{{ route('admin.mails.history') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('admin.mails.history') || request()->routeIs('admin.mails.history.show') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">メール履歴</a>
                                    <a href="{{ route('line.send') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('admin.line.send') || request()->routeIs('admin.line.send') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">Line配信</a>
                                </div>
                            </div>

                            <!-- 各登録（動画／お知らせ／年間スケジュール） -->
                            @php
                                $regActive = request()->routeIs('videos.create')
                                              || request()->routeIs('posts.index')
                                              || request()->routeIs('schedules.index');
                            @endphp
                            <div class="relative">
                                <button id="reg-menu-btn" type="button"
                                    class="h-16 inline-flex items-center px-4 rounded-md text-sm font-medium transition
                                        {{ $regActive ? 'bg-white text-black' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="reg-menu">
                                    各登録
                                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                    </svg>
                                </button>
                                <div id="reg-menu" class="absolute right-0 mt-2 w-56 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                    <a href="{{ route('videos.create') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('videos.create') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">動画登録</a>
                                    <a href="{{ route('posts.index') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('posts.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">お知らせ登録</a>
                                    <a href="{{ route('schedules.index') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('schedules.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">年間スケジュール登録</a>
                                </div>
                            </div>

                            <!-- 生徒管理・出席（出席登録／出席管理／生徒管理） -->
                            @php
                                $stuActive = request()->routeIs('attendance.scan')
                                              || request()->routeIs('attendance.index')
                                              || request()->routeIs('users.index');
                            @endphp
                            <div class="relative">
                                <button id="stu-menu-btn" type="button"
                                    class="h-16 inline-flex items-center px-4 rounded-md text-sm font-medium transition
                                        {{ $stuActive ? 'bg-white text-black' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="stu-menu">
                                    生徒管理・出席
                                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                    </svg>
                                </button>
                                <div id="stu-menu" class="absolute right-0 mt-2 w-56 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                    <a href="{{ route('attendance.scan') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('attendance.scan') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">出席登録</a>
                                    <a href="{{ route('attendance.index') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('attendance.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">出席管理</a>
                                    <a href="{{ route('users.index') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('users.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">生徒管理</a>
                                </div>
                            </div>

                            <!-- 講師管理へ（メイン→講師管理ナビへ遷移） -->
                            <x-nav-link :href="route('staff.dashboard')" :active="request()->routeIs('staff.*')" class="h-16 flex items-center">
                                <span class="{{ request()->routeIs('staff.*') ? 'bg-white text-black px-3 py-2 rounded-md' : 'text-white hover:text-white/90' }}">
                                    {{ __('講師管理へ') }}
                                </span>
                            </x-nav-link>

                        @elseif(auth()->user()->role === 'teacher')
                            <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.index')" class="h-16 flex items-center">
                                <span class="{{ request()->routeIs('students.index') ? 'bg-white text-black px-3 py-2 rounded-md' : 'text-white hover:text-white/90' }}">
                                    {{ __('成績登録') }}
                                </span>
                            </x-nav-link>

                        @elseif(auth()->user()->role === 'editor')
                            <!-- エディター向け：各登録のみ -->
                            @php
                                $regActive = request()->routeIs('videos.create')
                                              || request()->routeIs('posts.index')
                                              || request()->routeIs('schedules.index');
                            @endphp
                            <div class="relative">
                                <button id="reg-menu-btn" type="button"
                                    class="h-16 inline-flex items-center px-4 rounded-md text-sm font-medium transition
                                        {{ $regActive ? 'bg-white text-black' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="reg-menu">
                                    各登録
                                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                    </svg>
                                </button>
                                <div id="reg-menu" class="absolute right-0 mt-2 w-56 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                    <a href="{{ route('videos.create') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('videos.create') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">動画登録</a>
                                    <a href="{{ route('posts.index') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('posts.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">お知らせ登録</a>
                                    <a href="{{ route('schedules.index') }}"
                                       class="block px-4 py-2 text-sm {{ request()->routeIs('schedules.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                       role="menuitem">年間スケジュール登録</a>
                                </div>
                            </div>

                        @else
                            {{-- user（一般生徒）向け --}}
                            <x-nav-link :href="route('students.dashboard', auth()->id())" :active="request()->routeIs('students.dashboard')" class="h-16 flex items-center">
                                <span class="{{ request()->routeIs('students.dashboard') ? 'bg-white text-black px-3 py-2 rounded-md' : 'text-white hover:text-white/90' }}">
                                    {{ __('成績登録') }}
                                </span>
                            </x-nav-link>
                        @endif
                    @endif
                </div>
            </div>

            <!-- 右側：ユーザードロップダウン -->
            <div class="flex sm:flex sm:items-center sm:ml-6">
                @if(auth()->check())
                    <div class="relative">
                        <button id="user-menu-btn" type="button"
                                class="h-16 inline-flex items-center px-4 border border-transparent text-sm leading-4 font-medium rounded-md bg-white text-gray-700 hover:text-gray-900 focus:outline-none transition"
                                aria-haspopup="true" aria-expanded="false" aria-controls="user-menu">
                            <div>ようこそ、{{ auth()->user()->last_name }} さん</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>

                        <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden" role="menu" aria-labelledby="user-menu-btn">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" role="menuitem">
                                プロフィール情報
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    {{ __('ログアウト') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</nav>

<!-- モバイル固定ボトムバー -->
<div class="fixed bottom-0 left-0 w-full bg-pink-50 border-t border-gray-200 shadow-md md:hidden" style="z-index: 9999;">
    <div class="flex justify-around items-center py-3 text-sm">
        <div class="flex flex-col items-center">
            <a href="{{ route('online.classes') }}" class="text-blue-600 font-bold text-sm">
                オンライン
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </a>
        </div>
        <div class="flex flex-col items-center">
            <a href="{{ route('videos.dashboard_ts') }}" class="text-blue-600 font-bold text-sm">
                動画
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                </svg>
            </a>
        </div>
        <div class="flex flex-col items-center">
            <a href="{{ route('welcome') }}" class="text-blue-600 font-bold text-sm">
                ホーム
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
            </a>
        </div>
        <div class="flex flex-col items-center">
            <a href="{{ route('users.my_card') }}" class="text-blue-600 font-bold text-sm">
                QRコード
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 block mx-auto mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h4v4H4V4zM10 4h4v4h-4V4zM16 4h4v4h-4V4zM4 10h4v4H4v-4zM16 10h4v4h-4v-4zM4 16h4v4H4v-4zM10 16h4v4h-4v-4zM16 16h4v4h-4v-4z" />
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- JavaScript: ドロップダウン開閉制御 -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const setupToggle = (btnId, menuId, closeIds = []) => {
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        if (!btn || !menu) return;
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            closeIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add("hidden");
            });
            menu.classList.toggle("hidden");
            btn.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
        });
        document.addEventListener("click", (e) => {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add("hidden");
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    };

    // ユーザーメニュー
    setupToggle('user-menu-btn', 'user-menu', ['bulk-menu','reg-menu','stu-menu']);

    // 管理者ドロップダウン
    setupToggle('bulk-menu-btn', 'bulk-menu', ['reg-menu','stu-menu','user-menu']);
    setupToggle('reg-menu-btn',  'reg-menu',  ['bulk-menu','stu-menu','user-menu']);
    setupToggle('stu-menu-btn',  'stu-menu',  ['bulk-menu','reg-menu','user-menu']);
});
</script>
