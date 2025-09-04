<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamScore2021;
use OpenAI\Laravel\Facades\OpenAI; // laravel-openai パッケージ利用時

class ExamAnalysisController extends Controller
{
    /**
     * 合否判定や集計結果を ChatGPT に渡す
     */
    public function analyze(Request $request)
    {
        // ✅ ユーザー入力を取得
        $school     = $request->input('school');     // 学校名
        $department = $request->input('department'); // 学科
        $scores     = $request->only(['japanese','math','english','science','social']);
        $naishin    = $request->only(['naishin_1','naishin_2','naishin_3']);

        // ✅ DBから対象データを取得（例：学校・学科一致）
        $query = ExamScore2021::query()
            ->when($school, fn($q) => $q->where('school_name', $school))
            ->when($department, fn($q) => $q->where('department', $department));

        $data = $query->get();

        // ✅ 集計例（平均点/合格率など）
        $summary = [
            'count' => $data->count(),
            'avg_score' => $data->avg('disclosed_score'),
            'avg_naishin' => [
                '1年' => $data->avg('naishin_1'),
                '2年' => $data->avg('naishin_2'),
                '3年' => $data->avg('naishin_3'),
            ],
            'passed_ratio' => $data->count() > 0
                ? round($data->where('is_passed', 1)->count() / $data->count() * 100, 1)
                : 0,
            '検定' => [
                '英検あり' => $data->whereNotNull('eiken')->count(),
                '漢検あり' => $data->whereNotNull('kanken')->count(),
                '数検あり' => $data->whereNotNull('suken')->count(),
            ],
        ];

        // ✅ 不合格者の点数一覧（昇順ソート）
        $failedScores = $data->where('is_passed', 0)
            ->pluck('disclosed_score')
            ->filter()
            ->sort()
            ->values()
            ->all();

        $summary['failed_scores'] = $failedScores;

        // ✅ ChatGPT に渡すプロンプトを組み立て
        $prompt = "以下は exam_scores2021 テーブルの集計結果です。\n"
            ."学校: {$school} {$department}\n"
            ."件数: {$summary['count']}\n"
            ."平均開示点: {$summary['avg_score']}\n"
            ."平均内申: 1年={$summary['avg_naishin']['1年']} "
            ."2年={$summary['avg_naishin']['2年']} "
            ."3年={$summary['avg_naishin']['3年']}\n"
            ."合格率: {$summary['passed_ratio']}%\n"
            ."検定保持者: 英検={$summary['検定']['英検あり']} "
            ."漢検={$summary['検定']['漢検あり']} "
            ."数検={$summary['検定']['数検あり']}\n\n"
            ."この集計結果をもとに、ユーザーが入力した成績（国数英理社="
            .implode(',', $scores).", 内申="
            .implode(',', $naishin)."）の合格可能性や対策を返答してください。";

        // ✅ システムプロンプトを定義
        $sys = <<<'SYS'
目的：2025年度の埼玉県高校入試に関する質問に答える
対象：中学生および保護者

基本方針：
- 回答時は exam_scores2021 テーブルを必ず参照する（学校関連の質問時）。
- できるだけ具体的に返答し、数値や日付を明示する。
- 私立高校の「大丈夫ですよ」「安心して受験してください」は“確約をもらった事実”として扱う。
- 公立高校には確約が無いため、「確約」という表現は禁止。
- 合否判定だけでなく、偏差値ランキングや検定保持者割合などの集計質問にも exam_scores2021 を活用する。
SYS;

        // ✅ ChatGPT呼び出し
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $sys],
                ['role' => 'user', 'content' => $prompt],
            ]
        ]);

        return view('exam_result', [
            'input' => $request->all(),
            'summary' => $summary,
            'chatgpt_answer' => $response->choices[0]->message->content ?? ''
        ]);
    }
}
