<div class="min-h-screen bg-gray-100">
    {{-- 講師管理用ナビ --}}
    @include('layouts.navigation_staff')

    <!-- Page Heading（必要なら各ページで <x-slot name="header"> を使う） -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main>
        {{ $slot }}
    @stack('modals')
    @stack('scripts')
    </main>
</div>
