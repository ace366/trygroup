<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // 入力バリデーション
        $validated = $request->validate([
            'q'            => ['nullable','string','max:100'],
            'fiscal_year'  => ['nullable','integer','exists:fiscal_years,id'],
            'date_from'    => ['nullable','date_format:Ymd'],
            'date_to'      => ['nullable','date_format:Ymd'],
            'type'         => ['nullable','array'],
            'type.*'       => [Rule::in(['venue','individual'])],
            'page'         => ['nullable','integer','min:1'],
            'per_page'     => ['nullable','integer','min:5','max:100'],
            'sort'         => ['nullable','in:recent,oldest,name_asc,name_desc'],
        ]);

        $perPage = (int)($validated['per_page'] ?? 10);
        $types   = (array)($validated['type'] ?? []);

        $query = Project::query()
            ->with('fiscalYear')
            ->keyword($validated['q'] ?? null)
            ->fiscal($validated['fiscal_year'] ?? null)
            ->overlapPeriod($validated['date_from'] ?? null, $validated['date_to'] ?? null)
            ->typeIn($types);

        // 既定：契約開始の新しい順
        $sort = $validated['sort'] ?? 'recent';
        $query->when($sort === 'recent', fn($q) => $q->orderByDesc('contract_start'))
              ->when($sort === 'oldest', fn($q) => $q->orderBy('contract_start'))
              ->when($sort === 'name_asc', fn($q) => $q->orderBy('name'))
              ->when($sort === 'name_desc', fn($q) => $q->orderByDesc('name'));

        $projects = $query->paginate($perPage)->appends($validated);
        $fiscalYears = FiscalYear::orderByDesc('year')->get();

        return view('staff.projects.index', [
            'projects'     => $projects,
            'fiscalYears'  => $fiscalYears,
            'filters'      => [
                'q'           => $validated['q'] ?? '',
                'fiscal_year' => $validated['fiscal_year'] ?? '',
                'date_from'   => $validated['date_from'] ?? '',
                'date_to'     => $validated['date_to'] ?? '',
                'type'        => $types,
                'per_page'    => $perPage,
                'sort'        => $sort,
            ],
        ]);
    }
public function store(Request $request)
{
    // 管理者のみ
    abort_if(!auth()->user() || (auth()->user()->role ?? null) !== 'admin', 403);

    // 入力バリデーション（DB定義に合わせる）
    $data = $request->validate([
        'name'            => ['required','string','max:255'],
        'description'     => ['nullable','string','max:10000'],
        'fiscal_year_id'  => ['required','integer','exists:fiscal_years,id'],
        'contract_start'  => ['nullable','date'], // <input type="date"> を想定（YYYY-MM-DD）
        'contract_end'    => ['nullable','date','after_or_equal:contract_start'],
        'type'            => ['required', Rule::in(['venue','individual'])],
        'client_id'       => ['required','integer','exists:clients,id'],
    ]);

    // 明示代入でマスアサイン回避
    $p = new Project();
    $p->name           = $data['name'];
    $p->description    = $data['description'] ?? null;
    $p->fiscal_year_id = $data['fiscal_year_id'];
    $p->contract_start = $data['contract_start'] ?? null;
    $p->contract_end   = $data['contract_end'] ?? null;
    $p->type           = $data['type'];
    $p->save();

    return redirect()
        ->route('staff.projects.index')
        ->with('status', '事業を登録しました。');
}

}
