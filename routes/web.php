<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\OnlineClassController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PhysicalAttendanceController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Post;
use App\Http\Controllers\LineController;
use App\Http\Controllers\LineWebhookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use Carbon\Carbon;

use App\Http\Controllers\RegularTestController;
use App\Http\Controllers\HokushinTestController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\StudentController;

use App\Http\Controllers\SchoolRegisterController;
use App\Http\Controllers\HighSchoolController;
use App\Http\Controllers\AttendanceDownloadController;
use App\Http\Controllers\AisystemController;
use App\Http\Controllers\LessonCommentController;
use App\Http\Controllers\MockTestController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\FileBoxController;
use App\Http\Controllers\Admin\BulkMailController;
use App\Http\Controllers\UnsubscribeController;

// ★ Staff配下Controllerを明示
use App\Http\Controllers\Staff\ProjectController;
use App\Http\Controllers\Staff\Master\ClientController as StaffClientController;
use App\Http\Controllers\Staff\ClientLookupController;

// ==================== Staff（講師管理系） ====================
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    // ダッシュボード
    Route::get('/', function () {
        abort_if(!auth()->user() || (auth()->user()->role ?? null) !== 'admin', 403);
        return view('staff.dashboard');
    })->name('dashboard');

    // 事業：Controllerで一覧表示（統一名：staff.projects.index）
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

    // ▼ 新規作成（追加）
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

    // 受託元検索 API（JSON）
    Route::get('/api/clients', [ClientLookupController::class, 'index'])
        ->name('api.clients.index');

    // 会場・会場日程・生徒（スタブ）
    Route::get('/venues', fn () => view('staff.stub', ['title' => '会場']))->name('biz.venues');
    Route::get('/schedules', fn () => view('staff.stub', ['title' => '会場日程']))->name('biz.schedules');
    Route::get('/students', fn () => view('staff.stub', ['title' => '生徒']))->name('biz.students');

    // メモ系（スタブ）
    Route::prefix('memos')->name('memos.')->group(function () {
        Route::get('/projects', fn () => view('staff.stub', ['title' => '事業メモ']))->name('projects');
        Route::get('/venues',   fn () => view('staff.stub', ['title' => '会場メモ']))->name('venues');
        Route::get('/teachers', fn () => view('staff.stub', ['title' => '講師メモ']))->name('teachers');
        Route::get('/students', fn () => view('staff.stub', ['title' => '生徒メモ']))->name('students');
    });

    // マスタ
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/clients',  [StaffClientController::class, 'index'])->name('clients');
        Route::post('/clients', [StaffClientController::class, 'store'])->name('clients.store');

        Route::get('/system', fn () => view('staff.stub', ['title' => 'システム系マスタ']))->name('system');
    });

    // シフト（スタブ）
    Route::get('/shift/by-schedule', fn () => view('staff.stub', ['title' => '会場日程別状況']))->name('shift.by_schedule');
    Route::get('/shift/leave',       fn () => view('staff.stub', ['title' => '休み申請']))->name('shift.leave');
    Route::get('/shift/entry',       fn () => view('staff.stub', ['title' => 'エントリー']))->name('shift.entry');
    Route::get('/shift/count-settings', fn () => view('staff.stub', ['title' => '回数設定状況']))->name('shift.count_settings');

    // 実績確認（プレースホルダー）
    Route::get('/results', fn () => view('staff.stub', ['title' => '実績確認（準備中）']))->name('results.index');

    // 出金（スタブ）
    Route::get('/payouts/monthly',            fn () => view('staff.stub', ['title' => '月別出金']))->name('payouts.monthly');
    Route::get('/payouts/attendance-reports', fn () => view('staff.stub', ['title' => '勤怠報告状況']))->name('payouts.attendance_reports');
    Route::get('/payouts/execution',          fn () => view('staff.stub', ['title' => '実施状況']))->name('payouts.execution');

    // 追加：講師（スタブ）
    Route::prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/search', function () {
            abort_if(!auth()->user() || (auth()->user()->role ?? null) !== 'admin', 403);
            return view('staff.stub', ['title' => '講師検索']);
        })->name('search');

        Route::get('/', function () {
            abort_if(!auth()->user() || (auth()->user()->role ?? null) !== 'admin', 403);
            return view('staff.stub', ['title' => '講師']);
        })->name('index');
    });
});

// ==================== 管理メール履歴 ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/mails/history', [BulkMailController::class, 'history'])->name('admin.mails.history');
    Route::get('/admin/mails/history/{batch}', [BulkMailController::class, 'historyShow'])->name('admin.mails.history.show');
    Route::get('/admin/mails/reuse/{batch}', [BulkMailController::class, 'reuse'])->name('admin.mails.reuse');
});

// ==================== 購読解除 ====================
Route::get('/unsubscribe', [UnsubscribeController::class, 'show'])->name('unsubscribe.show');
Route::match(['GET','POST'], '/unsubscribe/one-click', [UnsubscribeController::class, 'oneClick'])->name('unsubscribe.oneclick');

// ==================== 管理メール送信 ====================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/mails', [BulkMailController::class, 'index'])->name('mails.index');
    Route::post('/mails/preview', [BulkMailController::class, 'preview'])->name('mails.preview');
    Route::post('/mails/send', [BulkMailController::class, 'send'])->name('mails.send');
});

// ==================== FileBox ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/filebox', [FileBoxController::class, 'index'])->name('filebox.index');
    Route::post('/filebox', [FileBoxController::class, 'store'])->name('filebox.store');
    Route::post('/filebox/download/{id}', [FileBoxController::class, 'download'])->name('filebox.download');
    Route::patch('/filebox/{sharedFile}/rename', [FileBoxController::class, 'rename'])->name('filebox.rename');
    Route::delete('/filebox/{sharedFile}', [FileBoxController::class, 'destroy'])->name('filebox.destroy');
    Route::post('/filebox/folder', [FileBoxController::class, 'storeFolder'])->name('filebox.folder');
});

// ==================== Guidance ====================
Route::middleware(['auth'])->group(function () {
    Route::resource('guidances', GuidanceController::class)->only(['destroy']);
    Route::get('/api/guidances/available-dates', [GuidanceController::class, 'availableDates'])
        ->name('guidances.available_dates');

    Route::get('/ask-ai', [AisystemController::class, 'index'])->name('ask.ai');
    Route::post('/ask-ai', [AisystemController::class, 'ask'])->name('ask.ai.post');

    Route::get('/guidances/report/today', [GuidanceController::class, 'reportToday'])
        ->name('guidances.report.today');

    // IDパラメータは数値のみ
    Route::get('/guidances/report/{student_id}', [GuidanceController::class, 'report'])
        ->whereNumber('student_id')
        ->name('guidances.report');

    Route::get('/guidances/create', [GuidanceController::class, 'create'])->name('guidances.create');
    Route::post('/guidances', [GuidanceController::class, 'store'])->name('guidances.store');
    Route::get('/guidances/{guidance}/edit', [GuidanceController::class, 'edit'])->name('guidances.edit');
    Route::patch('/guidances/{guidance}', [GuidanceController::class, 'update'])->name('guidances.update');
    Route::get('/guidances/history/{student_id}', [GuidanceController::class, 'history'])
        ->name('guidances.history');
});

// ==================== 出席まわり ====================
Route::get('/attendance/analysis', [AttendanceController::class, 'analysis'])->name('attendance.analysis');
Route::patch('/guidances/{guidance}/homework-flag', [GuidanceController::class, 'updateHomeworkFlag'])
    ->name('guidances.updateHomeworkFlag');

Route::middleware(['auth'])->group(function () {
    Route::get('/mock_tests', [MockTestController::class, 'index'])->name('mock-tests.index');
});

// ※ report の重複定義は上で統一済み

// ==================== Lesson comments ====================
Route::get('/students/{student}/lesson-comments', [LessonCommentController::class, 'index'])->name('lesson-comments.index');
Route::middleware(['auth'])->group(function () {
    Route::get('/students/{student}/lesson-comments', [LessonCommentController::class, 'index'])->name('lesson-comments.index');
    Route::get('/students/{student}/lesson-comments/create', [LessonCommentController::class, 'create'])->name('lesson-comments.create');
    Route::post('/students/{student}/lesson-comments', [LessonCommentController::class, 'store'])->name('lesson-comments.store');
});

// ==================== ダウンロード/API ====================
Route::get('/attendance/download', [AttendanceDownloadController::class, 'download'])->name('attendance.download');
Route::get('/api/highschools', [HighSchoolController::class, 'autocomplete'])->name('highschools.autocomplete');

// ==================== 学校登録 ====================
Route::get('/schools/register', [SchoolRegisterController::class, 'create'])->name('schools.register');
Route::post('/schools/register', [SchoolRegisterController::class, 'store'])->name('schools.store');

// ==================== 生徒ダッシュボード ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}/dashboard', [StudentController::class, 'dashboard'])->name('students.dashboard');
});
Route::prefix('students/{student}')->name('students.')->group(function () {
    Route::post('memo', [StudentController::class, 'updateMemo'])->name('memo');
});

// 生徒別 成績管理
Route::middleware(['auth'])->group(function () {
    Route::prefix('students/{student}')->group(function () {
        // 定期テスト
        Route::get('regular-tests', [RegularTestController::class, 'index'])->name('regular-tests.index');
        Route::get('regular-tests/create', [RegularTestController::class, 'create'])->name('regular-tests.create');
        Route::post('regular-tests', [RegularTestController::class, 'store'])->name('regular-tests.store');
        Route::get('regular-tests/{test}/edit', [RegularTestController::class, 'edit'])->name('regular-tests.edit');
        Route::put('regular-tests/{test}', [RegularTestController::class, 'update'])->name('regular-tests.update');
        Route::delete('regular-tests/{test}', [RegularTestController::class, 'destroy'])->name('regular-tests.destroy');

        // 北辰テスト
        Route::get('hokushin-tests', [HokushinTestController::class, 'index'])->name('hokushin-tests.index');
        Route::get('hokushin-tests/create', [HokushinTestController::class, 'create'])->name('hokushin-tests.create');
        Route::post('hokushin-tests', [HokushinTestController::class, 'store'])->name('hokushin-tests.store');
        Route::get('hokushin-tests/{test}/edit', [HokushinTestController::class, 'edit'])->name('hokushin-tests.edit');
        Route::put('hokushin-tests/{test}', [HokushinTestController::class, 'update'])->name('hokushin-tests.update');
        Route::delete('hokushin-tests/{test}', [HokushinTestController::class, 'destroy'])->name('hokushin-tests.destroy');

        // 内申点
        Route::get('report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
        Route::get('report-cards/create', [ReportCardController::class, 'create'])->name('report-cards.create');
        Route::post('report-cards', [ReportCardController::class, 'store'])->name('report-cards.store');
        Route::get('report-cards/{report}/edit', [ReportCardController::class, 'edit'])->name('report-cards.edit');
        Route::put('report-cards/{report}', [ReportCardController::class, 'update'])->name('report-cards.update');
        Route::delete('report-cards/{report}', [ReportCardController::class, 'destroy'])->name('report-cards.destroy');

        // 志望校
        Route::get('aspirations', [AspirationController::class, 'index'])->name('aspirations.index');
        Route::get('aspirations/create', [AspirationController::class, 'create'])->name('aspirations.create');
        Route::post('aspirations', [AspirationController::class, 'store'])->name('aspirations.store');
        Route::get('aspirations/{aspiration}/edit', [AspirationController::class, 'edit'])->name('aspirations.edit');
        Route::put('aspirations/{aspiration}', [AspirationController::class, 'update'])->name('aspirations.update');
        Route::delete('aspirations/{aspiration}', [AspirationController::class, 'destroy'])->name('aspirations.destroy');
    });
});

// 模試（生徒別）
Route::middleware(['auth'])->group(function () {
    Route::prefix('students/{student}')->name('students.')->group(function () {
        Route::get('mock_tests/create', [MockTestController::class, 'create'])->name('students.mock_tests.create');
        Route::post('mock_tests', [MockTestController::class, 'store'])->name('mock_tests.store');
        Route::get('mock_tests', [MockTestController::class, 'indexByStudent'])->name('mock_tests.index');
    });
});

// ユーザー権限
Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

// ユーザーカード
Route::get('/users/cards', [UserController::class, 'card'])->name('users.card');
Route::middleware(['auth'])->group(function () {
    Route::get('/mycard', [UserController::class, 'myCard'])->name('users.my_card');
});

// パスワード更新
Route::put('/user/password', function (Request $request) {
    $request->validate([
        'password' => ['required', 'string', 'min:4', 'confirmed'],
    ]);

    auth()->user()->update([
        'password' => Hash::make($request->password),
    ]);

    return back()->with('status', 'password-updated');
})->middleware(['auth'])->name('password.update');

// welcome（要ログイン）
Route::get('/', function () {
    $today = Carbon::today()->toDateString();

    $schedules = Schedule::where('date', '>=', $today)
                         ->orderBy('date', 'asc')
                         ->take(7)
                         ->get();

    $posts = Post::latest()->take(5)->get();

    return view('welcome', [
        'schedules' => $schedules,
        'posts' => $posts,
        'user' => Auth::user(),
    ]);
})->middleware(['auth'])->name('welcome');

// 出席登録（QR）
Route::get('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');

// 出席簿
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
});

// 出席登録（オンライン・対面）
Route::middleware(['auth'])->group(function () {
    Route::post('/attendance/{class}', [AttendanceController::class, 'logEntry'])->name('attendance.log');
});

// Zoom入室時
Route::post('/attendance/log', [AttendanceController::class, 'logAttendance'])->name('attendance.log');

// 動画
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/videos/dashboard', [VideoController::class, 'index'])->name('videos.dashboard');
});
Route::get('/videos/dashboard_ts', [VideoController::class, 'testDashboard'])->name('videos.dashboard_ts');
Route::get('/videos/list_ts', [VideoController::class, 'testList'])->name('videos.list.ts');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/videos/history', [VideoController::class, 'history'])->name('videos.history');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/user-ranking', [VideoController::class, 'userRanking'])->name('admin.user_ranking');
});
Route::post('/save-playtime', [VideoController::class, 'savePlaytime'])->middleware('auth');
Route::get('/admin/histories', [VideoController::class, 'userHistories'])->middleware('auth')->name('admin.histories');
Route::get('/admin/ranking', [VideoController::class, 'videoRanking'])->middleware('auth')->name('admin.ranking');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos/store', [VideoController::class, 'store'])->name('videos.store');
});
Route::resource('videos', VideoController::class);

// ダッシュボード（既存をwelcomeに寄せる仕様のまま）
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $today = Carbon::today()->toDateString();

        $schedules = Schedule::where('date', '>=', $today)
                             ->orderBy('date', 'asc')
                             ->take(7)
                             ->get();

        $posts = Post::latest()->take(5)->get();

        return view('welcome', [
            'schedules' => $schedules,
            'posts' => $posts,
            'user' => Auth::user(),
        ]);
    })->name('dashboard');
});

// ユーザー投稿
Route::middleware(['auth'])->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->name('posts');
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

// プロフィール
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// 年間スケジュール（管理者のみ）
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
});

// オンライン授業
Route::middleware(['auth'])->group(function () {
    Route::get('/online-classes', [OnlineClassController::class, 'index'])->name('online.classes');
});

// 対面授業の出席登録
Route::middleware(['auth'])->group(function () {
    Route::get('/physical-attendance/{venue}', [PhysicalAttendanceController::class, 'markAttendance'])->name('physical.attendance');
    Route::post('/physical-attendance', [PhysicalAttendanceController::class, 'logEntry'])->name('physical.attendance.log');
});

// 認証関連
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// LINE
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/line/send', [LineController::class, 'showForm'])->name('line.form');
    Route::post('/line/send', [LineController::class, 'sendLineMessage'])->name('line.send');
});
Route::prefix('users')->group(function () {
    Route::get('csv', [UserController::class, 'exportCsv'])->name('users.csv');
});
Route::resource('users', UserController::class);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/users', function (Request $request) {
        if ($request->user()->role !== 'admin') abort(403);
        return app(UserController::class)->index($request);
    })->name('users.index');

    Route::get('/users/csv', function (Request $request) {
        if ($request->user()->role !== 'admin') abort(403);
        return app(UserController::class)->exportCsv($request);
    })->name('users.csv');

    Route::delete('/users/{id}', function (Request $request, $id) {
        if ($request->user()->role !== 'admin') abort(403);
        return app(UserController::class)->destroy($request, $id);
    })->name('users.destroy');

    // 重複防止のため下行はコメントアウト可：Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// LINE登録/Callback/Webhook
Route::get('/line/register', [LineController::class, 'showRegisterForm'])->name('line.register.form');
Route::get('/line/callback', [LineController::class, 'handleLineCallback'])->name('line.callback');
Route::post('/webhook', [LineWebhookController::class, 'handleWebhook']);

// 認証ルート
require __DIR__.'/auth.php';
