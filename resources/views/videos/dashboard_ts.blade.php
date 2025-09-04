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
           class="flex flex-row md:flex-col items-center gap-2 px-4 py-2 rounded text-black border border-gray-300">
            <span>TryIT</span>
        </a>
    </div>

    @guest
        <div class="text-center text-sm text-gray-600 mb-4">
            ※ 学習記録を残すにはログインしてください
        </div>
    @endguest

    @if(isset($rank))
        <div class="flex justify-center items-center gap-2 text-sm font-semibold text-indigo-600 mt-1">
            <span>🏆 あなたの順位：{{ $total }}人中 <strong class="text-pink-600">{{ $rank }} 位</strong></span>
            @if($rank <= 3)
                <span class="text-2xl">{!! ['🥇','🥈','🥉'][$rank - 1] !!}</span>
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

    @foreach (['3年生', '2年生', '1年生'] as $grade)
        @php
            $filtered = collect($videos)->where('grade', $grade);
            $count = $filtered->count();
            $latestThree = $filtered->sortByDesc('id')->take(1);
        @endphp

        <div class="course-box px-4 max-w-screen-lg mx-auto">
            <div class="md:w-1/4 mb-4 md:mb-0 md:pr-6">
                <h2 class="text-xl font-bold mb-2">{{ $grade }}</h2><h2>（{{ $currentSubject }}：{{ $count }}本）</h2>
                <a href="{{ route('videos.list.ts', ['subject' => $currentSubject, 'grade' => $grade]) }}"
                   class="inline-block mt-4 text-blue-500 font-semibold py-2 px-6 rounded-full transition">
                   📖 過去の授業へ
                </a>
            </div>

            <div class="flex-wrap gap-4">
                @foreach($latestThree as $video)
                    @php
                        $youtubeId = Str::after($video->youtube_url, 'embed/');
                        $isViewed = in_array($video->id, $viewedVideoIds ?? []);
                    @endphp
                    <div class="video-wrapper w-full bg-gray-50 p-2 rounded shadow-sm relative"
                         data-video-index="{{ $youtubeId }}"
                         data-video-id="{{ $youtubeId }}"
                         data-db-id="{{ $video->id }}"
                         data-already-viewed="{{ $isViewed ? 'true' : 'false' }}">
                        <p class="text-sm font-semibold mb-1 truncate">最新の授業</p>
                        <p class="text-sm font-semibold mb-1 truncate">{{ $video->unit }}</p>
                        <div id="player-{{ $youtubeId }}" class="w-full sm:mx-auto bg-black rounded" style="aspect-ratio: 16 / 9;"></div>
                        <p class="mt-2 text-blue-600">⏱️ 再生時間: <span id="watch-time-{{ $youtubeId }}">0.0</span> 秒</p>
                        @auth
                        <p id="status-{{ $youtubeId }}" class="text-sm mt-1 {{ $isViewed ? 'text-blue-600' : 'text-green-600' }}" style="{{ $isViewed ? '' : 'display: none;' }}">
                            {{ $isViewed ? '✅ 視聴済み' : '⏳ 再生中...' }}
                        </p>
                        @if($isViewed)
                            <div class="viewed-stamp">🌟 視聴済み</div>
                        @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
<!-- YouTube API -->
<script src="https://www.youtube.com/iframe_api"></script>
<script>

let players = {}, watchStartTimes = {}, totalWatchTimes = {}, intervals = {}, alreadySent = {}, videoList = [];
window.isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

function updateDisplay(index) {
    const el = document.getElementById(`watch-time-${index}`);
    if (el && totalWatchTimes[index] != null) {
        el.textContent = totalWatchTimes[index].toFixed(1);
    }
}

function sendPlaytimeToServer(index) {
    const seconds = totalWatchTimes[index] ?? 0;
    const videoInfo = window.videoList[index];
    if (!videoInfo || !videoInfo.videoDbId) return;

    fetch("{{ url('/save-playtime') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ seconds: seconds, video_id: videoInfo.videoDbId })
    });
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
        }
    };
}

function createPlayer(videoInfo) {
    const index = videoInfo.index;
    const el = document.getElementById(`player-${index}`);
    if (!el || players[index]) return;
    players[index] = new YT.Player(el.id, {
        videoId: videoInfo.videoId,
        events: {
            onStateChange: getPlayerStateChangeHandler(index, videoInfo.alreadyViewed)
        }
    });
}


function bindAccordionInit() {
    document.querySelectorAll('.accordion-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const index = btn.dataset.index;
            const content = document.getElementById(`accordion-${index}`);
            if (content) {
                const wasHidden = content.classList.contains('hidden');
                content.classList.toggle('hidden');

                if (wasHidden) {
                    setTimeout(() => {
                        if (!players[index]) {
                            const video = videoList.find(v => v.index === index);
                            if (video) createPlayer(video);
                        }
                    }, 100); // ← iOS対応で描画安定まで待つ
                }
            }
        });
    });
}

function initVisiblePlayers() {
    document.querySelectorAll('.video-wrapper').forEach(wrapper => {
        const videoData = {
            index: wrapper.dataset.videoIndex,
            videoId: wrapper.dataset.videoId,
            videoDbId: wrapper.dataset.dbId,
            alreadyViewed: wrapper.dataset.alreadyViewed === 'true'
        };
        videoList.push(videoData);
        // ✅ 端末に関わらず最初からプレイヤーを生成（iOSでの不具合回避）
        createPlayer(videoData);
    });
}



window.onYouTubeIframeAPIReady = function () {
    initVisiblePlayers();
    bindAccordionInit();
};
</script>



<style>
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

.animate-bounce {
    animation: bounce 1s infinite;
}
@keyframes bounce {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
}
/* ✅ 視聴済みバッジ */
.viewed-stamp {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #fcd34d;
    color: #7c3aed;
    font-weight: bold;
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 9999px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    z-index: 10;
    animation: popIn 0.3s ease-out;
}

@keyframes popIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

/* 科目ナビゲーションのリンク */
.tap-button {
    margin-top: 80px; /* 必要に応じて数値は調整 */
}
.course-box {
    display: flex;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 8px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
    min-height: 220px; /* ⬅️ 高さを少し高く指定 */
}
.course-box .info {
    width: 30%;
}
.course-box .videos {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    justify-content: flex-end;
    width: 75%;
}
.course-box .videos .thumb {
    width: 200px;
    background: #f9f9f9;
    padding: 8px;
    border-radius: 6px;
}
.course-box .videos .thumb img {
    width: 100%;
    border-radius: 4px;
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

/* 学年ごとのボックス */
.subject-box {
    max-width: 600px;
    margin: 0 auto;
}

/* スマホナビゲーション */
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
.subject-box {
    max-width: none; /* ← 幅制限を解除 */
    width: 100%;
    padding-left: 40px;
    padding-right: 40px;
}

/* モバイル表示時の科目ナビ調整 */
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
        padding: 4px 15px;         /* ⬅ 間隔を狭く */
        margin-right: -1px;
        display: inline-block;
    }
    .subject-nav a:last-child {
        margin-right: 0; /* 最後のボタンだけ余白を消す（オプション） */
    }
    .subject-box {
        margin-left: 16px;
        margin-right: 16px;
    }

}
.latest-videos-container {
    display: flex;
    gap: 24px;
    justify-content: flex-start;
    flex-wrap: wrap;
}

.video-card {
    background-color: #f9fafb;
    padding: 16px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    width: 320px;
}

/* PC時はモバイルナビ非表示 */
@media (min-width: 768px) {
    .mobile-nav {
        display: none;
    }
}

/* フッター調整 */
.footer-padding {
    padding-bottom: 120px;
}
.footer-inner {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
}
.footer-logo {
    width: 32px;
    height: 32px;
}
.footer-text {
    font-size: 14px;
    color: #6b7280;
    text-align: center;
}
.mobile-nav a {
    font-weight: bold;
    color: #3b82f6;
    text-decoration: none;
}
</style>


</x-app-layout>