<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">お知らせ編集</h1>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <form method="POST" action="{{ route('posts.update', $post->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-bold">タイトル</label>
                    <input type="text" name="title" id="title" class="w-full border-gray-300 rounded-md p-2" value="{{ old('title', $post->title) }}" required>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-gray-700 font-bold">内容</label>
                    <textarea name="content" id="content" rows="5" class="w-full border-gray-300 rounded-md p-2" required>{{ old('content', $post->content) }}</textarea>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md shadow-md hover:bg-blue-600 transition">
                    更新
                </button>
                <a href="{{ route('posts.index') }}" class="ml-4 px-4 py-2 bg-gray-500 text-white rounded-md shadow-md hover:bg-gray-600 transition">
                    戻る
                </a>
            </form>
        </div>
    </div>
</x-app-layout>
