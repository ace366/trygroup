<?php
// app/Http/Controllers/Staff/Master/ClientController.php（最新）

namespace App\Http\Controllers\Staff\Master;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    // ※ ここに __construct() は置かない（$this->middleware(...) を呼ばない）

    /** 受託元一覧 + 検索 + 新規作成モーダル */
    public function index(Request $request)
    {
        $this->authorizeRole();

        $q = trim((string)$request->input('q', ''));

        $clients = Client::with('base')
            ->when($q !== '', fn($query) => $query->where('client_name', 'like', "%{$q}%"))
            ->orderBy('client_code')
            ->paginate(10)
            ->withQueryString();

        $bases = ClientBase::orderBy('base_code')->get(['id', 'base_code', 'base_name']);

        return view('staff.master.clients.index', compact('clients', 'q', 'bases'));
    }

    /** 登録（モーダルからPOST） */
    public function store(Request $request)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:100'],
            'base_id'     => ['required', Rule::exists('client_bases', 'id')->whereNull('deleted_at')],
        ]);

        DB::transaction(function () use ($validated) {
            $last = Client::withTrashed()->select('client_code')->orderByDesc('id')->first();
            $nextNumber = ($last && preg_match('/^\d+$/', $last->client_code)) ? ((int)$last->client_code + 1) : 1;

            do {
                $code = str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
                $exists = Client::withTrashed()->where('client_code', $code)->exists();
                $nextNumber++;
            } while ($exists);

            Client::create([
                'client_code' => $code,
                'client_name' => $validated['client_name'],
                'base_id'     => (int)$validated['base_id'],
            ]);
        });

        return redirect()
            ->route('staff.master.clients')
            ->with('status', '受託元を登録しました。');
    }

    private function authorizeRole(): void
    {
        $role = Auth::user()->role ?? 'guest';
        if (!in_array($role, ['admin', 'teacher'], true)) {
            abort(403);
        }
    }
}
