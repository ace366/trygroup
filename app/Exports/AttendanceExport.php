<?php
namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Attendance::with('user')
            ->when($this->request->date, fn($q) => $q->whereDate('attendance_time', $this->request->date))
            ->when($this->request->attendance_type, fn($q) => $q->where('attendance_type', $this->request->attendance_type))
            ->get();

        return $query->map(function ($attendance) {
            return [
                '日時'         => $attendance->attendance_time,
                '氏名'         => optional($attendance->user)->last_name . ' ' . optional($attendance->user)->first_name,
                '学校名'       => optional($attendance->user)->school,
                '学年'         => optional($attendance->user)->grade,
                'クラス'       => $attendance->class,
                '出席タイプ'   => $attendance->attendance_type === 'online' ? 'オンライン' : '対面',
            ];
        });
    }

    public function headings(): array
    {
        return ['日時', '氏名', '学校名', '学年', 'クラス', '出席タイプ'];
    }
}
