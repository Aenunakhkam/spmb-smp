<?php

namespace App\Http\Controllers;

use App\Models\StudentDetail;
use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\InterestExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminInterestController extends Controller
{
    public function index(Request $request)
    {
        // Since minat is inside registration's additional_data JSON, we fetch registrations.
        $registrations = Registration::with('studentDetail')
            ->where('academic_year', \App\Models\Setting::where('key', 'academic_year')->first()?->value ?? date('Y'))
            ->get();

        $stats = [];
        foreach ($registrations as $reg) {
            $minat = $reg->additional_data['major'] ?? 'Belum Memilih';
            if (!isset($stats[$minat])) {
                $stats[$minat] = 0;
            }
            $stats[$minat]++;
        }

        // Format stats for Chart
        $chartData = [];
        foreach ($stats as $key => $count) {
            $chartData[] = [
                'label' => $key,
                'count' => $count,
            ];
        }

        // 2. Data Table Logic
        // We can either return all data to Vue for client-side filtering or do it server-side.
        // We'll return all data since it's typically manageable, or paginate if requested.
        $query = Registration::with('studentDetail')
            ->where('academic_year', \App\Models\Setting::where('key', 'academic_year')->first()?->value ?? date('Y'));
            
        if ($request->has('minat') && $request->minat !== 'Semua') {
            $minat = $request->minat;
            $query->where('additional_data->major', $minat);
        }

        $students = $query->paginate(20)->through(function ($item) {
            return [
                'id' => $item->id,
                'registration_number' => $item->registration_number ?? '-',
                'full_name' => $item->studentDetail->full_name ?? '-',
                'gender' => $item->studentDetail->gender ?? '-',
                'phone' => $item->studentDetail->phone ?? '-',
                'minat' => $item->additional_data['major'] ?? 'Belum Memilih',
                'status' => $item->status ?? '-',
            ];
        });

        // Get list of unique minat for the dropdown filter
        $minatOptions = collect($chartData)->pluck('label')->toArray();
        array_unshift($minatOptions, 'Semua');

        return Inertia::render('Admin/Interests', [
            'chartData' => $chartData,
            'students' => $students,
            'filters' => $request->only('minat'),
            'minatOptions' => $minatOptions,
        ]);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new InterestExport($request->minat), 'Data_Peminatan_Siswa.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Registration::with('studentDetail')
            ->where('academic_year', Setting::where('key', 'academic_year')->first()?->value ?? date('Y'));
            
        $minatFilter = 'Semua';
        if ($request->has('minat') && $request->minat !== 'Semua') {
            $minatFilter = $request->minat;
            $query->where('additional_data->major', $minatFilter);
        }

        $registrations = $query->orderBy('created_at', 'asc')->get();
        $settings = Setting::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('print.interests-pdf', compact('registrations', 'settings', 'minatFilter'))
            ->setPaper(array(0, 0, 609.45, 935.43), 'landscape');

        return $pdf->download('Data_Peminatan_Siswa.pdf');
    }
}
