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

Route::get('/layanan/{slug}', [LayananController::class, 'show'])->name('layanan.show');

Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('berita.show');

// Rute yang membutuhkan user login
Route::middleware(['auth'])->group(function () {
    Route::post('/user-files', [UserFileController::class, 'store'])->name('user-files.store');
    Route::delete('/user-files/{userFile}', [UserFileController::class, 'destroy'])->name('user-files.destroy');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/user-feedback', [UserFeedbackController::class, 'store'])->name('user-feedback.store');
});

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

// login
Route::view('/register', 'auth.register')->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/', function () {

    $reviews = Review::with('user')
        ->where('is_approved', true)
        ->inRandomOrder()
        ->take(5)
        ->get();
        
    $portofolios = \App\Models\Portfolio::inRandomOrder()->take(6)->get();

    return view('index', compact('reviews', 'portofolios'));
});