<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Setting;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\RegistrationExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminRegistrationController extends Controller
{
    use Loggable;
    public function index(Request $request)
    {
        $query = Registration::with('studentDetail')
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('studentDetail', function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('nisn', 'like', "%{$request->search}%");
            })->orWhere('registration_number', 'like', "%{$request->search}%");
        }

        return Inertia::render('Admin/RegistrationList', [
            'registrations' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['status', 'search']),
            'stats' => [
                'total' => Registration::count(),
                'pending' => Registration::where('status', 'pending')->count(),
                'verified' => Registration::where('status', 'verified')->count(),
                'incomplete' => Registration::where('status', 'incomplete')->count(),
                'aid_recipients' => \App\Models\ParentDetail::whereNotNull('aid_card_number')->where('aid_card_number', '!=', '')->count(),
            ]
        ]);
    }

    public function exportExcel()
    {
        $this->logActivity('Export Data Pendaftar Excel', 'Admin mengekspor data pendaftar ke format Excel.');
        return Excel::download(new RegistrationExport, 'Data_Pendaftar_SPMB.xlsx');
    }

    public function exportPdf()
    {
        $registrations = Registration::with(['studentDetail', 'grade'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $settings = Setting::pluck('value', 'key')->toArray();

        // 215mm x 330mm to points is approx 609.45 x 935.43 (F4 paper)
        $pdf = Pdf::loadView('print.registrations-pdf', compact('registrations', 'settings'))
            ->setPaper(array(0, 0, 609.45, 935.43), 'landscape');
            
        $this->logActivity('Export Data Pendaftar PDF', 'Admin mengekspor data pendaftar ke format PDF.');

        return $pdf->download('Data_Pendaftar_SPMB.pdf');
    }

    public function show($id)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail', 'grade', 'documents'])
            ->findOrFail($id);

        return Inertia::render('Admin/VerificationDetail', [
            'registration' => $registration,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:incomplete,pending,revision,verified,passed,failed',
            'admin_notes' => 'nullable|string',
        ]);

        $registration = Registration::findOrFail($id);
        $oldStatus = $registration->status;
        $registration->update($request->all());

        $this->logActivity('verify_registration', 'Registration', [
            'registration_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
        ]);

        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    public function runRanking()
    {
        $quota = Setting::where('key', 'quota')->first()?->value ?? 200;
        
        $registrations = Registration::where('status', 'verified')->with('studentDetail')->get();

        // Urutkan berdasarkan skor akhir (Rapor + Prestasi) tertinggi, lalu pendaftar tercepat jika seri
        $registrations = $registrations->sort(function ($a, $b) {
            if ($a->final_score == $b->final_score) {
                return $a->created_at <=> $b->created_at;
            }
            return $b->final_score <=> $a->final_score;
        })->values();

        foreach ($registrations as $index => $registration) {
            $rank = $index + 1;
            $status = $rank <= $quota ? 'passed' : 'failed';
            
            $registration->update([
                'rank' => $rank,
                'status' => $status
            ]);
        }

        $this->logActivity('run_ranking', 'System', [
            'total_processed' => $registrations->count(),
            'quota' => $quota,
        ]);

        return back()->with('success', 'Proses seleksi dan ranking otomatis berhasil dijalankan.');
    }

    public function edit($id)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail', 'grade'])->findOrFail($id);
        return Inertia::render('Admin/RegistrationEdit', [
            'registration' => $registration,
        ]);
    }

    public function update(Request $request, $id)
    {
        $registration = Registration::with('studentDetail')->findOrFail($id);
        
        $request->validate([
            'student_detail.full_name' => 'required|string',
            'student_detail.nisn' => 'required|string|size:10',
            'student_detail.phone' => 'required|string',
        ]);

        if ($request->has('student_detail')) {
            $registration->studentDetail->update($request->input('student_detail'));
        }

        $this->logActivity('update_registration', 'Registration', [
            'registration_id' => $id,
        ]);

        return redirect()->route('admin.registrations.index')->with('success', 'Data pendaftar berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->delete();

        $this->logActivity('delete_registration', 'Registration', [
            'registration_id' => $id,
        ]);

        return redirect()->route('admin.registrations.index')->with('success', 'Data pendaftar berhasil dihapus.');
    }
}
