<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdmissionSettingController extends Controller
{
    use Loggable;

    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        $defaultAvailableSubjects = json_encode([
            ['key' => 'mathematics', 'label' => 'Matematika'],
            ['key' => 'indonesian', 'label' => 'Bahasa Indonesia'],
            ['key' => 'english', 'label' => 'Bahasa Inggris'],
            ['key' => 'religion', 'label' => 'Pendidikan Agama'],
            ['key' => 'ipa', 'label' => 'IPA (Ilmu Pengetahuan Alam)'],
            ['key' => 'ips', 'label' => 'IPS (Ilmu Pengetahuan Sosial)'],
            ['key' => 'pkn', 'label' => 'PKN (Pendidikan Kewarganegaraan)'],
        ]);
        $availableSubjects = json_decode($settings['available_subjects'] ?? $defaultAvailableSubjects, true);

        $defaultSubjects = json_encode(['mathematics', 'indonesian', 'english', 'religion']);
        $subjectsRequired = json_decode($settings['subjects_required'] ?? $defaultSubjects, true);

        // Default options
        $defaultPendidikan = json_encode(['Tidak Sekolah', 'SD/MI', 'SMP/MTs', 'SMA/SMK/MA', 'Diploma', 'S1', 'S2', 'S3']);
        $defaultPekerjaan = json_encode(['Tidak Bekerja', 'Nelayan', 'Petani', 'Peternak', 'PNS/TNI/POLRI', 'Karyawan Swasta', 'Pedagang Kecil', 'Pedagang Besar', 'Wiraswasta', 'Wirausaha', 'Buruh', 'Pensiunan', 'Tenaga Kerja Indonesia', 'Karyawan BUMN', 'Lainnya']);
        $defaultPenghasilan = json_encode(['Kurang dari 500.000', '500.000 - 999.999', '1.000.000 - 1.999.999', '2.000.000 - 4.999.999', '5.000.000 - 20.000.000', 'Lebih dari 20.000.000', 'Tidak Berpenghasilan']);
        $defaultAlasanKip = json_encode(['Pemegang KPS/PKH/KIP', 'Keluarga Miskin/Rentan Miskin', 'Yatim Piatu/Panti Asuhan/Panti Sosial', 'Dampak Bencana Alam', 'Pernah Drop Out', 'Siswa Miskin']);
        $defaultKebutuhanKhusus = json_encode(['Tidak Ada', 'Tunanetra', 'Tunarungu', 'Tunagrahita', 'Tunadaksa', 'Lainnya']);
        $defaultTempatTinggal = json_encode(['Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan']);
        $defaultEkstra = json_encode(['Tidak Ada', 'Pramuka', 'PMR', 'Paskibra', 'Olah Raga', 'Seni']);
        $defaultTransportasi = json_encode(['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Mobil Pribadi', 'Angkutan Umum', 'Antar Jemput']);

        return Inertia::render('Admin/AdmissionSettings', [
            'settings' => [
                'registration_status' => $settings['registration_status'] ?? 'open',
                'quota' => (int) ($settings['quota'] ?? 200),
                'report_semester' => $settings['report_semester'] ?? 'Kelas 6 Semester 2',
                'available_subjects' => $availableSubjects,
                'subjects_required' => $subjectsRequired,
                
                'opt_pendidikan' => json_decode($settings['opt_pendidikan'] ?? $defaultPendidikan, true),
                'opt_pekerjaan' => json_decode($settings['opt_pekerjaan'] ?? $defaultPekerjaan, true),
                'opt_penghasilan' => json_decode($settings['opt_penghasilan'] ?? $defaultPenghasilan, true),
                'opt_kebutuhan_khusus' => json_decode($settings['opt_kebutuhan_khusus'] ?? $defaultKebutuhanKhusus, true),
                'opt_tempat_tinggal' => json_decode($settings['opt_tempat_tinggal'] ?? $defaultTempatTinggal, true),
                'opt_ekstrakurikuler' => json_decode($settings['opt_ekstrakurikuler'] ?? $defaultEkstra, true),
                'opt_moda_transportasi' => json_decode($settings['opt_moda_transportasi'] ?? $defaultTransportasi, true),
                'opt_alasan_kip' => json_decode($settings['opt_alasan_kip'] ?? $defaultAlasanKip, true),
            ]
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'registration_status' => 'required|in:open,closed',
            'quota' => 'required|integer|min:1',
            'report_semester' => 'required|string',
            'available_subjects' => 'required|array|min:1',
            'subjects_required' => 'required|array',
            
            'opt_pendidikan' => 'required|array',
            'opt_pekerjaan' => 'required|array',
            'opt_penghasilan' => 'required|array',
            'opt_kebutuhan_khusus' => 'required|array',
            'opt_tempat_tinggal' => 'required|array',
            'opt_ekstrakurikuler' => 'required|array',
            'opt_moda_transportasi' => 'required|array',
            'opt_alasan_kip' => 'required|array',
        ]);

        $settingsToSave = [
            'registration_status' => $request->registration_status,
            'quota' => $request->quota,
            'report_semester' => $request->report_semester,
            'available_subjects' => json_encode($request->available_subjects),
            'subjects_required' => json_encode($request->subjects_required),
            
            'opt_pendidikan' => json_encode($request->opt_pendidikan),
            'opt_pekerjaan' => json_encode($request->opt_pekerjaan),
            'opt_penghasilan' => json_encode($request->opt_penghasilan),
            'opt_kebutuhan_khusus' => json_encode($request->opt_kebutuhan_khusus),
            'opt_tempat_tinggal' => json_encode($request->opt_tempat_tinggal),
            'opt_ekstrakurikuler' => json_encode($request->opt_ekstrakurikuler),
            'opt_moda_transportasi' => json_encode($request->opt_moda_transportasi),
            'opt_alasan_kip' => json_encode($request->opt_alasan_kip),
        ];

        foreach ($settingsToSave as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->logActivity('update_admission_settings', 'Setting', [
            'registration_status' => $request->registration_status,
            'quota' => $request->quota,
        ]);

        return back()->with('success', 'Pengaturan PPDB berhasil diperbarui.');
    }
}
