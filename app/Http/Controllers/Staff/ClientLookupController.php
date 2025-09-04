<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientLookupController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->check() || (auth()->user()->role ?? null) !== 'admin', 403);

        $q = trim((string)$request->get('q', ''));
        $perPage = min(max((int)$request->get('per_page', 10), 5), 50);

        $query = DB::table('clients')
            ->select('id','client_code','client_name','base_id')
            ->orderBy('client_code');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('client_code','like',"%{$q}%")
                  ->orWhere('client_name','like',"%{$q}%");
            });
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ]);
    }
}
