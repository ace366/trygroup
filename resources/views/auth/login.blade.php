{{-- resources/views/auth/login.blade.php （全置換） --}}
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン | 行政事業部</title>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-sm bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">ログイン</h1>

            {{-- セッションメッセージ --}}
            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- バリデーションエラー --}}
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5" autocomplete="on">
                @csrf

                <div>
                    <label for="email" class="block text-sm text-gray-700 mb-1">メールアドレス</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="w-full border rounded px-3 py-2"
                           autocomplete="username" inputmode="email">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm text-gray-700 mb-1">パスワード</label>
                    <input id="password" name="password" type="password" required
                           class="w-full border rounded px-3 py-2"
                           autocomplete="current-password">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="remember" class="rounded mr-2"
                               {{ old('remember') ? 'checked' : '' }}>
                        ログイン状態を保存する
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                            パスワードを忘れた方はこちら
                        </a>
                    @endif
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded">
                    ログイン
                </button>

                @if (Route::has('register'))
                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}" class="text-blue-600 underline hover:text-blue-800 text-sm">
                            新規登録はこちら
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Tailwind CDN（開発用。ビルド済みなら不要） -->
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
