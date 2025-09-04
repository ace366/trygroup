<x-app-layout>
    <div class="max-w-2xl mx-auto py-10">
        <h2 class="text-2xl font-bold mb-6">動画情報の編集</h2>

        @if ($errors->any())
            <div class="p-4 mb-4 text-red-800 bg-red-100 border border-red-300 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('videos.update', $video->id) }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')

            <!-- 学年 -->
            <div class="mb-4">
                <label for="grade" class="block font-semibold">学年</label>
                <select name="grade" id="grade" class="w-full border border-gray-300 p-2 rounded">
                    @foreach(['1年生', '2年生', '3年生'] as $grade)
                        <option value="{{ $grade }}" {{ $video->grade === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 科目 -->
            <div class="mb-4">
                <label for="subject" class="block font-semibold">科目</label>
                <select name="subject" id="subject" class="w-full border border-gray-300 p-2 rounded">
                    @foreach([
                        '国語', '数学', '英語', '英検準2級', '英検3級', '英検4級',
                        '英検5級', '全国学力調査問題', '理科', '社会'
                    ] as $subject)
                        <option value="{{ $subject }}" {{ $video->subject === $subject ? 'selected' : '' }}>{{ $subject }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 単元 -->
            <div class="mb-4">
                <label for="unit" class="block font-semibold">単元</label>
                <input type="text" name="unit" id="unit" value="{{ $video->unit }}" class="w-full border border-gray-300 p-2 rounded" required>
            </div>

            <!-- YouTube URL -->
            <div class="mb-4">
                <label for="youtube_url" class="block font-semibold">YouTube URL</label>
                <input type="url" name="youtube_url" id="youtube_url" value="{{ $video->youtube_url }}" class="w-full border border-gray-300 p-2 rounded" required>
            </div>

            <!-- ボタン -->
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('videos.create') }}" class="text-blue-500 hover:underline">← 登録画面に戻る</a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    更新する
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
