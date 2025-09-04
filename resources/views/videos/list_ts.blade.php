<x-app-layout>
<!-- 科目ナビゲーション -->
<div class="subject-nav mt-4 mb-6 flex flex-wrap justify-center gap-4">
    @php
        $subjectsList = ['英語', '数学', '国語', '理科', '社会', '英検', 'その他'];
        $currentSubject = request('subject', '英語');
    @endphp

    @foreach($subjectsList as $subjectItem)
        <a href="{{ route('videos.dashboard_ts', ['subject' => $subjectItem]) }}"
           class="px-4 py-2 rounded {{ $currentSubject == $subjectItem ? 'bg-blue-500 text-white' : 'text-black border border-gray-300' }}">
            {{ $subjectItem }}
        </a>
    @endforeach
    <a href="https://student.try-it.jp/"
        target="_blank" rel="noopener noreferrer"
        class="flex flex-row md:flex-col items-center gap-1 px-4 py-2 rounded 
                {{ $currentSubject == $subject ? 'bg-blue-500 text-white' : 'text-black border border-gray-300' }}">
            
            <span>TryIT</span>
    </a>
</div>
@if(isset($rank))
<div class="flex justify-center items-center gap-2 text-sm font-semibold text-indigo-600 mt-1">
    <span>🏆 あなたの順位：{{ $total }}人中 <strong class="text-pink-600">{{ $rank }} 位</strong></span>
    @if($rank <= 3)
        <span class="text-2xl">
            {!! ['🥇','🥈','🥉'][$rank - 1] !!}
        </span>
    @elseif($rank <= 10)
        <span class="text-yellow-500 text-xl animate-bounce">🎖️</span>
    @endif
</div>
@endif
@if(isset($levelData))
<div class="level-container">
    <div class="level-header">
        <span class="level-time">🎓 総学習時間：{{ $levelData['hours'] }}時間{{ $levelData['minutes'] }}分</span>
        <span class="level-label">Lv.{{ $levelData['level'] }}</span>
    </div>
    <div class="level-bar">
        <div class="level-fill" style="width: {{ $levelData['percent'] }}%;"></div>
    </div>
    <div class="level-remaining">
        🚀 次のレベルまで：{{ floor($levelData['nextLevelRemaining'] / 3600) }}時間{{ floor(($levelData['nextLevelRemaining'] % 3600) / 60) }}分
    </div>
</div>
@endif

    <div class="mx-auto max-w-4xl px-4 py-6">
        <h2 class="text-2xl font-bold text-center mb-6">
            {{ $grade }} の {{ $subject }} に関する学習動画
        </h2>

        <!-- アコーディオン表示（PC・モバイル共通） -->
<!-- 動画表示部分 -->
@foreach($groupedVideos as $month => $videosInMonth)
    <div class="mb-4">
        <button onclick="toggleAccordion('{{ $month }}')" class="w-full flex justify-between items-center bg-gray-100 border border-gray-300 rounded-lg px-4 py-3 font-bold">
            📅 {{ $month }} の動画（{{ count($videosInMonth) }}本）
            <span id="arrow-{{ $month }}" class="text-yellow-500">▼</span>
        </button>
        <div id="accordion-{{ $month }}" class="overflow-hidden transition-all duration-500 ease-in-out max-h-0">
            @foreach($videosInMonth as $index => $video)
                @php
                    $videoId = Str::after($video->youtube_url, 'embed/');
                    $globalIndex = crc32($month . $index);
                    $isViewed = in_array($video->id, $viewedVideoIds ?? []);
                @endphp
                <div class="video-card"
                     data-index="{{ $globalIndex }}"
                     data-video-id="{{ $videoId }}"
                     data-db-id="{{ $video->id }}"
                     data-already-viewed="{{ $isViewed ? 'true' : 'false' }}">
                    <h3 class="text-lg font-bold mb-2">{{ $video->unit }}</h3>
                    <div id="player-{{ $globalIndex }}" class="w-full aspect-video rounded bg-black"></div>
                    <p class="mt-2 text-blue-600">⏱️ 再生時間: <span id="watch-time-{{ $globalIndex }}">0.0</span> 秒</p>
                    <p id="status-{{ $globalIndex }}" class="text-sm mt-1 {{ $isViewed ? 'text-blue-600' : 'text-green-600' }}" style="{{ $isViewed ? '' : 'display: none;' }}">
                        {{ $isViewed ? '✅ 視聴済み' : '⏳ 再生中...' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
@endforeach


        <div class="mt-6">
            {{ $videos->appends(request()->query())->links() }}
        </div>
    </div>

    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-md md:hidden">
        <div class="flex justify-around items-center py-3">
            <a href="{{ route('online.classes') }}" class="text-blue-600 font-bold">オンライン授業</a>
            <a href="{{ route('videos.dashboard') }}" class="text-blue-600 font-bold">学習動画</a>
        </div>
    </div>
</x-app-layout>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>

<script>
window.isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
let players = {}, watchStartTimes = {}, totalWatchTimes = {}, intervals = {}, alreadySent = {};
window.videoList = [];

function collectVideoData() {
    document.querySelectorAll('.video-card').forEach(el => {
        window.videoList.push({
            index: el.dataset.index,
            videoId: el.dataset.videoId,
            videoDbId: el.dataset.dbId,
            alreadyViewed: el.dataset.alreadyViewed === 'true'
        });
    });
}

function updateDisplay(index) {
    const el = document.getElementById(`watch-time-${index}`);
    if (el && totalWatchTimes[index] != null) {
        el.textContent = totalWatchTimes[index].toFixed(1);
    }
}

// ✅ POST送信処理を変更（フラグに関係なく送信）
function sendPlaytimeToServer(index) {
    const seconds = totalWatchTimes[index] ?? 0;
    const videoInfo = window.videoList.find(v => v.index == index);
    if (!videoInfo || !videoInfo.videoDbId || seconds < 5) return; // 5秒未満は無視

    fetch("{{ url('/save-playtime') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ seconds: seconds, video_id: videoInfo.videoDbId })
    }).then(res => console.log("✅ 終了・離脱時POST:", index, seconds));
}


function getPlayerStateChangeHandler(index, alreadyViewed) {
    totalWatchTimes[index] = 0;
    alreadySent[index] = alreadyViewed;
    return function (event) {
        const statusEl = document.getElementById(`status-${index}`);
        if (event.data === YT.PlayerState.PLAYING) {
            if (!alreadySent[index] && statusEl) {
                statusEl.style.display = 'block';
                statusEl.innerHTML = '⏳ 再生中...';
            }
            watchStartTimes[index] = Date.now();
            intervals[index] = setInterval(() => {
                if (!window.isLoggedIn) return;
                const now = Date.now();
                totalWatchTimes[index] += (now - watchStartTimes[index]) / 1000;
                watchStartTimes[index] = now;
                updateDisplay(index);
                if (!alreadySent[index] && totalWatchTimes[index] >= 30) {
                    sendPlaytimeToServer(index);
                    alreadySent[index] = true;
                    if (statusEl) {
                        statusEl.innerHTML = '✅ 視聴済み';
                        statusEl.classList.remove('text-green-600');
                        statusEl.classList.add('text-blue-600');
                    }
                }
            }, 1000);
        } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
            if (!window.isLoggedIn) return;
            if (watchStartTimes[index]) {
                totalWatchTimes[index] += (Date.now() - watchStartTimes[index]) / 1000;
                watchStartTimes[index] = null;
                updateDisplay(index);
            }
            clearInterval(intervals[index]);
            // ✅ ← ここで送信
            if (event.data === YT.PlayerState.ENDED) {
                sendPlaytimeToServer(index);
            }
        }
    }; // ← 🔴このカッコが抜けていた
}

window.addEventListener('beforeunload', function () {
    for (const index in totalWatchTimes) {
        sendPlaytimeToServer(index);
    }
});

// iframe APIの読み込み完了後
window.onYouTubeIframeAPIReady = function () {
    collectVideoData(); // ← ここで動画一覧を構築
    for (const item of window.videoList) {
        const { index, videoId, alreadyViewed } = item;
        const el = document.getElementById(`player-${index}`);
        if (!el || el.offsetParent === null || players[index]) continue;
        players[index] = new YT.Player(`player-${index}`, {
            videoId,
            events: {
                onStateChange: getPlayerStateChangeHandler(index, alreadyViewed)
            }
        });
    }
};

// アコーディオン展開時に未初期化プレイヤーを追加
function toggleAccordion(month) {
    const content = document.getElementById('accordion-' + month);
    const arrow = document.getElementById('arrow-' + month);
    if (!content) return;
    const isOpen = content.classList.contains('open');
    if (isOpen) {
        content.style.maxHeight = null;
        content.classList.remove('open');
        arrow.textContent = '▼';
    } else {
        content.style.maxHeight = content.scrollHeight + 'px';
        content.classList.add('open');
        arrow.textContent = '▲';
        for (const item of window.videoList) {
            const { index, videoId, alreadyViewed } = item;
            const el = document.getElementById(`player-${index}`);
            if (!el || el.offsetParent === null || players[index]) continue;
            players[index] = new YT.Player(`player-${index}`, {
                videoId,
                events: {
                    onStateChange: getPlayerStateChangeHandler(index, alreadyViewed)
                }
            });
        }
    }
}
</script>

<!-- 最後に読み込む -->
<script src="https://www.youtube.com/iframe_api"></script>


<style>
    /* ✅ モバイル時：科目ナビゲーションのスクロール調整 */
    @media (max-width: 767px) {
        .subject-nav {
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none; /* Firefox */
        }
        .subject-nav::-webkit-scrollbar {
            display: none; /* Chrome */
        }

        .subject-nav a {
            font-size: 12px;
            padding: 4px 15px;
            margin-right: -1px;
            display: inline-block;
        }
        .subject-nav a:last-child {
            margin-right: 0;
        }
    }
    #accordion-wrapper > div {
        overflow: hidden;
        transition: max-height 0.4s ease;
    }
    .open {
        /* JSで max-height をセットするため不要でも動くが、保険で設定 */
        max-height: 1000px;
    }
    /* ✅ アコーディオンのヘッダー */
    .month-toggle-header {
        @apply font-bold text-base flex justify-between items-center px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg cursor-pointer transition;
    }
    .month-toggle-header:hover {
        background-color: #f3f4f6;
    }

    /* ✅ アコーディオン用アイコン（矢印） */
    .cute-arrow {
        font-size: 18px;
        color: #f59e0b;
        animation: bounceArrow 1.5s infinite;
        transition: transform 0.3s ease;
    }
    @keyframes bounceArrow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(3px); }
    }

    /* ✅ アコーディオン展開部分 */
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: all 0.5s ease;
        padding-left: 8px;
    }
    .accordion-content.show {
        max-height: 1000px;
        margin-bottom: 16px;
    }

    /* ✅ スマホ用の動画カード */
    .video-card {
        margin: 10px 0;
        background: #fff;
        border: 1px solid #ddd;
        padding: 12px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .thumbnail-wrapper {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
    }
    .thumbnail-wrapper img {
        width: 100%;
        border-radius: 8px;
    }
    .play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.5);
        padding: 8px;
        border-radius: 50%;
    }
    .play-icon img {
        width: 40px;
        height: 40px;
    }
    .video-title {
        margin-top: 8px;
        font-weight: 600;
        font-size: 14px;
    }
.subject-nav a {
    font-weight: bold;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    border-radius: 9999px; /* ⬅ 丸くする */
    padding: 8px 16px;
    border: 1px solid #ccc;
}
.subject-nav a:hover {
    opacity: 0.85;
}
.level-container {
    max-width: 800px;
    margin: 0 auto 24px;
    padding: 16px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    font-family: sans-serif;
}

.level-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

.level-time {
    color: #2b2b2b;
}

.level-label {
    color: #6b21a8;
    font-size: 14px;
}

.level-bar {
    height: 16px;
    width: 100%;
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
}

.level-fill {
    height: 100%;
    background: linear-gradient(to right, #a78bfa, #fde047); /* purple to yellow */
    border-radius: 9999px;
    transition: width 0.3s ease;
}

.level-remaining {
    text-align: right;
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}
    /* ✅ モバイルナビ（そのまま残してOK） */
    .mobile-nav {
        display: flex;
        justify-content: space-around;
        align-items: center;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 12px 0;
        background-color: white;
        border-top: 1px solid #e5e7eb;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        z-index: 50;
    }
    .mobile-nav a {
        font-weight: bold;
        color: #3b82f6;
        text-decoration: none;
    }
    @media (min-width: 768px) {
        .mobile-nav {
            display: none;
        }
        .month-toggle-header,
        .accordion-content {
            display: none;
        }
    }
</style>
