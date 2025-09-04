<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-xl font-bold mb-4">指導・宿題 記録フォーム</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('guidances.store') }}">
            @csrf

            {{-- hiddenで生徒IDと学校名を送信 --}}
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <input type="hidden" name="school_name" value="{{ $student->school }}">

            {{-- 表示のみ：生徒名 --}}
            <div class="mb-4">
                <label class="block font-semibold">生徒名</label>
                <input type="text" value="{{ $student->last_name }} {{ $student->first_name }}"
                       class="w-full border rounded p-2 bg-gray-100" readonly>
            </div>

            {{-- 表示のみ：学校名 --}}
            <div class="mb-4">
                <label class="block font-semibold">学校名</label>
                <input type="text" value="{{ $student->school }}"
                       class="w-full border rounded p-2 bg-gray-100" readonly>
            </div>

            {{-- 登録日 --}}
            <div class="mb-4">
                <label class="block font-semibold">登録日</label>
                <input type="date" name="registered_at" class="w-full border rounded p-2"
                       value="{{ old('registered_at', \Carbon\Carbon::today()->toDateString()) }}">
            </div>

            {{-- 講師名（ログイン中のユーザー） --}}
            <div class="mb-4">
                <label class="block font-semibold">講師名</label>
                <input type="text"
                    value="{{ Auth::user()->last_name }} {{ Auth::user()->first_name }}"
                    class="w-full border rounded p-2 bg-gray-100" readonly>
            </div>

            {{-- 講座内容 --}}
            <div class="mb-4">
                <label class="block font-semibold">講座内容</label>
                <select name="course_type" class="w-full border rounded p-2">
                    <option value="土曜塾">土曜塾</option>
                    <option value="英検対策">英検対策</option>
                </select>
            </div>

            {{-- 時間区分 --}}
            <div class="mb-4">
                <label class="block font-semibold">時間区分</label>
                <select name="time_zone" class="w-full border rounded p-2">
                    <option value="午前">午前</option>
                    <option value="午後">午後</option>
                </select>
            </div>

            {{-- グループ --}}
            <div class="mb-4">
                <label class="block font-semibold">グループ</label>
                <input type="text" name="group" class="w-full border rounded p-2"
                       placeholder="A, B, C, 準2級など" list="group-options">
                <datalist id="group-options">
                    <option value="A"><option value="B"><option value="C">
                    <option value="D"><option value="E"><option value="F"><option value="G">
                    <option value="H"><option value="I"><option value="J">
                    <option value="K"><option value="L">
                    <option value="準１級"><option value="２級"><option value="準２級プラス">
                    <option value="準２級"><option value="３級"><option value="４級"><option value="５級">
                </datalist>
            </div>

            {{-- 科目 --}}
            <div class="mb-4">
                <label class="block font-semibold">教科</label>
                <input type="text" name="subject" class="w-full border rounded p-2" placeholder="例：英語・数学など">
            </div>

            {{-- 単元 --}}
            <div class="mb-4">
                <label class="block font-semibold">単元</label>
                <input type="text" name="unit" class="w-full border rounded p-2">
            </div>

            {{-- 指導内容 --}}
            <div class="mb-4">
                <label class="block font-semibold">指導内容</label>
                <textarea name="content" class="w-full border rounded p-2" rows="4"></textarea>
            </div>

            {{-- 授業態度 --}}
            <div class="mb-4">
                <label class="block font-semibold">授業態度・雰囲気</label>
                <textarea name="attitude" class="w-full border rounded p-2" rows="4"></textarea>
            </div>

            {{-- 理解度 --}}
            <div class="mb-4">
                <label class="block font-semibold">理解度</label>
                <select name="understanding_level" class="w-full border rounded p-2">
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            {{-- 集中度 --}}
            <div class="mb-4">
                <label class="block font-semibold">集中度</label>
                <select name="concentration_level" class="w-full border rounded p-2">
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            {{-- 宿題内容 --}}
            <div class="mb-4">
                <label class="block font-semibold">宿題内容</label>
                <textarea name="homework" class="w-full border rounded p-2" rows="4"></textarea>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">
                登録する
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