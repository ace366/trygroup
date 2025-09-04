<x-app-layout>
    <div class="max-w-2xl mx-auto py-10">
        <h2 class="text-2xl font-bold mb-6">動画情報の登録</h2>

        @if (session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('videos.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf

            <!-- 学年 -->
            <div class="mb-4">
                <label for="grade" class="block font-semibold">学年</label>
                <select name="grade" id="grade" class="w-full border border-gray-300 p-2 rounded">
                    <option value="1年生">1年生</option>
                    <option value="2年生">2年生</option>
                    <option value="3年生">3年生</option>
                </select>
            </div>

            <!-- 科目 -->
            <div class="mb-4">
                <label for="subject" class="block font-semibold">科目</label>
                <select name="subject" id="subject" class="w-full border border-gray-300 p-2 rounded">
                    <option value="国語">国語</option>
                    <option value="数学">数学</option>
                    <option value="英語">英語</option>
                    <option value="英検準2級">英検準2級</option>
                    <option value="英検3級">英検3級</option>
                    <option value="英検4級">英検4級</option>
                    <option value="英検5級">英検5級</option>
                    <option value="全国学力調査問題">全国学力調査問題</option>
                    <option value="理科">理科</option>
                    <option value="社会">社会</option>
                    <option value="その他">その他</option>
                </select>
            </div>

            <!-- 単元 -->
            <div class="mb-4">
                <label for="unit" class="block font-semibold">単元(例：〇月〇日実施　因数分解　1/3)</label>
                <input type="text" name="unit" id="unit" class="w-full border border-gray-300 p-2 rounded" required>
            </div>

            <!-- YouTube URL -->
            <div class="mt-4">
                <x-input-label for="youtube_url" :value="__('YouTube URL (https://youtu.be/動画ID)')" />
                <x-text-input id="youtube_url" class="block mt-1 w-full" type="url" name="youtube_url" required />
            </div>

            <!-- 送信ボタン -->
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">登録</button>
        </form>
    </div>
<!-- 動画一覧 -->
<div class="mt-10 bg-white p-6 rounded-lg shadow-md px-4 sm:px-8">
    <h3 class="text-xl font-bold mb-4">登録済み動画一覧</h3>

    @if ($videos->count())
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100 text-sm text-left">
                    <th class="border p-2">保存日時</th>
                    <th class="border p-2">学年</th>
                    <th class="border p-2">科目</th>
                    <th class="border p-2">単元</th>
                    <th class="border p-2">URL</th>
                    <th class="border p-2 text-center">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($videos as $video)
                    <tr class="text-sm">
                        <td class="border p-2 text-gray-600">
                            {{ $video->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="border p-2">{{ $video->grade }}</td>
                        <td class="border p-2">{{ $video->subject }}</td>
                        <td class="border p-2">{{ $video->unit }}</td>
                        <td class="border p-2">
                            <a href="{{ $video->youtube_url }}" target="_blank" class="text-blue-500 underline break-all">リンク</a>
                        </td>
                        <td class="border p-2">
                            <div class="flex flex-wrap justify-center gap-2">
                                <a href="{{ route('videos.edit', $video->id) }}"
                                   class="bg-orange-400 hover:bg-orange-500 text-white text-xs font-semibold px-3 py-1 rounded">
                                    編集
                                </a>
                                <form action="{{ route('videos.destroy', $video->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-3 py-1 rounded">
                                        削除
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-600">まだ動画は登録されていません。</p>
    @endif
</div>


</x-app-layout>

<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>

