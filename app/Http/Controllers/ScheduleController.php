<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        //$this->authorize('admin'); // 管理者のみ
        $schedules = Schedule::orderBy('date', 'asc')->get();
        return view('schedules.index', compact('schedules'));
    }


    public function create()
    {
        //$this->authorize('admin'); // 管理者のみ
        return view('schedules.create');
    }

    public function store(Request $request)
    {
        //$this->authorize('admin'); // 管理者のみ

        $request->validate([
            'date' => 'required|date',
            'event' => 'required|string|max:255',
        ]);

        Schedule::create($request->all());

        return redirect()->route('schedules.index')->with('success', 'スケジュールを登録しました。');
    }

    public function edit(Schedule $schedule)
    {
        //$this->authorize('admin'); // 管理者のみ
        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        //$this->authorize('admin'); // 管理者のみ

        $request->validate([
            'date' => 'required|date',
            'event' => 'required|string|max:255',
        ]);

        $schedule->update($request->all());

        return redirect()->route('schedules.index')->with('success', 'スケジュールを更新しました。');
    }

    public function destroy(Schedule $schedule)
    {
        //$this->authorize('admin'); // 管理者のみ
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'スケジュールを削除しました。');
    }
}
