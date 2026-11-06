<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Penawaran;
use App\Models\SuratPerjanjian;
use App\Models\User;
use App\Models\UserFeedback;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProjects     = Project::count();
        $activeProjects    = Project::where('status', 'aktif')->count();
        $completedProjects = Project::where('status', 'selesai')->count();
        
        $totalPenawaran    = Penawaran::count();
        $totalSurat        = SuratPerjanjian::count();
        $pendingSurat      = SuratPerjanjian::where('status', 'pending')->count();
        
        $totalFeedbacks    = UserFeedback::count();
        $pendingFeedbacks  = UserFeedback::where('status', 'pending')->count();
        
        $totalReviews      = Review::count();
        $pendingReviews    = Review::where('is_approved', false)->count();
        
        $totalClients      = User::where('role', 'owner')->count();

        // 5 Penawaran terbaru
        $recentPenawaran   = Penawaran::latest()->take(6)->get();

        // 5 Proyek terbaru
        $recentProjects    = Project::with('user')->latest()->take(6)->get();

        // 5 Masukan lapangan terbaru
        $recentFeedbacks   = UserFeedback::with(['user', 'project'])->latest()->take(5)->get();

        // Distribusi Kategori Proyek
        $categoriesCount = [
            'Pembangunan Rumah'     => Project::where('kategori', 'Pembangunan Rumah')->count(),
            'Gedung Komersial'      => Project::where('kategori', 'Gedung Komersial')->count(),
            'Renovasi Bangunan'     => Project::where('kategori', 'Renovasi Bangunan')->count(),
            'Konsultasi Konstruksi' => Project::where('kategori', 'Konsultasi Konstruksi')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'totalPenawaran',
            'totalSurat',
            'pendingSurat',
            'totalFeedbacks',
            'pendingFeedbacks',
            'totalReviews',
            'pendingReviews',
            'totalClients',
            'recentPenawaran',
            'recentProjects',
            'recentFeedbacks',
            'categoriesCount'
        ));
    }
}

