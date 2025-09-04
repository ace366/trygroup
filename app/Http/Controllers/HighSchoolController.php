<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HighSchoolController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');
    
        $results = DB::table('high_schools')
            ->where('name', 'like', "%{$query}%")
            ->select('name', 'department')
            ->distinct()
            ->limit(20)
            ->get();
    
        // 表示名（例: 大宮高校（普通科））を value に変換
        return response()->json($results->map(function ($row) {
            return [
                'label' => "{$row->name}（{$row->department}）",
                'value' => $row->name,
            ];
        }));
    }
    
}
