<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">LINE メッセージ送信</h1>

        @if(session('success'))
            <div class="text-green-500">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="text-red-500">{{ session('error') }}</div>
        @endif

        <form action="{{ url('/line/message/send') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block font-semibold">登録済みLINEユーザー:</label>
                <select name="user_id" class="border p-2 w-full">
                    @foreach (\App\Models\LineUser::all() as $user)
                        <option value="{{ $user->line_user_id }}">{{ $user->line_user_id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">メッセージ内容:</label>
                <textarea name="message" rows="4" class="border p-2 w-full" required></textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">送信</button>
        </form>
    </div>
</x-app-layout>

<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>