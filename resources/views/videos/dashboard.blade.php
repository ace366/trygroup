<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4 text-center">学習動画一覧</h2>

            <!-- 📱 スマートフォン用ナビゲーション -->
            <div class="mobile-nav">
                <a href="{{ route('online.classes') }}">オンライン授業</a>
                <a href="{{ route('videos.dashboard') }}">学習動画</a>
            </div>

        <!-- フィルタリングフォーム -->
        <form method="GET" action="{{ route('videos.dashboard') }}" class="mb-4">
            <label for="gradeSelect" class="font-bold">学年を選択：</label>
            <select id="gradeSelect" name="grade" class="p-2 border rounded">
                <option value="">すべての学年</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                @endforeach
            </select>
            
            <label for="subjectSelect" class="font-bold ml-4">科目を選択：</label>
            <select id="subjectSelect" name="subject" class="p-2 border rounded">
                <option value="">すべての科目</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject }}" {{ request('subject') == $subject ? 'selected' : '' }}>{{ $subject }}</option>
                @endforeach
            </select>

            <button type="submit" class="ml-4 p-2 bg-blue-500 text-white rounded">フィルタ</button>
        </form>

        <!-- 動画リスト -->
        <div id="videoContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($videos as $video)
                <div class="video-item p-4 border rounded shadow bg-white">
                    <h3 class="text-lg font-bold">{{ $video->grade }} - {{ $video->subject }}</h3>
                    <p class="text-sm text-gray-600">{{ $video->unit }}</p>
                    <iframe width="100%" height="250" src="{{ $video->youtube_url }}" frameborder="0" allowfullscreen></iframe>
                </div>
            @endforeach
        </div>

        <!-- ページネーション -->
        <div class="mt-6">
            {{ $videos->appends(request()->query())->links() }}
        </div>
    </div><br>
<footer class="footer-padding text-sm text-gray-500 mt-12">
    <div class="footer-inner">
        <img src="{{ asset('images/logo.JPG') }}" alt="よりE土曜塾ロゴ" class="footer-logo">
        <span class="footer-text">© よりE土曜塾 / Trygroup Inc. All rights reserved.</span>
    </div>
</footer>
<!-- 📱 スマートフォン用ナビゲーション -->
<div class="mobile-nav">
    <a href="{{ route('online.classes') }}">オンライン授業</a>
    <a href="{{ route('videos.dashboard_ts') }}">学習動画</a>
</div>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
<style>
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

/* ✅ スマホ用ナビはPCでは非表示 */
@media (min-width: 768px) {
    .mobile-nav {
        display: none;
    }
}

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
