<x-app-layout>
<style>
    body { background-color: #f3f4f6; }
    .chat-container { display:flex; flex-direction:column; height:calc(100vh - 100px); }
    .chat-box { flex:1; overflow-y:auto; padding:1rem; background:#f9fafb; border-radius:.5rem; }
    .message { display:flex; margin-bottom:1rem; align-items:flex-start; }
    .message.user { justify-content:flex-end; }
    .message.assistant { justify-content:flex-start; }
    .bubble { max-width:75%; padding:.6rem .8rem; border-radius:1rem; line-height:1.5; }
    .bubble.user { background:#3b82f6; color:#fff; border-bottom-right-radius:.25rem; }
    .bubble.assistant { background:#fff; color:#111; border:1px solid #e5e7eb; border-bottom-left-radius:.25rem; }
    .avatar { width:40px; height:40px; border-radius:50%; margin-right:.5rem; }
</style>

<div class="max-w-2xl mx-auto p-4 chat-container">
    <h1 class="text-xl font-bold mb-2">教えて！彩さん！</h1>

    <div id="chat-box" class="chat-box">
        {{-- 最初に挨拶＋200字要約 --}}
        @if(!empty($greeting))
            <div class="message assistant">
                <img src="{{ asset('images/aya_icon.png') }}" alt="彩さん" class="avatar">
                <div class="bubble assistant">{!! nl2br(e($greeting)) !!}</div>
            </div>
        @endif

        {{-- 既存履歴（任意で非表示にしてもOK） --}}
        @foreach($history as $msg)
            @if($msg->role === 'user')
                <div class="message user">
                    <div class="bubble user">{{ $msg->content }}</div>
                </div>
            @elseif($msg->role === 'assistant')
                <div class="message assistant">
                    <img src="{{ asset('images/aya_icon.png') }}" alt="彩さん" class="avatar">
                    <div class="bubble assistant">{{ $msg->content }}</div>
                </div>
            @endif
        @endforeach
    </div>

    <form id="chat-form" class="mt-2 flex gap-2">
        <input type="text" name="question" id="question"
               class="flex-grow border rounded p-2"
               placeholder="質問を入力..." required>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">送信</button>
    </form>
</div>

<script>
const form = document.getElementById('chat-form');
const chatBox = document.getElementById('chat-box');

function scrollBottom(){ chatBox.scrollTop = chatBox.scrollHeight; }
scrollBottom();

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const input = document.getElementById('question');
    const q = input.value.trim();
    if (!q) return;

    // 自分の吹き出し
    chatBox.insertAdjacentHTML('beforeend', `
        <div class="message user">
            <div class="bubble user"></div>
        </div>`);
    chatBox.lastElementChild.querySelector('.bubble').innerText = q;
    input.value = '';
    scrollBottom();

    // 送信
    const res = await fetch("{{ route('ask.ai.post') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ question: q })
    });

    let data;
    try { data = await res.json(); } catch { data = { answer: '（通信に失敗しました）' }; }

    // 彩さんの吹き出し
    chatBox.insertAdjacentHTML('beforeend', `
        <div class="message assistant">
            <img src="{{ asset('images/aya_icon.png') }}" alt="彩さん" class="avatar">
            <div class="bubble assistant"></div>
        </div>`);
    chatBox.lastElementChild.querySelector('.bubble').innerText = data.answer ?? '（回答取得エラー）';
    scrollBottom();
});
</script>
</x-app-layout>

<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>