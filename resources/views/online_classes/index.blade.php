<x-app-layout>
    <div class="container mx-auto p-6 text-center relative rounded-lg shadow-lg max-w-screen-lg">
        <h1 class="text-3xl font-bold mb-4 text-gray-900">よりE土曜塾</h1>

        <!-- ZOOMの注意事項 -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('storage/Zoom.png') }}" alt="Zoom" class="w-2/3 md:w-1/3 h-auto">
        </div>

        <p class="text-lg mb-6 text-gray-900">
            {{ auth()->user()->last_name }} さんの学年のボタンをクリックして入室してください。
        </p>

        {{-- ✅ 共有ファイルBOX（問題の共有）への明確な導線 --}}
        <div class="mb-8">
            <div class="mx-auto max-w-3xl text-left bg-white border-2 border-blue-300 rounded-lg p-5 shadow">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900">📦 ファイルBOX（問題の共有）</h2>
                        <p class="text-gray-700 mt-1">
                            講師と生徒が授業で使う<strong>問題用ファイル</strong>をここからダウンロードできます。<br class="hidden md:block">
                            
                        </p>

                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('filebox.index') }}"
                           class="inline-block px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow">
                            ファイルBOXを開く
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-2xl font-bold mt-8 mb-4 text-gray-900">オンライン授業</h2>

        @php
            $all_classes = [
                ['name' => '3年生', 'class' => '3年生', 'zoom_url' => 'https://us02web.zoom.us/j/81568645143', 'image' => '3nen.jpg'],
                ['name' => '2年生', 'class' => '2年生', 'zoom_url' => 'https://us02web.zoom.us/j/83825396447', 'image' => '2nen.jpg'],
                ['name' => '1年生', 'class' => '1年生', 'zoom_url' => 'https://us02web.zoom.us/j/87837205298', 'image' => '1nen.jpg'],
                ['name' => '英検 3級', 'class' => '3級', 'zoom_url' => 'https://us02web.zoom.us/j/83044878937', 'image' => '3kyu.jpg'],
                ['name' => '英検 4級', 'class' => '4級', 'zoom_url' => 'https://us02web.zoom.us/j/82259710039', 'image' => '4kyu.jpg'],
                ['name' => '英検 5級', 'class' => '5級', 'zoom_url' => 'https://us02web.zoom.us/j/82774024711', 'image' => '5kyu.jpg'],
            ];
        @endphp

        <div class="grid-container">
            @foreach ($all_classes as $item)
                <div class="card">
                    <div class="card-title">
                        {{ $item['name'] }}{{ \Illuminate\Support\Str::contains($item['name'], '年生') ? 'の教室' : '' }}
                    </div>

                    @if(\Illuminate\Support\Str::contains($item['name'], '年生'))
                        <p class="card-desc">
                            <strong>前半の部</strong> 9:00 ～ 11:50 <br>
                            <strong>後半の部</strong> 12:30 ～ 15:20
                        </p>
                    @endif

                    <button
                        class="zoom-btn"
                        data-class="{{ $item['class'] }}"
                        data-zoom="{{ $item['zoom_url'] }}"
                    >
                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }} 入室" class="card-image" />
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ✅ CSRFトークンの追加 -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ===== レイアウト（既存CSSの崩れ防止） ===== */
        .grid-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            text-align: center;
            justify-items: center;
            width: 100%;
            max-width: none;
            padding: 24px;
        }
        @media (min-width: 768px) {
            .grid-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* カード */
        .card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            width: 240px;
            border: 2px solid #60a5fa; /* 青系（教育・信頼感） */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            border-color: #3b82f6; /* ホバー時は濃い青に */
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3); /* 青い影 */
        }

        /* タイトル/説明/画像 */
        .card-title { font-weight: bold; font-size: 18px; color: #1f2937; margin-bottom: 8px; }
        .card-desc  { font-size: 14px; color: #374151; }
        .card-image {
            width: 192px;
            height: auto;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            margin-top: 12px;
        }

        /* ボタン（装飾なし） */
        .zoom-btn { background: none; border: none; cursor: pointer; }

        /* 既存のスマホナビ等（意図せず消えないよう現状維持） */
        .mobile-nav {
            display: flex;
            justify-content: space-around;
            align-items: center;
            position: fixed;
            bottom: 0; left: 0;
            width: 100%;
            padding: 12px 0;
            background-color: white;
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            z-index: -10;
        }
        @media (min-width: 768px) { .mobile-nav { display: none; } }
        .mobile-nav a { font-weight: bold; color: #3b82f6; text-decoration: none; }

        .footer-padding { padding-bottom: 100px; }
        .footer-inner { display: flex; justify-content: center; align-items: center; gap: 12px; }
        .footer-logo { width: 32px; height: 32px; }
        .footer-text { font-size: 14px; color: #6b7280; text-align: center; }
    </style>

    <!-- ✅ 出席登録後に Zoom を開く（失敗時はタブ閉じ） -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".zoom-btn").forEach(button => {
                button.addEventListener("click", async function(event) {
                    event.preventDefault();
                    const className = this.dataset.class;
                    const zoomUrl   = this.dataset.zoom;

                    // ユーザー操作直後に空タブを開いておく（ポップアップブロック回避）
                    const newTab = window.open("", "_blank");

                    // 押下中は無効化
                    this.disabled = true;
                    this.style.opacity = "0.6";

                    try {
                        const response = await fetch("{{ route('attendance.log') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                attendance_type: "online",
                                class: className
                            })
                        });

                        const data = await response.json();

                        if (data.status === "success") {
                            newTab.location.href = zoomUrl; // Zoomを新規タブで開く
                        } else {
                            alert("出席登録に失敗しました。\nもう一度試してください。");
                            newTab.close();
                            this.disabled = false;
                            this.style.opacity = "1";
                        }
                    } catch (e) {
                        console.error(e);
                        alert("エラーが発生しました。管理者にお問い合わせください。");
                        newTab.close();
                        this.disabled = false;
                        this.style.opacity = "1";
                    }
                });
            });
        });
    </script>

    <!-- Tailwind CDN（開発用） -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
</x-app-layout>
