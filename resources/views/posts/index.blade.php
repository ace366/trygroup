<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">お知らせ一覧</h1>

        @auth
            @if(auth()->user()->role === 'admin') 
                <!-- 新規作成ボタン (admin のみ表示) -->
                <div class="text-right mb-4">
                    <a href="{{ route('posts.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                        新規作成
                    </a>
                </div>
            @endif
        @endauth

        <div class="bg-white shadow-lg rounded-lg p-6">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2">内容</th> <!-- 横幅を狭くする -->
                        <th class="border border-gray-300 px-4 py-2">作成日</th>
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <th class="border border-gray-300 px-4 py-2">操作</th>
                            @endif
                        @endauth
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr class="text-center">
                            <td class="border border-gray-300 px-4 py-2 break-all text-left"> <!-- ✅ break-all で折り返し強制 -->
                                <h1 class="text-2xl font-bold text-center">{{ $post->title }}</h1>
                                    {!! $post->content !!}
                                </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $post->created_at->format('Y-m-d') }}</td>
                            @auth
                                @if(auth()->user()->role === 'admin')
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="{{ route('posts.edit', $post->id) }}" class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                                            編集
                                        </a>
                                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('削除しますか？');" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                                                削除
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            @endauth
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">お知らせがありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>