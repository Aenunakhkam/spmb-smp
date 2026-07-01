<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Loggable
{
    public function logActivity($action, $module, $details = null)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'details' => is_array($details) ? json_encode($details) : $details,
            'ip_address' => Request::ip(),
        ]);
    }
}
