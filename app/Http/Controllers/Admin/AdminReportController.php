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
use App\Exports\MasterDataExport;
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
        $registrations = Registration::with(['studentDetail', 'parentDetail', 'grade'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'totalRegistrations' => Registration::count(),
            'byStatus' => Registration::select('status', DB::raw('count(*) as total'))->groupBy('status')->get(),
            'byGender' => StudentDetail::select('gender', DB::raw('count(*) as total'))->groupBy('gender')->get()->map(function ($item) {
                return ['label' => $item->gender === 'L' ? 'Laki-laki' : ($item->gender === 'P' ? 'Perempuan' : 'Tidak Diketahui'), 'total' => $item->total];
            }),
            'bySchool' => StudentDetail::select('origin_school_name', DB::raw('count(*) as total'))->groupBy('origin_school_name')->orderByDesc('total')->get(),
            'registrations' => $registrations,
            'settings' => Setting::pluck('value', 'key')->toArray()
        ];
    }

    public function exportPdf()
    {
        $data = $this->getReportData();
        // F4 Landscape: 215mm x 330mm
        $pdf = Pdf::loadView('print.reports-pdf', $data)
            ->setPaper([0, 0, 609.45, 935.43], 'landscape');

        return $pdf->download('Laporan_Resmi_PPDB_' . date('Ymd') . '.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new ReportExport(), 'Laporan_Statistik_PPDB.xlsx');
    }

    public function exportMaster()
    {
        $filename = 'Master_Data_Siswa_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new MasterDataExport(), $filename);
    }
}
