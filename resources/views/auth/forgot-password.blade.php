<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('メールアドレスをお知らせいただければ、パスワードリセット用のリンクをメールでお送りしますので、新しいパスワードをご選択ください。') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <!-- ? 戻るボタンを追加 -->
            <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-500 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-gray-600 transition">
                {{ __('戻る') }}
            </a>
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
