<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">ユーザー一覧</h1>

        <!-- フィルタリングフォーム -->
        <form method="GET" action="{{ route('users.index') }}" class="mb-4 flex flex-wrap gap-4 justify-center">
            <select name="school" class="p-2 border rounded-md">
                <option value="">学校を選択</option>
                <option value="寄居中学校" {{ request('school') == '寄居中学校' ? 'selected' : '' }}>寄居中学校</option>
                <option value="男衾中学校" {{ request('school') == '男衾中学校' ? 'selected' : '' }}>男衾中学校</option>
                <option value="城南中学校" {{ request('school') == '城南中学校' ? 'selected' : '' }}>城南中学校</option>
            </select>

            <select name="grade" class="p-2 border rounded-md">
                <option value="">学年を選択</option>
                <option value="1年生" {{ request('grade') == '1年生' ? 'selected' : '' }}>1年生</option>
                <option value="2年生" {{ request('grade') == '2年生' ? 'selected' : '' }}>2年生</option>
                <option value="3年生" {{ request('grade') == '3年生' ? 'selected' : '' }}>3年生</option>
            </select>

            <select name="eiken" class="p-2 border rounded-md">
                <option value="">英検級を選択</option>
                <option value="5級" {{ request('eiken') == '5級' ? 'selected' : '' }}>5級</option>
                <option value="4級" {{ request('eiken') == '4級' ? 'selected' : '' }}>4級</option>
                <option value="3級" {{ request('eiken') == '3級' ? 'selected' : '' }}>3級</option>
                <option value="準2級" {{ request('eiken') == '準2級' ? 'selected' : '' }}>準2級</option>
                <option value="2級" {{ request('eiken') == '2級' ? 'selected' : '' }}>2級</option>
                <option value="準1級" {{ request('eiken') == '準1級' ? 'selected' : '' }}>準1級</option>
            </select>

            <select name="lesson_time" class="p-2 border rounded-md">
                <option value="">授業時間を選択</option>
                <option value="午前" {{ request('lesson_time') == '午前' ? 'selected' : '' }}>午前</option>
                <option value="午後" {{ request('lesson_time') == '午後' ? 'selected' : '' }}>午後</option>
            </select>

            <select name="lesson_type" class="p-2 border rounded-md">
                <option value="">参加タイプを選択</option>
                <option value="対面" {{ request('lesson_type') == '対面' ? 'selected' : '' }}>対面</option>
                <option value="オンライン" {{ request('lesson_type') == 'オンライン' ? 'selected' : '' }}>オンライン</option>
                <option value="オンデマンド"{{ request('lesson_type') == 'オンデマンド' ? 'selected' : '' }}>オンデマンド</option>
                <option value="オンライン・オンデマンド" {{ request('lesson_type') == 'オンライン・オンデマンド' ? 'selected' : '' }}>オンライン・オンデマンド</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">フィルター</button>
        </form>

        <!-- CSVダウンロードボタン -->
        <div class="text-center mb-4">
            <a href="{{ route('users.csv', request()->query()) }}" class="bg-green-500 text-white px-4 py-2 rounded-md">
                CSVダウンロード
            </a>
        </div>
        <!-- 🎯 ユーザーカード印刷ボタン（追加！） -->
        <div class="text-center mb-8">
            <a href="{{ route('users.card', request()->query()) }}" class="bg-purple-500 hover:bg-purple-700 text-white px-6 py-2 rounded-md font-semibold">
                ユーザーカード印刷ページへ
            </a>
        </div>
        <!-- ユーザー一覧表示 -->
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    @php
                        $currentSort = request('sort', 'id');
                        $currentDirection = request('direction', 'asc');
                        $reverseDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
                    @endphp

                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => $currentSort === 'id' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            ID {!! $currentSort === 'id' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'last_name_kana', 'direction' => $currentSort === 'last_name_kana' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            名前 {!! $currentSort === 'last_name_kana' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'school', 'direction' => $currentSort === 'school' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            学校 {!! $currentSort === 'school' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'grade', 'direction' => $currentSort === 'grade' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            学年 {!! $currentSort === 'grade' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'eiken', 'direction' => $currentSort === 'eiken' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            英検 {!! $currentSort === 'eiken' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'lesson_time', 'direction' => $currentSort === 'lesson_time' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            授業時間 {!! $currentSort === 'lesson_time' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'lesson_type', 'direction' => $currentSort === 'lesson_type' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            タイプ {!! $currentSort === 'lesson_type' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'phone', 'direction' => $currentSort === 'phone' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            電話番号 {!! $currentSort === 'phone' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'email', 'direction' => $currentSort === 'email' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            メールアドレス {!! $currentSort === 'email' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'created_at', 'direction' => $currentSort === 'created_at' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            登録日 {!! $currentSort === 'created_at' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">
                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'updated_at', 'direction' => $currentSort === 'updated_at' && $currentDirection === 'asc' ? 'desc' : 'asc'])) }}">
                            更新日 {!! $currentSort === 'updated_at' ? ($currentDirection === 'asc' ? '▲' : '▼') : '' !!}
                        </a>
                    </th>
                    <th class="border p-2">権限</th>
                    @if(auth()->user()->role === 'admin')
                        <th class="border p-2">削除</th>
                    @endif
                </tr>


            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="border p-2">{{ $user->id }}</td>
                        <td class="border p-2">{{ $user->last_name }} {{ $user->first_name }} <br>({{ $user->last_name_kana }} {{ $user->first_name_kana }})</td>
                        <td class="border p-2">{{ $user->school }}</td>
                        <td class="border p-2">{{ $user->grade }}</td>
                        <td class="border p-2">{{ $user->eiken }}</td>
                        <td class="border p-2">{{ $user->lesson_time }}</td>
                        <td class="border p-2">{{ $user->lesson_type }}</td>
                        <td class="border p-2">{{ $user->phone }}</td>
                        <td class="border p-2">{{ $user->email }}</td>
                        <td class="border p-2">{{ $user->created_at }}</td>
                        <td class="border p-2">{{ $user->updated_at }}</td>
                        <td class="border p-2">
                            @if(auth()->user()->role === 'admin')
                                <form method="POST" action="{{ route('users.updateRole', $user->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>admin</option>
                                        <option value="teacher" {{ $user->role === 'teacher' ? 'selected' : '' }}>teacher</option>
                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>user</option>
                                        <option value="editor" {{ $user->role === 'editor' ? 'selected' : '' }}>editor</option>
                                    </select>
                                </form>
                            @else
                                {{ $user->role }}
                            @endif
                        </td>
                        @if(auth()->user()->role === 'admin')
                            <td class="border p-2 text-center">
                                <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">削除</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
