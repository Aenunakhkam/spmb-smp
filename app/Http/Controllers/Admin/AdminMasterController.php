<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminMasterController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::with(['studentDetail', 'parentDetail', 'grade', 'documents'])
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->whereHas('studentDetail', function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('nisn', 'like', "%{$request->search}%");
            })->orWhere('registration_number', 'like', "%{$request->search}%");
        }

        return Inertia::render('Admin/MasterData', [
            'registrations' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }
}
