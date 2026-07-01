<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\StudentDetail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminReportController extends Controller
{
    public function index()
    {
        $totalRegistrations = Registration::count();

        // 1. By Status
        $byStatus = Registration::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // 2. By Gender
        $byGender = StudentDetail::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->gender === 'L' ? 'Laki-laki' : ($item->gender === 'P' ? 'Perempuan' : 'Tidak Diketahui'),
                    'total' => $item->total
                ];
            });

        // 3. By Origin School (Top 10)
        $bySchool = StudentDetail::select('origin_school_name', DB::raw('count(*) as total'))
            ->groupBy('origin_school_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Reports', [
            'totalRegistrations' => $totalRegistrations,
            'byStatus' => $byStatus,
            'byGender' => $byGender,
            'bySchool' => $bySchool,
        ]);
    }

    private function getReportData()
    {
        return [
            'totalRegistrations' => Registration::count(),
            'byStatus' => Registration::select('status', DB::raw('count(*) as total'))->groupBy('status')->get(),
            'byGender' => StudentDetail::select('gender', DB::raw('count(*) as total'))->groupBy('gender')->get()->map(function ($item) {
                return ['label' => $item->gender === 'L' ? 'Laki-laki' : ($item->gender === 'P' ? 'Perempuan' : 'Tidak Diketahui'), 'total' => $item->total];
            }),
            'bySchool' => StudentDetail::select('origin_school_name', DB::raw('count(*) as total'))->groupBy('origin_school_name')->orderByDesc('total')->get(),
            'settings' => Setting::pluck('value', 'key')->toArray()
        ];
    }

    public function exportPdf()
    {
        $data = $this->getReportData();
        $pdf = Pdf::loadView('print.reports-pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Statistik_PPDB.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new ReportExport(), 'Laporan_Statistik_PPDB.xlsx');
    }
}
