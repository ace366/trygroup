<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
    </header>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <!-- 姓 -->
        <div>
            <x-input-label for="last_name" :value="__('姓')" />
            <x-text-input id="last_name" name="last_name" type="text" value="{{ old('last_name', auth()->user()->last_name) }}" required autofocus />
        </div>

        <!-- 名 -->
        <div>
            <x-input-label for="first_name" :value="__('名')" />
            <x-text-input id="first_name" name="first_name" type="text" value="{{ old('first_name', auth()->user()->first_name) }}" required />
        </div>

        <!-- 受講時間 -->
        <div>
            <x-input-label for="lesson_time" :value="__('受講時間')" />
            <select id="lesson_time" name="lesson_time" required>
                <option value="午前" {{ old('lesson_time', auth()->user()->lesson_time) == '午前' ? 'selected' : '' }}>午前（9:00-11:50）</option>
                <option value="午後" {{ old('lesson_time', auth()->user()->lesson_time) == '午後' ? 'selected' : '' }}>午後（12:30-15:20）</option>
            </select>
        </div>

        <!-- 英検受験 -->
        <div>
            <x-input-label for="eiken" :value="__('英検受験')" />
            <select id="eiken" name="eiken" required>
                <option value="なし" {{ old('eiken', auth()->user()->eiken) == 'なし' ? 'selected' : '' }}>なし</option>
                <option value="3級" {{ old('eiken', auth()->user()->eiken) == '3級' ? 'selected' : '' }}>3級</option>
                <option value="4級" {{ old('eiken', auth()->user()->eiken) == '4級' ? 'selected' : '' }}>4級</option>
                <option value="5級" {{ old('eiken', auth()->user()->eiken) == '5級' ? 'selected' : '' }}>5級</option>
            </select>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('更新') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600">{{ __('更新しました') }}</p>
            @endif
        </div>
    </form>
</section>
