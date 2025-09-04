<?php
namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceDownloadController extends Controller
{
    public function download(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        $filename = '出席者名簿-' . str_replace('-', '', $date) . '.xlsx';
        return Excel::download(new AttendanceExport($request), $filename);
    }
}