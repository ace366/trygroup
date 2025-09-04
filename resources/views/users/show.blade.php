<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6">ユーザー詳細</h2>

        <div class="bg-white shadow rounded p-6">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <tbody>
                    <tr>
                        <th class="border px-4 py-2 text-left w-1/4">ID</th>
                        <td class="border px-4 py-2">{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">名前</th>
                        <td class="border px-4 py-2">{{ $user->last_name }} {{ $user->first_name }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">かな</th>
                        <td class="border px-4 py-2">{{ $user->last_name_kana }} {{ $user->first_name_kana }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">学校</th>
                        <td class="border px-4 py-2">{{ $user->school }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">学年</th>
                        <td class="border px-4 py-2">{{ $user->grade }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">組</th>
                        <td class="border px-4 py-2">{{ $user->class }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">英検</th>
                        <td class="border px-4 py-2">{{ $user->eiken }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">授業タイプ</th>
                        <td class="border px-4 py-2">{{ $user->lesson_type }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">授業時間</th>
                        <td class="border px-4 py-2">{{ $user->lesson_time }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">電話番号</th>
                        <td class="border px-4 py-2">{{ $user->phone }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">メール</th>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">特記事項</th>
                        <td class="border px-4 py-2 whitespace-pre-line">{{ $user->other }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">登録日</th>
                        <td class="border px-4 py-2">{{ $user->created_at }}</td>
                    </tr>
                    <tr>
                        <th class="border px-4 py-2">更新日</th>
                        <td class="border px-4 py-2">{{ $user->updated_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <a href="{{ route('users.index') }}" 
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">一覧に戻る</a>
        </div>
    </div>
</x-app-layout>
