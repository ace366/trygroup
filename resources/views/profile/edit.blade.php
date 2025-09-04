<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <!-- 姓 -->
                        <div>
                            <x-input-label for="last_name" :value="__('姓')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required autofocus />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>

                        <!-- 名 -->
                        <div>
                            <x-input-label for="first_name" :value="__('名')" />
                            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <!-- 姓（かな） -->
                        <div>
                            <x-input-label for="last_name_kana" :value="__('姓（かな）')" />
                            <x-text-input id="last_name_kana" class="block mt-1 w-full" type="text" name="last_name_kana" value="{{ old('last_name_kana', auth()->user()->last_name_kana) }}" required />
                            <x-input-error :messages="$errors->get('last_name_kana')" class="mt-2" />
                        </div>

                        <!-- 名（かな） -->
                        <div>
                            <x-input-label for="first_name_kana" :value="__('名（かな）')" />
                            <x-text-input id="first_name_kana" class="block mt-1 w-full" type="text" name="first_name_kana" value="{{ old('first_name_kana', auth()->user()->first_name_kana) }}" required />
                            <x-input-error :messages="$errors->get('first_name_kana')" class="mt-2" />
                        </div>

                        <!-- 中学校名 -->
                        <div>
                            <x-input-label for="school" :value="__('中学校名')" />
                            <select id="school" name="school" class="block mt-1 w-full" required>
                                <option value="寄居中学校" {{ old('school', auth()->user()->school) == '寄居中学校' ? 'selected' : '' }}>寄居中学校</option>
                                <option value="男衾中学校" {{ old('school', auth()->user()->school) == '男衾中学校' ? 'selected' : '' }}>男衾中学校</option>
                                <option value="城南中学校" {{ old('school', auth()->user()->school) == '城南中学校' ? 'selected' : '' }}>城南中学校</option>
                            </select>
                            <x-input-error :messages="$errors->get('school')" class="mt-2" />
                        </div>

                        <!-- 学年 -->
                        <div>
                            <x-input-label for="grade" :value="__('学年')" />
                            <select id="grade" name="grade" class="block mt-1 w-full" required>
                                <option value="1年生" {{ old('grade', auth()->user()->grade) == '1年生' ? 'selected' : '' }}>1年生</option>
                                <option value="2年生" {{ old('grade', auth()->user()->grade) == '2年生' ? 'selected' : '' }}>2年生</option>
                                <option value="3年生" {{ old('grade', auth()->user()->grade) == '3年生' ? 'selected' : '' }}>3年生</option>
                            </select>
                            <x-input-error :messages="$errors->get('grade')" class="mt-2" />
                        </div>

                        <!-- 組 -->
                        <div>
                            <x-input-label for="class" :value="__('組')" />
                            <select id="class" name="class" class="block mt-1 w-full" required>
                                <option value="1組" {{ old('class', auth()->user()->class) == '1組' ? 'selected' : '' }}>1組</option>
                                <option value="2組" {{ old('class', auth()->user()->class) == '2組' ? 'selected' : '' }}>2組</option>
                                <option value="3組" {{ old('class', auth()->user()->class) == '3組' ? 'selected' : '' }}>3組</option>
                            </select>
                            <x-input-error :messages="$errors->get('class')" class="mt-2" />
                        </div>

                        <!-- 電話番号 -->
                        <div>
                            <x-input-label for="phone" :value="__('電話番号')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- メールアドレス -->
                        <div>
                            <x-input-label for="email" :value="__('メールアドレス')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- 受講形式 -->
                        <div>
                            <x-input-label for="lesson_type" :value="__('受講形式')" />
                            <select id="lesson_type" name="lesson_type" class="block mt-1 w-full" required>
                                <option value="対面" {{ old('lesson_type', auth()->user()->lesson_type) == '対面' ? 'selected' : '' }}>対面</option>
                                <option value="オンライン" {{ old('lesson_type', auth()->user()->lesson_type) == 'オンライン' ? 'selected' : '' }}>オンライン</option>
                                <option value="オンデマンド" {{ old('lesson_type', auth()->user()->lesson_type) == 'オンデマンド' ? 'selected' : '' }}>オンデマンド</option>
                            </select>
                            <x-input-error :messages="$errors->get('lesson_type')" class="mt-2" />
                        </div>
                        <!-- 受講時間 -->
                        <div>
                            <x-input-label for="lesson_time" :value="__('受講時間')" />
                            <div class="flex gap-4">
                                <label>
                                    <input type="radio" name="lesson_time" value="午前" required {{ old('lesson_time', auth()->user()->lesson_time) == '午前' ? 'checked' : '' }}>
                                    午前（9:00-11:50）
                                </label>
                                <label>
                                    <input type="radio" name="lesson_time" value="午後" required {{ old('lesson_time', auth()->user()->lesson_time) == '午後' ? 'checked' : '' }}>
                                    午後（12:30-15:20）
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('lesson_time')" class="mt-2" />
                        </div>

                        <!-- 英検受験 -->
                        <div>
                            <x-input-label for="eiken" :value="__('英検受験')" />
                            <select id="eiken" name="eiken" class="block mt-1 w-full" required>
                                <option value="なし" {{ old('eiken', auth()->user()->eiken) == 'なし' ? 'selected' : '' }}>なし</option>
                                <option value="準1級" {{ old('eiken', auth()->user()->eiken) == '準1級' ? 'selected' : '' }}>準1級</option>
                                <option value="2級" {{ old('eiken', auth()->user()->eiken) == '2級' ? 'selected' : '' }}>2級</option>
                                <option value="準2級" {{ old('eiken', auth()->user()->eiken) == '準2級' ? 'selected' : '' }}>準2級</option>
                                <option value="3級" {{ old('eiken', auth()->user()->eiken) == '3級' ? 'selected' : '' }}>3級</option>
                                <option value="4級" {{ old('eiken', auth()->user()->eiken) == '4級' ? 'selected' : '' }}>4級</option>
                                <option value="5級" {{ old('eiken', auth()->user()->eiken) == '5級' ? 'selected' : '' }}>5級</option>
                            </select>
                            <x-input-error :messages="$errors->get('eiken')" class="mt-2" />
                        </div>

                        <!-- 更新ボタン -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('更新') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>