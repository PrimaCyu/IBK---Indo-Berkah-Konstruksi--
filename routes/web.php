<?php
use App\Models\Review;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserFileController;
use App\Http\Controllers\PenawaranController;
use App\Http\Controllers\SuratPerjanjianController;




use App\Http\Controllers\BeritaController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\JoinUsController;
use App\Http\Controllers\LayananController;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserFeedbackController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPenawaranController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\AdminSuratPerjanjianController;
use App\Http\Controllers\Admin\AdminFeedbackController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPortfolioController;
use App\Http\Controllers\Admin\AdminBeritaController;
use App\Http\Controllers\Admin\AdminTeamController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserFileController;
use App\Http\Controllers\Admin\ProjectCashflowController;
use App\Http\Controllers\Admin\AdminClientContactController;

Route::get('/layanan/{slug}', [LayananController::class, 'show'])->name('layanan.show');

Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('berita.show');

// Rute yang membutuhkan user login
Route::middleware(['auth', 'user'])->group(function () {
    Route::post('/user-files', [UserFileController::class, 'store'])->name('user-files.store');
    Route::delete('/user-files/{userFile}', [UserFileController::class, 'destroy'])->name('user-files.destroy');
    Route::post('/user-feedback', [UserFeedbackController::class, 'store'])->name('user-feedback.store');
});

// Review publik (tamu & user login bisa kirim review)
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::get('/penawaran', [PenawaranController::class, 'index'])->name('penawaran.index');
Route::post('/penawaran', [PenawaranController::class, 'store'])->name('penawaran.store');

Route::get('/portofolio', function () { 
    return view('frontend.portofolio-video');
})->name('portofolio');

Route::get('/portofolio/list', function () { 
    $portfolios = \App\Models\Portfolio::inRandomOrder()->get();
    return view('frontend.portofolio', compact('portfolios'));
})->name('portofolio.list');

Route::get('/video', function () { 
    $videos = \App\Models\Video::where('is_active', true)->latest()->get();
    return view('frontend.video', compact('videos'));
})->name('video');

Route::get('/tentang-kami', function () { return view('frontend.tentang-kami');
})->name('tentang-kami');

Route::get('/our-team', [TeamMemberController::class, 'index'])->name('our-team');

Route::get('/join-us', [JoinUsController::class, 'index'])->name('join-us');
Route::post('/join-us', [JoinUsController::class, 'store'])->name('join-us.store');

Route::get('/surat-perjanjian', [SuratPerjanjianController::class, 'index'])->name('surat-perjanjian.index');
Route::post('/surat-perjanjian', [SuratPerjanjianController::class, 'store'])->name('surat-perjanjian.store');

Route::get('/partner-client', function () { 
    return view('frontend.partner-client');
})->name('partner-client');
Route::redirect('/parent-client', '/partner-client');
Route::redirect('/logo-client', '/partner-client');

// user dashboard & projects
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/dashbord', function () {
        $user = auth()->user();
        $projects = $user->role === 'admin'
            ? \App\Models\Project::latest()->get()
            : $user->projects()->latest()->get();
        return view('user.dashbord', compact('projects'));
    })->name('dashboard');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/dashbord/project/{project}', [ProjectController::class, 'show'])->name('projects.show');
});

// login & register
Route::get('/register', function () {
    return redirect('/penawaran')->with('info', 'Pendaftaran akun klien IBK diproses otomatis setelah pengajuan penawaran proyek Anda disetujui.');
})->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::post('/logout', [AuthController::class, 'logout']);

// Buku kas & keuangan internal: accounting tidak mendapat akses ke pengelolaan admin lainnya.
Route::middleware(['auth', 'accounting'])->prefix('admin')->name('admin.cashbook.')->scopeBindings()->group(function () {
    Route::get('/keuangan-umum', [ProjectCashflowController::class, 'general'])->name('general');
    Route::get('/keuangan-proyek', [ProjectCashflowController::class, 'index'])->name('index');
    Route::get('/kontak-klien', [AdminClientContactController::class, 'index'])->name('clients.index');
    Route::post('/kontak-klien', [AdminClientContactController::class, 'store'])->name('clients.store');
    Route::get('/projects/{project}/buku-kas/export', [ProjectCashflowController::class, 'export'])->name('export');
    Route::get('/projects/{project}/buku-kas', [ProjectCashflowController::class, 'show'])->name('show');
    Route::post('/projects/{project}/buku-kas/options', [ProjectCashflowController::class, 'storeOption'])->name('options.store');
    Route::post('/projects/{project}/buku-kas', [ProjectCashflowController::class, 'store'])->name('store');
    Route::put('/projects/{project}/buku-kas/{cashflow}', [ProjectCashflowController::class, 'update'])->name('update');
    Route::get('/projects/{project}/buku-kas/{cashflow}/invoice', [ProjectCashflowController::class, 'invoice'])->name('invoice');
    Route::delete('/projects/{project}/buku-kas/{cashflow}', [ProjectCashflowController::class, 'destroy'])->name('destroy');
    Route::patch('/projects/{project}/buku-kas/{cashflow}/approve', [ProjectCashflowController::class, 'approve'])->middleware('superadmin')->name('approve');
    Route::patch('/projects/{project}/buku-kas/{cashflow}/reject', [ProjectCashflowController::class, 'reject'])->middleware('superadmin')->name('reject');
});

// AdminLTE Backend Panel Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Projects
    Route::get('/projects', [AdminProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [AdminProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [AdminProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [AdminProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [AdminProjectController::class, 'update'])->name('projects.update');

    // Penawaran (RFQ Leads)
    Route::get('/penawaran', [AdminPenawaranController::class, 'index'])->name('penawaran.index');
    Route::post('/penawaran/{penawaran}/approve', [AdminPenawaranController::class, 'approve'])->name('penawaran.approve');
    Route::post('/penawaran/{penawaran}/reject', [AdminPenawaranController::class, 'reject'])->name('penawaran.reject');
    Route::delete('/penawaran/{penawaran}', [AdminPenawaranController::class, 'destroy'])->name('penawaran.destroy');

    // Surat Perjanjian (SPK)
    Route::get('/surat-perjanjian', [AdminSuratPerjanjianController::class, 'index'])->name('surat-perjanjian.index');
    Route::post('/surat-perjanjian', [AdminSuratPerjanjianController::class, 'store'])->name('surat-perjanjian.store');
    Route::patch('/surat-perjanjian/{suratPerjanjian}/status', [AdminSuratPerjanjianController::class, 'updateStatus'])->name('surat-perjanjian.status');
    Route::delete('/surat-perjanjian/{suratPerjanjian}', [AdminSuratPerjanjianController::class, 'destroy'])->name('surat-perjanjian.destroy');

    // Feedbacks
    Route::get('/feedbacks', [AdminFeedbackController::class, 'index'])->name('feedbacks.index');
    Route::patch('/feedbacks/{userFeedback}/status', [AdminFeedbackController::class, 'updateStatus'])->name('feedbacks.status');
    Route::delete('/feedbacks/{userFeedback}', [AdminFeedbackController::class, 'destroy'])->name('feedbacks.destroy');

    // Reviews
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/toggle', [AdminReviewController::class, 'toggleApprove'])->name('reviews.toggle');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // User Files & Designs
    Route::get('/user-files', [AdminUserFileController::class, 'index'])->name('user-files.index');
    Route::post('/user-files', [AdminUserFileController::class, 'store'])->name('user-files.store');
    Route::delete('/user-files/{userFile}', [AdminUserFileController::class, 'destroy'])->name('user-files.destroy');

    // Portfolios
    Route::get('/portfolios', [AdminPortfolioController::class, 'index'])->name('portfolios.index');
    Route::post('/portfolios', [AdminPortfolioController::class, 'store'])->name('portfolios.store');
    Route::delete('/portfolios/{portfolio}', [AdminPortfolioController::class, 'destroy'])->name('portfolios.destroy');

    // Berita & Artikel
    Route::get('/berita', [AdminBeritaController::class, 'index'])->name('berita.index');
    Route::post('/berita', [AdminBeritaController::class, 'store'])->name('berita.store');
    Route::put('/berita/{berita}', [AdminBeritaController::class, 'update'])->name('berita.update');
    Route::patch('/berita/{berita}/publish', [AdminBeritaController::class, 'togglePublish'])->name('berita.publish');
    Route::delete('/berita/{berita}', [AdminBeritaController::class, 'destroy'])->name('berita.destroy');

    // Team & Join Us
    Route::get('/team', [AdminTeamController::class, 'index'])->name('team.index');
    Route::post('/team', [AdminTeamController::class, 'store'])->name('team.store');
    Route::patch('/team/{teamMember}/status', [AdminTeamController::class, 'updateStatus'])->name('team.status');
    Route::patch('/team/{teamMember}/visibility', [AdminTeamController::class, 'toggleVisibility'])->name('team.visibility');
    Route::delete('/team/{teamMember}', [AdminTeamController::class, 'destroy'])->name('team.destroy');

    // Khusus Super Admin: Kelola Admin/User, Pengaturan Website, Hapus Proyek
    Route::middleware(['superadmin'])->group(function () {
        Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy'])->name('projects.destroy');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});

Route::get('/', function () {

    $reviews = Review::with('user')
        ->where('is_approved', true)
        ->inRandomOrder()
        ->take(5)
        ->get();
        
    $portofolios = \App\Models\Portfolio::inRandomOrder()->take(6)->get();

    return view('index', compact('reviews', 'portofolios'));
});