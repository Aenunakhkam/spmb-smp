<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\StudentDetail;
use App\Models\ParentDetail;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $quota = (int) (Setting::where('key', 'quota')->first()?->value ?? 200);
        $academicYear = Setting::where('key', 'academic_year')->first()?->value ?? '2026/2027';
        $registrationStatus = Setting::where('key', 'registration_status')->first()?->value ?? 'open';

        // Base Stats
        $stats = [
            'total' => Registration::count(),
            'pending' => Registration::where('status', 'pending')->count(),
            'verified' => Registration::where('status', 'verified')->count(),
            'incomplete' => Registration::where('status', 'incomplete')->count(),
            'passed' => Registration::where('status', 'passed')->count(),
            'failed' => Registration::where('status', 'failed')->count(),
            'quota' => $quota,
            'academic_year' => $academicYear,
            'status' => $registrationStatus,
            'show_ranking' => Setting::where('key', 'show_ranking')->first()?->value !== '0',
        ];

        // Gender Stats
        $genderStats = StudentDetail::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->pluck('total', 'gender')
            ->toArray();

        $stats['gender'] = [
            'L' => $genderStats['L'] ?? 0,
            'P' => $genderStats['P'] ?? 0,
        ];

        // Top School Stats
        $stats['top_schools'] = StudentDetail::select('origin_school_name', DB::raw('count(*) as total'))
            ->whereNotNull('origin_school_name')
            ->where('origin_school_name', '!=', 'Belum Diisi')
            ->groupBy('origin_school_name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->toArray();

        // Aid Card Recipients
        $stats['aid_recipients'] = ParentDetail::where(function($q) {
            $q->whereNotNull('aid_card_number')->where('aid_card_number', '!=', '');
        })->count();

        // Recent Activity Logs
        $recentLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user?->name ?? 'System',
                    'action' => $log->action,
                    'module' => $log->module,
                    'details' => $log->details,
                    'created_at' => $log->created_at->diffForHumans(),
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentLogs' => $recentLogs,
        ]);
    }
}
