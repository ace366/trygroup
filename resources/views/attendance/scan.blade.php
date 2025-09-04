<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">QRコードをスキャンして出席登録</h1>

        <!-- ✅ 右上トースト通知 -->
        <div id="toast" class="fixed top-5 right-5 text-white px-4 py-2 rounded shadow hidden z-50"></div>

        <div class="flex flex-col items-center">
            <div id="qr-reader" class="w-full max-w-md"></div>
            <p id="qr-result" class="mt-4 text-lg font-semibold text-gray-700">
                QRコードをカメラにかざしてください
            </p>
            <p id="qr-message" class="mt-2 text-xl font-bold text-green-600 hidden"></p>
        </div>
    </div>

    <!-- QRコードスキャン用ライブラリ -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
    // ======= トースト通知（成功:緑 / 失敗:赤） =======
    let qrReader;                  // Html5QrcodeScanner 参照用（スコープ修正）
    let beep;                      // 事前許可済みのAudioインスタンス
    let isProcessing = false;      // 多重POST防止
    let lastScanAt = 0;            // スキャン連打対策（クールダウン）
    const SCAN_COOLDOWN_MS = 1800; // スキャン再開までの最短時間

    function showToast(message, isError = false) {
        const toast = document.getElementById("toast");
        toast.classList.remove("hidden", "bg-green-500", "bg-red-500");
        toast.classList.add(isError ? "bg-red-500" : "bg-green-500");
        toast.innerText = message;

        // 既存のタイマーをクリアしてから再セット（多重表示対策）
        clearTimeout(window.__toastTimer);
        window.__toastTimer = setTimeout(() => {
            toast.classList.add("hidden");
        }, 2000);
    }

    document.addEventListener("DOMContentLoaded", function () {
        // ✅ 効果音（ユーザー操作で一度許可を得る）
        beep = new Audio("{{ asset('sounds/beep.mp3') }}");
        beep.volume = 1.0;
        document.body.addEventListener('click', () => {
            beep.play().then(() => {
                beep.pause();
                beep.currentTime = 0;
            }).catch(() => {});
        }, { once: true });

        // ✅ スキャナ初期化（グローバル変数に格納）
        qrReader = new Html5QrcodeScanner("qr-reader", {
            fps: 30,
            qrbox: { width: 250, height: 250 }
        });
        qrReader.render(onScanSuccess, onScanError);
    });

    async function onScanSuccess(qrMessage) {
        const now = Date.now();
        if (now - lastScanAt < SCAN_COOLDOWN_MS || isProcessing) {
            return; // 連続読み取り抑止
        }
        lastScanAt = now;

        // 表示更新
        document.getElementById('qr-result').innerText = "QRコード読み取り成功: " + qrMessage;

        // ✅ 効果音（失敗してもアプリは継続）
        try { beep && beep.play(); } catch(_) {}

        // ✅ 入力バリデーション（想定：user_id 数値。必要に応じて調整）
        const userId = String(qrMessage).trim();
        if (!/^\d+$/.test(userId)) {
            showToast("不正なQRコードです（user_idが数値ではありません）", true);
            return resetScannerWithDelay();
        }

        isProcessing = true;
        try {
            const res = await fetch("{{ route('attendance.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    user_id: userId,
                    attendance_type: "physical",
                    "class": "scan"
                })
            });

            // ネットワーク/HTTPエラー処理
            if (!res.ok) {
                showToast("通信エラーが発生しました", true);
                return resetScannerWithDelay();
            }

            const data = await res.json();

            if (data && data.success) {
                const message = `${data.user_name} さん入室を確認しました！`;

                // 画面内メッセージ（2秒表示）
                const messageBox = document.getElementById('qr-message');
                messageBox.innerText = message;
                messageBox.classList.remove("hidden");
                setTimeout(() => messageBox.classList.add("hidden"), 2000);

                // ✅ トーストにも同じメッセージ（成功:緑）
                showToast(message, false);
            } else {
                // サーバーからの失敗応答
                const reason = (data && data.message) ? `（${data.message}）` : "";
                showToast(`出席登録に失敗しました${reason}`, true);
            }
        } catch (e) {
            console.error("出席登録エラー:", e);
            showToast("出席登録に失敗しました（例外）", true);
        } finally {
            isProcessing = false;
            resetScannerWithDelay();
        }
    }

    function resetScannerWithDelay() {
        if (qrReader && typeof qrReader.clear === "function") {
            qrReader.clear();
            setTimeout(() => {
                qrReader.render(onScanSuccess, onScanError);
            }, 2000); // 2秒後に再開
        }
    }

    function onScanError(errorMessage) {
        // ライブラリエラーは控えめにログのみ
        console.log("QRコード読み取りエラー:", errorMessage);
    }
    </script>
</x-app-layout>

<!-- Tailwind（CDN版を利用している場合のみ） -->
<script src="https://cdn.tailwindcss.com"></script>
