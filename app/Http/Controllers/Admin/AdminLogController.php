<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Inertia\Inertia;

class AdminLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Logs', [
            'logs' => $logs,
        ]);
    }

    public function clear()
    {
        AuditLog::truncate();
        
        return redirect()->back()->with('success', 'Semua log aktivitas berhasil dihapus.');
    }
}
