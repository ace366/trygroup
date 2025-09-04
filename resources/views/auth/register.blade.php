{{-- resources/views/auth/register.blade.php （全置換） --}}
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>新規登録 | 行政事業部</title>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-lg bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">新規登録</h1>

            {{-- 必須注意書き --}}
            <div class="text-red-500 text-sm mb-4">
                ※特に記載のないもの以外、すべて入力必須です。現時点で不明な場合は仮で登録をお願いします。登録後プロフィール情報から変更ができます。
            </div>

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

            <form method="POST" action="{{ route('register') }}" class="space-y-4" autocomplete="on">
                @csrf

                {{-- 姓 --}}
                <div>
                    <label for="last_name" class="block text-sm text-gray-700 mb-1">姓（＊必須）</label>
                    <input id="last_name" name="last_name" type="text"
                           class="block mt-1 w-full border rounded px-3 py-2"
                           value="{{ old('last_name') }}" placeholder="例）山田" required autofocus>
                    @error('last_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 名 --}}
                <div>
                    <label for="first_name" class="block text-sm text-gray-700 mb-1">名（＊必須）</label>
                    <input id="first_name" name="first_name" type="text"
                           class="block mt-1 w-full border rounded px-3 py-2"
                           value="{{ old('first_name') }}" placeholder="例）太郎" required>
                    @error('first_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 姓（かな） --}}
                <div>
                    <label for="last_name_kana" class="block text-sm text-gray-700 mb-1">姓（かな）（＊必須）</label>
                    <input id="last_name_kana" name="last_name_kana" type="text"
                           class="block mt-1 w-full border rounded px-3 py-2"
                           value="{{ old('last_name_kana') }}" placeholder="例）やまだ" required>
                    @error('last_name_kana') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 名（かな） --}}
                <div>
                    <label for="first_name_kana" class="block text-sm text-gray-700 mb-1">名（かな）（＊必須）</label>
                    <input id="first_name_kana" name="first_name_kana" type="text"
                           class="block mt-1 w-full border rounded px-3 py-2"
                           value="{{ old('first_name_kana') }}" placeholder="例）たろう" required>
                    @error('first_name_kana') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 中学校名 --}}
                <div>
                    <label for="school" class="block text-sm text-gray-700 mb-1">中学校名（＊必須）</label>
                    <select id="school" name="school" class="block mt-1 w-full border rounded px-3 py-2" required>
                        <option value="">選択してください</option>
                        <option value="寄居中学校" {{ old('school')==='寄居中学校' ? 'selected' : '' }}>寄居中学校</option>
                        <option value="男衾中学校" {{ old('school')==='男衾中学校' ? 'selected' : '' }}>男衾中学校</option>
                        <option value="城南中学校" {{ old('school')==='城南中学校' ? 'selected' : '' }}>城南中学校</option>
                    </select>
                    @error('school') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 学年 --}}
                <div>
                    <label for="grade" class="block text-sm text-gray-700 mb-1">学年（＊必須）</label>
                    <select id="grade" name="grade" class="block mt-1 w-full border rounded px-3 py-2" required>
                        <option value="">選択してください</option>
                        <option value="1年生" {{ old('grade')==='1年生' ? 'selected' : '' }}>1年生</option>
                        <option value="2年生" {{ old('grade')==='2年生' ? 'selected' : '' }}>2年生</option>
                        <option value="3年生" {{ old('grade')==='3年生' ? 'selected' : '' }}>3年生</option>
                    </select>
                    @error('grade') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 組（※ name="class" は既存DBに合わせています） --}}
                <div>
                    <label for="class" class="block text-sm text-gray-700 mb-1">組（＊必須）</label>
                    <select id="class" name="class" class="block mt-1 w-full border rounded px-3 py-2" required>
                        <option value="">選択してください</option>
                        <option value="1組" {{ old('class')==='1組' ? 'selected' : '' }}>1組</option>
                        <option value="2組" {{ old('class')==='2組' ? 'selected' : '' }}>2組</option>
                        <option value="3組" {{ old('class')==='3組' ? 'selected' : '' }}>3組</option>
                    </select>
                    @error('class') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 電話番号 --}}
                <div>
                    <label for="phone" class="block text-sm text-gray-700 mb-1">電話番号（＊必須）</label>
                    <input id="phone" name="phone" type="text"
                           class="block mt-1 w-full border rounded px-3 py-2"
                           value="{{ old('phone') }}" required inputmode="numeric"
                           pattern="^0\d{1,4}-\d{1,4}-\d{4}$" placeholder="例）09012345678">
                    <p class="text-sm text-gray-500 mt-1">※ハイフン（-）は入力不要です。自動で挿入されます。</p>
                    @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 受講形式 --}}
                <div>
                    <label for="lesson_type" class="block text-sm text-gray-700 mb-1">受講形式（＊必須）</label>
                    <select id="lesson_type" name="lesson_type" class="block mt-1 w-full border rounded px-3 py-2" required>
                        <option value="">選択してください</option>
                        <option value="対面" {{ old('lesson_type')==='対面' ? 'selected' : '' }}>対面</option>
                        <option value="オンライン" {{ old('lesson_type')==='オンライン' ? 'selected' : '' }}>オンライン</option>
                        <option value="オンデマンド" {{ old('lesson_type')==='オンデマンド' ? 'selected' : '' }}>オンデマンド</option>
                    </select>
                    @error('lesson_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 受講時間 --}}
                <div>
                    <span class="block text-sm text-gray-700 mb-1">受講時間（＊必須）</span>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="lesson_time" value="午前" required
                                   {{ old('lesson_time') === '午前' ? 'checked' : '' }}>
                            <span class="ml-2">午前（9:00-11:50）</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="lesson_time" value="午後" required
                                   {{ old('lesson_time') === '午後' ? 'checked' : '' }}>
                            <span class="ml-2">午後（12:30-15:20）</span>
                        </label>
                    </div>
                    @error('lesson_time') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- 英検参加 --}}
                <div>
                    <label for="eiken" class="block text-sm text-gray-700 mb-1">英検参加（＊必須）</label>
                    <select id="eiken" name="eiken" class="block mt-1 w-full border rounded px-3 py-2" required>
                        <option value="">選択してください</option>
                        <option value="準1級" {{ old('eiken')==='準1級' ? 'selected' : '' }}>準1級</option>
                        <option value="2級"   {{ old('eiken')==='2級'   ? 'selected' : '' }}>2級</option>
                        <option value="準2級" {{ old('eiken')==='準2級' ? 'selected' : '' }}>準2級</option>
                        <option value="3級"   {{ old('eiken')==='3級'   ? 'selected' : '' }}>3級</option>
                        <option value="4級"   {{ old('eiken')==='4級'   ? 'selected' : '' }}>4級</option>
                        <option value="5級"   {{ old('eiken')==='5級'   ? 'selected' : '' }}>5級</option>
                        <option value="参加しない" {{ old('eiken')==='参加しない' ? 'selected' : '' }}>参加しない</option>
                    </select>
                    @error('eiken') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- メールアドレス --}}
                <div>
                    <label for="email" class="block text-sm text-gray-700 mb-1">メールアドレス（＊必須）</label>
                    <input id="email" name="email" type="email"
                           class="block mt-1 w-full border rounded px-3 py-2"
                           value="{{ old('email') }}" required autocomplete="email" inputmode="email">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- パスワード --}}
                <div>
                    <label for="password" class="block text-sm text-gray-700 mb-1">パスワード（＊必須）</label>
                    <input id="password" name="password" type="password"
                           class="block mt-1 w-full border rounded px-3 py-2"
                           required autocomplete="new-password">
                    @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- パスワード（確認） --}}
                <div>
                    <label for="password_confirmation" class="block text-sm text-gray-700 mb-1">パスワード（確認）（＊必須）</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="block mt-1 w-full border rounded px-3 py-2"
                           required autocomplete="new-password">
                    @error('password_confirmation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- その他（任意） --}}
                <div>
                    <label for="other" class="block text-sm text-gray-700 mb-1">その他（任意）</label>
                    <textarea id="other" name="other" rows="4"
                              class="block mt-1 w-full border rounded px-3 py-2"
                              placeholder="特記事項があれば記入してください">{{ old('other') }}</textarea>
                    @error('other') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- ボタン --}}
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
                        既に登録済みですか？
                    </a>
                    <button type="submit"
                            class="ms-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded">
                        登録
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tailwind CDN（開発用。ビルド済みなら不要） -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        // 電話番号の自動整形（数字以外を除去してからフォーマット）
        document.addEventListener('DOMContentLoaded', function () {
            const phoneInput = document.getElementById('phone');
            if (!phoneInput) return;

            function formatPhone() {
                let raw = phoneInput.value.replace(/[^\d]/g, '');

                // 携帯/050系
                if (/^(090|080|070|050)/.test(raw)) {
                    if (raw.length > 3 && raw.length <= 7) {
                        phoneInput.value = raw.slice(0, 3) + '-' + raw.slice(3);
                    } else if (raw.length > 7) {
                        phoneInput.value = raw.slice(0, 3) + '-' + raw.slice(3, 7) + '-' + raw.slice(7, 11);
                    } else {
                        phoneInput.value = raw;
                    }
                // 市外局番048/049など3桁想定
                } else if (/^(048|049)/.test(raw)) {
                    if (raw.length > 3 && raw.length <= 6) {
                        phoneInput.value = raw.slice(0, 3) + '-' + raw.slice(3);
                    } else if (raw.length > 6) {
                        phoneInput.value = raw.slice(0, 3) + '-' + raw.slice(3, 6) + '-' + raw.slice(6, 10);
                    } else {
                        phoneInput.value = raw;
                    }
                } else {
                    // それ以外は無加工（サーバ側バリデーションで許否）
                    phoneInput.value = raw;
                }
            }

            phoneInput.addEventListener('input', formatPhone);
            phoneInput.addEventListener('blur', formatPhone);
        });
    </script>
</body>
</html>
