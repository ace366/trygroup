<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>指導報告書一括（{{ $date }}）</title>
    <style>
        @font-face { font-family: 'ipag'; font-style: normal; font-weight: 400;
            src: url("file://{{ storage_path('fonts/ipag.ttf') }}") format('truetype'); }
        @font-face { font-family: 'ipag'; font-style: normal; font-weight: 700;
            src: url("file://{{ storage_path('fonts/ipag.ttf') }}") format('truetype'); }
        @font-face { font-family: 'ipag'; font-style: italic; font-weight: 400;
            src: url("file://{{ storage_path('fonts/ipag.ttf') }}") format('truetype'); }
        @font-face { font-family: 'ipag'; font-style: italic; font-weight: 700;
            src: url("file://{{ storage_path('fonts/ipag.ttf') }}") format('truetype'); }

        @page { size: A4; margin: 58mm 15mm 18mm 15mm; } /* 上/右/下/左 */

        html, body { margin: 0; padding: 0; }
        body { font-family: 'ipag', sans-serif; font-size: 13px; line-height: 1.7; }

        .header {
            position: fixed; top: -58mm; left: 0; right: 0; height: 30mm;
            display: flex; align-items: center; gap: 8mm;
            border-bottom: 1px solid #000; padding: 4mm 5mm 4mm;
        }
        .footer {
            position: fixed; bottom: -18mm; left: 0; right: 0; height: 12mm;
            display: flex; align-items: center; justify-content: space-between;
            border-top: 1px solid #000; padding: 3mm 5mm 0; font-size: 10px;
        }
        .header .logo, .footer .logo { height: 12mm; }
        .header .brand { font-size: 16px; font-weight: 700; }

/* ▼ 置換：.page を紙面中央に配置（枠ごと中央寄せ） */
.page {
    page-break-after: always;

    padding: 75px 20px 20px;   /* 既存値のまま */
    box-sizing: border-box;
    width: 170mm;              /* 印字領域(約180mm)より少し狭く設定 */
    margin: 0 auto;            /* 中央配置 */

}


        .page:last-child { page-break-after: auto; }

.title-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center; /* ← 文字を中央に */
}
.title-wrap h1 {
    margin: 0;
    text-align: center;
}
.report-date {
    position: absolute;
    right: 0;
    top: 0;
    font-size: 12px;
    font-weight: normal;
}
        .report-date { position: absolute; right: 0; top: 0; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; line-height: 1.6; }
        .info-table td.label { width: 80px; background-color: #f8f8f8; font-weight: bold; }

        .section-title { font-weight: bold; font-size: 13px; margin: 14px 0 6px; }
        .box {
            border: 1px solid #000;
            padding: 8px;
            min-height: 90px;
            white-space: pre-wrap;
            line-height: 1.8;
            font-size: 13.5px;

            /* ▼ 追加：長文・長単語を折り返す設定 */
            word-wrap: break-word;       /* 古いブラウザ用 */
            overflow-wrap: break-word;   /* 標準仕様 */
        }
/* ▼ 置換：ヘッダー/フッター位置と高さ（本文との重なり解消） */
.header {
    position: fixed;
    top: 2mm;            /* 余白内での表示位置 */
    left: 0; right: 0;
    height: 5mm;          /* 見出し＋ロゴ分の高さを確保 */
    display: flex;
    align-items: center;
    gap: 8mm;
    border-bottom: 1px solid #000;
    padding: 0 5mm 8mm;    /* 下側の余白を広めに */
}
/* ▼ 置換：フッターのレイアウト（文字だけ中央、ロゴは左固定） */
.footer {
    position: fixed;
    bottom: 0mm;            /* 既存値があればそのままでもOK */
    left: 0; right: 0;
    height: 5mm;             /* 既存の高さに合わせて可 */
    border-top: 1px solid #000;
    padding: 4mm 5mm 0;       /* 上側に余白 */
    font-size: 10px;
    display: block;           /* ← flex をやめる */
}

/* ロゴを左固定配置（テキスト中央と干渉しないよう絶対配置） */
.footer .logo {
    position: absolute;
    left: 5mm;
    top: 3mm;                 /* フッター内での縦位置は調整可 */
    height: 12mm;
}

/* 文字だけをページ中央に配置 */
.footer .copyright {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 2mm;                 /* 見やすい縦位置に調整 */
    text-align: center;
}


    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="file://{{ public_path('images/logo.JPG') }}" alt="よりE土曜塾ロゴ">
        
    </div>
    <div class="footer">
        
        <div class="copyright">© よりE土曜塾 / Trygroup Inc. All rights reserved.</div>
    </div>

    @foreach($groups as $studentId => $guidances)
        @php $student = $students[$studentId] ?? null; @endphp
        @if($student)
            @foreach($guidances as $g)
                <div class="page">
                    <div class="title-wrap">
                        <h1>指導報告書</h1>
                        <div class="report-date">
                            {{ \Carbon\Carbon::parse($g->registered_at ?? $g->created_at)->format('Y年m月d日') }}
                        </div>
                    </div>

                    <table class="info-table">
                        <tr><td class="label">氏名</td><td>{{ $student->last_name }} {{ $student->first_name }}</td></tr>
                        <tr><td class="label">学校</td><td>{{ $student->school }}</td></tr>
                        <tr><td class="label">学年</td><td>{{ $student->grade }}</td></tr>
                        <tr><td class="label">講師名</td><td>{{ $g->teacher_name ?? $g->teacher?->name ?? '-' }}</td></tr>
                        <tr><td class="label">講座</td><td>{{ $g->course_type }}</td></tr>
                        <tr><td class="label">時間帯</td><td>{{ $g->time_zone }}</td></tr>
                        <tr><td class="label">グループ</td><td>{{ $g->group }}</td></tr>
                        <tr><td class="label">教科</td><td>{{ $g->subject }}</td></tr>
                        <tr><td class="label">単元</td><td>{{ $g->unit }}</td></tr>
                    </table>

                    <div class="section-title">■ 指導内容</div>
                    <div class="box">{!! nl2br(e($g->content)) !!}</div>
                    <div class="section-title">■ 授業態度・雰囲気</div>
                    <div class="box">{!! nl2br(e($g->attitude)) !!}</div>
                    <div class="section-title">■ 宿題内容</div>
                    <div class="box">{!! nl2br(e($g->homework)) !!}</div>
                </div>
            @endforeach
        @endif
    @endforeach
</body>
</html>
