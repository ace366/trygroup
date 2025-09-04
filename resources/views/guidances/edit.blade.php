<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-xl font-bold mb-4">指導・宿題 修正フォーム</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('guidances.update', $guidance->id) }}">
            @csrf
            @method('PATCH')

            <input type="hidden" name="student_id" value="{{ $student->id }}">

            <div class="mb-4">
                <label class="block font-semibold">生徒名</label>
                <input type="text" value="{{ $student->last_name }} {{ $student->first_name }}"
                       class="w-full border rounded p-2 bg-gray-100" readonly>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">登録日</label>
                <input type="date" name="registered_at" class="w-full border rounded p-2"
                       value="{{ old('registered_at', $guidance->registered_at) }}">
            </div>

            <div class="mb-4">
                <label class="block font-semibold">講座内容</label>
                <select name="course_type" class="w-full border rounded p-2">
                    <option value="土曜塾" {{ $guidance->course_type == '土曜塾' ? 'selected' : '' }}>土曜塾</option>
                    <option value="英検対策" {{ $guidance->course_type == '英検対策' ? 'selected' : '' }}>英検対策</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">時間区分</label>
                <select name="time_zone" class="w-full border rounded p-2">
                    <option value="午前" {{ $guidance->time_zone == '午前' ? 'selected' : '' }}>午前</option>
                    <option value="午後" {{ $guidance->time_zone == '午後' ? 'selected' : '' }}>午後</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">グループ</label>
                <input type="text" name="group" class="w-full border rounded p-2"
                       value="{{ old('group', $guidance->group) }}">
            </div>

            <div class="mb-4">
                <label class="block font-semibold">教科</label>
                <input type="text" name="subject" class="w-full border rounded p-2"
                       value="{{ old('subject', $guidance->subject) }}">
            </div>

            <div class="mb-4">
                <label class="block font-semibold">単元</label>
                <input type="text" name="unit" class="w-full border rounded p-2"
                       value="{{ old('unit', $guidance->unit) }}">
            </div>

            <div class="mb-4">
                <label class="block font-semibold">指導内容</label>
                <textarea name="content" class="w-full border rounded p-2" rows="4">{{ old('content', $guidance->content) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">授業態度・雰囲気</label>
                <textarea name="attitude" class="w-full border rounded p-2" rows="4">{{ old('attitude', $guidance->attitude) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">理解度</label>
                <select name="understanding_level" class="w-full border rounded p-2">
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ $guidance->understanding_level == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">集中度</label>
                <select name="concentration_level" class="w-full border rounded p-2">
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ $guidance->concentration_level == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">宿題内容</label>
                <textarea name="homework" class="w-full border rounded p-2" rows="4">{{ old('homework', $guidance->homework) }}</textarea>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">
                更新する
            </button>
            <a href="{{ route('guidances.history', ['student_id' => $student->id]) }}"
                class="inline-block ml-4 text-gray-600 underline hover:text-blue-700">
                戻る
            </a>
        </form>
    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>