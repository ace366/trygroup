{{-- resources/views/layouts/navigation_staff.blade.php --}}
<nav class="bg-blue-600 border-b border-gray-100 z-50 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- ロゴ -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('staff.dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-white" />
                    </a>
                </div>

                <!-- ナビゲーションリンク -->
                <div class="hidden space-x-1 sm:-my-px sm:ml-10 sm:flex items-center">
                    <!-- メインへ戻る -->
                    <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')" class="text-white hover:text-white/90">
                        {{ __('メインへ') }}
                    </x-nav-link>

                    @php
                        $role   = auth()->user()->role ?? 'guest';
                        $isBiz   = request()->routeIs('staff.biz.*') || request()->routeIs('staff.projects.*');
                        $isShift = request()->routeIs('staff.shift.*');
                        $isTch   = request()->routeIs('staff.teachers.*');
                        $isPay   = request()->routeIs('staff.payouts.*');
                        $isMemo  = request()->routeIs('staff.memos.*');
                        $isMst   = request()->routeIs('staff.master.*');
                    @endphp

                    @if(auth()->check() && in_array($role, ['admin','teacher'], true))
                        {{-- 事業・会場 --}}
                        <div class="relative">
                            <button id="biz-menu-btn" type="button"
                                    class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition {{ $isBiz ? 'text-gray-900 bg-white' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="biz-menu">
                                事業・会場
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                </svg>
                            </button>
                            <div id="biz-menu" class="absolute right-0 mt-2 w-56 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                <a href="{{ route('staff.projects.index') }}"
                                    class="block px-4 py-2 text-sm {{ request()->routeIs('staff.projects.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                    事業
                                </a>
                                <a href="{{ route('staff.biz.venues') }}"    class="block px-4 py-2 text-sm {{ request()->routeIs('staff.biz.venues') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">会場</a>
                                <a href="{{ route('staff.biz.schedules') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('staff.biz.schedules') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">会場日程</a>
                                <a href="{{ route('staff.biz.students') }}"  class="block px-4 py-2 text-sm {{ request()->routeIs('staff.biz.students') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">生徒</a>
                            </div>
                        </div>

                        {{-- シフト --}}
                        <div class="relative">
                            <button id="shift-menu-btn" type="button"
                                    class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition {{ $isShift ? 'text-gray-900 bg-white' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="shift-menu">
                                シフト
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                </svg>
                            </button>
                            <div id="shift-menu" class="absolute right-0 mt-2 w-64 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                <a href="{{ route('staff.shift.by_schedule') }}"   class="block px-4 py-2 text-sm {{ request()->routeIs('staff.shift.by_schedule') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">会場日程別状況</a>
                                <a href="{{ route('staff.shift.leave') }}"         class="block px-4 py-2 text-sm {{ request()->routeIs('staff.shift.leave') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">休み申請</a>
                                <a href="{{ route('staff.shift.entry') }}"         class="block px-4 py-2 text-sm {{ request()->routeIs('staff.shift.entry') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">エントリー</a>
                                <a href="{{ route('staff.shift.count_settings') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('staff.shift.count_settings') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">回数設定状況</a>
                            </div>
                        </div>

                        {{-- 講師 --}}
                        <div class="relative">
                            <button id="tch-menu-btn" type="button"
                                    class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition {{ $isTch ? 'text-gray-900 bg-white' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="tch-menu">
                                講師
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                </svg>
                            </button>
                            <div id="tch-menu" class="absolute right-0 mt-2 w-44 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                <a href="{{ route('staff.teachers.search') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('staff.teachers.search') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">講師検索</a>
                                <a href="{{ route('staff.teachers.index') }}"  class="block px-4 py-2 text-sm {{ request()->routeIs('staff.teachers.index') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">講師</a>
                            </div>
                        </div>

                        {{-- 実績確認（メニューなし・無効表示） --}}
                        <div class="relative">
                            <button type="button" class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-400 cursor-not-allowed" aria-disabled="true">
                                実績確認（準備中）
                            </button>
                        </div>

                        {{-- 出金 --}}
                        <div class="relative">
                            <button id="pay-menu-btn" type="button"
                                    class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition {{ $isPay ? 'text-gray-900 bg-white' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="pay-menu">
                                出金
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                </svg>
                            </button>
                            <div id="pay-menu" class="absolute right-0 mt-2 w-56 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                <a href="{{ route('staff.payouts.monthly') }}"            class="block px-4 py-2 text-sm {{ request()->routeIs('staff.payouts.monthly') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">月別出金</a>
                                <a href="{{ route('staff.payouts.attendance_reports') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('staff.payouts.attendance_reports') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">勤怠報告状況</a>
                                <a href="{{ route('staff.payouts.execution') }}"         class="block px-4 py-2 text-sm {{ request()->routeIs('staff.payouts.execution') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">実施状況</a>
                            </div>
                        </div>

                        {{-- メモ --}}
                        <div class="relative">
                            <button id="memo-menu-btn" type="button"
                                    class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition {{ $isMemo ? 'text-gray-900 bg-white' : 'text-white hover:text-white/90' }}"
                                    aria-haspopup="true" aria-expanded="false" aria-controls="memo-menu">
                                メモ
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
                                </svg>
                            </button>
                            <div id="memo-menu" class="absolute right-0 mt-2 w-56 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
                                <a href="{{ route('staff.memos.projects') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('staff.memos.projects') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">事業メモ</a>
                                <a href="{{ route('staff.memos.venues') }}"   class="block px-4 py-2 text-sm {{ request()->routeIs('staff.memos.venues') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">会場メモ</a>
                                <a href="{{ route('staff.memos.teachers') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('staff.memos.teachers') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">講師メモ</a>
                                <a href="{{ route('staff.memos.students') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('staff.memos.students') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">生徒メモ</a>
                            </div>
                        </div>

{{-- マスタ --}}
<div class="relative">
    <button id="mst-menu-btn" type="button"
            class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition {{ $isMst ? 'text-gray-900 bg-white' : 'text-white hover:text-white/90' }}"
            aria-haspopup="true" aria-expanded="false" aria-controls="mst-menu">
        マスタ
        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" aria-hidden="true">
            <path fill="currentColor" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414z"/>
        </svg>
    </button>
    <div id="mst-menu" class="absolute right-0 mt-2 w-56 bg-white border rounded-md shadow-lg hidden z-50" role="menu">
        {{-- ▼ 受託元 --}}
        <a href="{{ route('staff.master.clients') }}"
           class="block px-4 py-2 text-sm {{ request()->routeIs('staff.master.clients*') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            受託元
        </a>
        {{-- 既存：システム系マスタ --}}
        <a href="{{ route('staff.master.system') }}"
           class="block px-4 py-2 text-sm {{ request()->routeIs('staff.master.system') ? 'text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            システム系マスタ
        </a>
    </div>
</div>
                    @endif
                </div>
            </div>

            <!-- 右側（ユーザー） -->
            <div class="flex sm:flex sm:items-center sm:ml-6">
                @if(auth()->check())
                    <div class="relative">
                        <button id="user-menu-btn" type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                                aria-haspopup="true" aria-expanded="false" aria-controls="user-menu">
                            <div>ようこそ、{{ auth()->user()->last_name }} さん</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                        <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden" role="menu">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" role="menuitem">プロフィール情報</a>
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
@stack('modals')
@stack('scripts')
</nav>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggle = (btnId, menuId, others=[]) => {
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        if (!btn || !menu) return;

        const close = () => {
            menu.classList.add("hidden");
            btn.setAttribute('aria-expanded', 'false');
        };
        const open = () => {
            others.forEach(id => {
                const m = document.getElementById(id);
                if (m) m.classList.add("hidden");
            });
            menu.classList.remove("hidden");
            btn.setAttribute('aria-expanded', 'true');
        };

        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            if (menu.classList.contains("hidden")) {
                open();
            } else {
                close();
            }
        });

        // キーボード対応
        btn.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                if (menu.classList.contains("hidden")) {
                    open();
                } else {
                    close();
                }
            } else if (e.key === "Escape") {
                close();
                btn.focus();
            }
        });

        document.addEventListener("click", function (e) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                close();
            }
        });
    };

    // ユーザーメニュー
    toggle('user-menu-btn', 'user-menu');

    // 各ドロップダウン（相互に閉じる）
    toggle('biz-menu-btn',   'biz-menu',   ['shift-menu','tch-menu','pay-menu','memo-menu','mst-menu','user-menu']);
    toggle('shift-menu-btn', 'shift-menu', ['biz-menu','tch-menu','pay-menu','memo-menu','mst-menu','user-menu']);
    toggle('tch-menu-btn',   'tch-menu',   ['biz-menu','shift-menu','pay-menu','memo-menu','mst-menu','user-menu']);
    toggle('pay-menu-btn',   'pay-menu',   ['biz-menu','shift-menu','tch-menu','memo-menu','mst-menu','user-menu']);
    toggle('memo-menu-btn',  'memo-menu',  ['biz-menu','shift-menu','tch-menu','pay-menu','mst-menu','user-menu']);
    toggle('mst-menu-btn',   'mst-menu',   ['biz-menu','shift-menu','tch-menu','pay-menu','memo-menu','user-menu']);
});
</script>
