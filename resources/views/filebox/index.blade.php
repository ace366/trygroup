{{-- resources/views/filebox/index.blade.php（最新） --}}
<x-app-layout>
    @php
        // 現在表示中フォルダID（コントローラの $currentFolder 優先、無ければ ?folder クエリ）
        $currentFolderId = isset($currentFolder) && $currentFolder?->id
            ? (int) $currentFolder->id
            : (is_numeric(request('folder')) ? (int) request('folder') : null);
        $parentFolderId = isset($currentFolder) ? ($currentFolder->parent_id ?? null) : null;
        $isStaff = in_array(auth()->user()->role ?? 'user', ['admin','teacher'], true);
    @endphp

    <div class="container mx-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">📂 共有ファイルBOX</h1>

            {{-- 戻るボタン --}}
            @if($parentFolderId)
                <a href="{{ route('filebox.index', ['folder' => $parentFolderId]) }}"
                   class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-sm">
                    ← 親フォルダへ戻る
                </a>
            @elseif($currentFolderId)
                <a href="{{ route('filebox.index') }}"
                   class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-sm">
                    ← ルートへ戻る
                </a>
            @endif
        </div>

        {{-- パンくず --}}
        <nav class="mb-2 text-sm text-gray-600">
            <a href="{{ route('filebox.index') }}" class="text-blue-600 hover:underline">ルート</a>
            @if(!empty($breadcrumbs))
                @foreach($breadcrumbs as $crumb)
                    / <a href="{{ route('filebox.index', ['folder' => $crumb->id]) }}" class="text-blue-600 hover:underline">
                        {{ $crumb->file_name }}
                      </a>
                @endforeach
            @endif
        </nav>

        {{-- 使い方案内（生徒にはリネーム/削除の説明を出さない） --}}
        <div class="mb-6 rounded border border-blue-200 bg-blue-50 text-blue-900 p-3 text-sm">
            <div class="font-semibold mb-1">使い方</div>
            <ul class="list-disc list-inside space-y-1">
                <li>
                    フォルダに入る：
                    <span class="inline-flex items-center"><span class="mr-1">📁</span>フォルダ名</span>
                    または
                    <span class="inline-flex items-center">
                        <span class="px-2 py-0.5 rounded bg-blue-600 text-white text-xs">開く</span> ボタン
                    </span>
                    をクリック
                </li>
                <li>
                    ファイルをダウンロード：
                    <span class="px-2 py-0.5 rounded bg-green-600 text-white text-xs">DL</span> ボタンをクリック
                </li>

                @if($isStaff)
                    <li>
                        名前を変える：
                        <span class="px-2 py-0.5 rounded bg-yellow-500 text-white text-xs">名前変更</span>
                        → 入力 →
                        <span class="px-2 py-0.5 rounded bg-yellow-500 text-white text-xs">保存</span>
                    </li>
                    <li>
                        削除する：
                        <span class="px-2 py-0.5 rounded bg-red-600 text-white text-xs">削除</span> ボタン
                    </li>
                @endif
            </ul>
        </div>

        {{-- 管理者・講師のみ：アップロード / フォルダ作成 --}}
        @if($isStaff)
            <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- ファイルアップロード --}}
                <form method="POST" action="{{ route('filebox.store') }}" enctype="multipart/form-data"
                      class="bg-white p-4 rounded shadow">
                    @csrf
                    {{-- 現在のフォルダIDを必ず送る --}}
                    <input type="hidden" name="parent_id" value="{{ $currentFolderId }}">
                    <div class="mb-4">
                        <input type="file" name="file" class="border p-2 w-full rounded" required>
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <input type="checkbox" name="is_protected" value="1" id="is_protected" class="rounded">
                        <label for="is_protected">パスワード保護する</label>
                    </div>
                    <div class="mb-4">
                        <input type="text" name="password" placeholder="パスワード (任意)" class="border p-2 w-full rounded">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        アップロード
                    </button>
                </form>

                {{-- フォルダ作成 --}}
                <form method="POST" action="{{ route('filebox.folder') }}" class="bg-white p-4 rounded shadow">
                    @csrf
                    {{-- 現在のフォルダIDを必ず送る --}}
                    <input type="hidden" name="parent_id" value="{{ $currentFolderId }}">
                    <div class="mb-4">
                        <input type="text" name="folder_name" placeholder="フォルダ名"
                               class="border p-2 w-full rounded" required>
                    </div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                        フォルダ作成
                    </button>
                </form>
            </div>
        @endif

        {{-- 一覧表示 --}}
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-3 border">名前</th>
                        <th class="p-3 border">投稿者</th>
                        <th class="p-3 border">日時</th>
                        <th class="p-3 border">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $file)
                        <tr class="hover:bg-gray-50 align-top">
                            {{-- 名前列 --}}
                            <td class="p-3 border">
                                @if($file->type === 'folder')
                                    {{-- フォルダ：リンク＋「開く」ボタン＋（スタッフのみ）トグル式リネーム --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('filebox.index', ['folder' => $file->id]) }}"
                                           class="text-blue-600 font-semibold hover:underline">
                                            📁 {{ $file->file_name }}
                                        </a>
                                        <a href="{{ route('filebox.index', ['folder' => $file->id]) }}"
                                           class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-600 text-white hover:bg-blue-700">
                                            開く
                                        </a>
                                        @if($isStaff)
                                            <button type="button"
                                                    class="text-xs px-2 py-1 rounded bg-yellow-500 text-white hover:bg-yellow-600"
                                                    onclick="document.getElementById('rename-{{ $file->id }}').classList.toggle('hidden')">
                                                名前変更
                                            </button>
                                        @endif
                                    </div>

                                    @if($isStaff)
                                        <form id="rename-{{ $file->id }}" method="POST" action="{{ route('filebox.rename', $file) }}"
                                              class="mt-2 hidden">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex items-center gap-2">
                                                <input type="text" name="file_name" value="{{ $file->file_name }}"
                                                       class="border p-1 rounded text-sm w-48" aria-label="フォルダ名">
                                                <button type="submit"
                                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-sm">
                                                    保存
                                                </button>
                                                <button type="button" class="px-2 py-1 text-sm"
                                                        onclick="document.getElementById('rename-{{ $file->id }}').classList.add('hidden')">
                                                    キャンセル
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                @else
                                    {{-- ファイル：スタッフのみインラインリネーム --}}
                                    @if($isStaff)
                                        <form method="POST" action="{{ route('filebox.rename', $file) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="file_name" value="{{ $file->file_name }}"
                                                   class="border p-1 rounded text-sm w-48">
                                            <button type="submit"
                                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-sm">
                                                変更
                                            </button>
                                        </form>
                                    @else
                                        {{ $file->file_name }}
                                    @endif
                                @endif
                            </td>

                            {{-- 投稿者・日時 --}}
                            <td class="p-3 border">{{ $file->user->last_name }}</td>
                            <td class="p-3 border">{{ $file->created_at->format('Y-m-d H:i') }}</td>

                            {{-- 操作列 --}}
                            <td class="p-3 border space-x-2">
                                @if($file->type === 'file')
                                    {{-- ダウンロード --}}
                                    @if($file->is_protected)
                                        <form method="POST" action="{{ route('filebox.download', $file->id) }}" class="inline-flex items-center gap-2">
                                            @csrf
                                            <input type="password" name="password" placeholder="パスワード"
                                                   class="border p-1 rounded text-sm">
                                            <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                DL
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('filebox.download', $file->id) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                DL
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if($isStaff)
                                    {{-- 削除（ファイル／フォルダ共通） --}}
                                    <form method="POST"
                                          action="{{ route('filebox.destroy', $file) }}"
                                          class="inline"
                                          onsubmit="return confirm('削除してよろしいですか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                            削除
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-3 text-center text-gray-500">
                                まだファイルやフォルダがありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tailwind CDN（開発用） --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
</x-app-layout>
