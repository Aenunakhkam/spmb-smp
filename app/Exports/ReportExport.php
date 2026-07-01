<?php

namespace App\Exports;

use App\Models\Registration;
use App\Models\StudentDetail;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        $totalRegistrations = Registration::count();
        $byStatus = Registration::select('status', DB::raw('count(*) as total'))->groupBy('status')->get();
        $byGender = StudentDetail::select('gender', DB::raw('count(*) as total'))->groupBy('gender')->get()->map(function ($item) {
            return ['label' => $item->gender === 'L' ? 'Laki-laki' : ($item->gender === 'P' ? 'Perempuan' : 'Tidak Diketahui'), 'total' => $item->total];
        });
        $bySchool = StudentDetail::select('origin_school_name', DB::raw('count(*) as total'))->groupBy('origin_school_name')->orderByDesc('total')->get();
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('print.reports-pdf', [
            'totalRegistrations' => $totalRegistrations,
            'byStatus' => $byStatus,
            'byGender' => $byGender,
            'bySchool' => $bySchool,
            'settings' => $settings,
            'isExcel' => true
        ]);
    }
}
