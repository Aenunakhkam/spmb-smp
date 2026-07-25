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

    private function normalizeOptions($jsonValue, $defaultArray) {
        $decoded = json_decode($jsonValue, true);
        if (!$decoded) {
            $decoded = $defaultArray;
        }
        
        $normalized = [];
        foreach ($decoded as $item) {
            if (is_string($item)) {
                $normalized[] = ['name' => $item, 'description' => ''];
            } elseif (is_array($item) && isset($item['name'])) {
                $normalized[] = ['name' => $item['name'], 'description' => $item['description'] ?? ''];
            }
        }
        return $normalized;
    }

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
        $defaultPendidikan = ['Tidak Sekolah', 'SD/MI', 'SMP/MTs', 'SMA/SMK/MA', 'Diploma', 'S1', 'S2', 'S3'];
        $defaultPekerjaan = ['Tidak Bekerja', 'Nelayan', 'Petani', 'Peternak', 'PNS/TNI/POLRI', 'Karyawan Swasta', 'Pedagang Kecil', 'Pedagang Besar', 'Wiraswasta', 'Wirausaha', 'Buruh', 'Pensiunan', 'Tenaga Kerja Indonesia', 'Karyawan BUMN', 'Lainnya'];
        $defaultPenghasilan = ['Kurang dari 500.000', '500.000 - 999.999', '1.000.000 - 1.999.999', '2.000.000 - 4.999.999', '5.000.000 - 20.000.000', 'Lebih dari 20.000.000', 'Tidak Berpenghasilan'];
        $defaultAlasanKip = ['Pemegang KPS/PKH/KIP', 'Keluarga Miskin/Rentan Miskin', 'Yatim Piatu/Panti Asuhan/Panti Sosial', 'Dampak Bencana Alam', 'Pernah Drop Out', 'Siswa Miskin'];
        $defaultKebutuhanKhusus = ['Tidak Ada', 'Tunanetra', 'Tunarungu', 'Tunagrahita', 'Tunadaksa', 'Lainnya'];
        $defaultTempatTinggal = ['Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan'];
        $defaultEkstra = ['Tidak Ada', 'Pramuka', 'PMR', 'Paskibra', 'Olah Raga', 'Seni'];
        $defaultPeminatan = ['IPA', 'IPS', 'Bahasa', 'Agama', 'Umum'];
        $defaultTransportasi = ['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Mobil Pribadi', 'Angkutan Umum', 'Antar Jemput'];

        $defaultAchievementScores = [
            'Tingkat Kecamatan' => ['Juara 1' => 10, 'Juara 2' => 7, 'Juara 3' => 5],
            'Tingkat Kabupaten / Kota' => ['Juara 1' => 25, 'Juara 2' => 20, 'Juara 3' => 15],
            'Tingkat Provinsi' => ['Juara 1' => 50, 'Juara 2' => 40, 'Juara 3' => 30],
            'Tingkat Nasional' => ['Juara 1' => 75, 'Juara 2' => 60, 'Juara 3' => 45],
            'Tingkat Internasional' => ['Juara 1' => 100, 'Juara 2' => 80, 'Juara 3' => 60],
        ];
        $achievementScores = json_decode($settings['achievement_scores'] ?? json_encode($defaultAchievementScores), true);

        return Inertia::render('Admin/AdmissionSettings', [
            'settings' => [
                'registration_status' => $settings['registration_status'] ?? 'open',
                'registration_start_date' => $settings['registration_start_date'] ?? null,
                'registration_end_date' => $settings['registration_end_date'] ?? null,
                'ppdb_agenda' => json_decode($settings['ppdb_agenda'] ?? '[]', true),
                'frontend_faqs' => json_decode($settings['frontend_faqs'] ?? json_encode([
                    ['question' => 'Bagaimana cara konfirmasi pembayaran?', 'answer' => 'Sistem pendaftaran ini gratis untuk tahap awal. Untuk biaya administrasi lainnya akan diinfokan setelah verifikasi data awal selesai.'],
                    ['question' => 'Apakah berkas harus diantar ke sekolah?', 'answer' => 'Tidak perlu. Cukup unggah scan dokumen asli (format JPG/PDF) ke sistem ini. Fisik dokumen dibawa saat tes wawancara.'],
                    ['question' => 'Apa yang harus dilakukan setelah mendaftar?', 'answer' => 'Silakan simpan Nomor Pendaftaran dan Kode Akses Anda untuk memantau status verifikasi berkas oleh admin secara berkala.'],
                ]), true),
                'quota' => (int) ($settings['quota'] ?? 200),
                'report_semester' => $settings['report_semester'] ?? 'Kelas 6 Semester 2',
                'available_subjects' => $availableSubjects,
                'subjects_required' => $subjectsRequired,
                'achievement_scores' => $achievementScores,
                
                'opt_pendidikan' => $this->normalizeOptions($settings['opt_pendidikan'] ?? null, $defaultPendidikan),
                'opt_pekerjaan' => $this->normalizeOptions($settings['opt_pekerjaan'] ?? null, $defaultPekerjaan),
                'opt_penghasilan' => $this->normalizeOptions($settings['opt_penghasilan'] ?? null, $defaultPenghasilan),
                'opt_kebutuhan_khusus' => $this->normalizeOptions($settings['opt_kebutuhan_khusus'] ?? null, $defaultKebutuhanKhusus),
                'opt_tempat_tinggal' => $this->normalizeOptions($settings['opt_tempat_tinggal'] ?? null, $defaultTempatTinggal),
                'opt_ekstrakurikuler' => $this->normalizeOptions($settings['opt_ekstrakurikuler'] ?? null, $defaultEkstra),
                'opt_peminatan' => $this->normalizeOptions($settings['opt_peminatan'] ?? null, $defaultPeminatan),
                'opt_moda_transportasi' => $this->normalizeOptions($settings['opt_moda_transportasi'] ?? null, $defaultTransportasi),
                'opt_alasan_kip' => $this->normalizeOptions($settings['opt_alasan_kip'] ?? null, $defaultAlasanKip),
                'popup_banner' => $settings['popup_banner'] ?? null,
                'social_tiktok' => $settings['social_tiktok'] ?? '',
                'social_instagram' => $settings['social_instagram'] ?? '',
                'social_facebook' => $settings['social_facebook'] ?? '',
                'social_x' => $settings['social_x'] ?? '',
                'social_youtube' => $settings['social_youtube'] ?? '',
                'contact_whatsapp' => $settings['contact_whatsapp'] ?? '',
                'contact_email' => $settings['contact_email'] ?? '',
            ]
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'registration_status' => 'required|in:open,closed',
            'registration_start_date' => 'nullable|date',
            'registration_end_date' => 'nullable|date|after_or_equal:registration_start_date',
            'quota' => 'required|integer|min:1',
            'ppdb_agenda' => 'array',
            'frontend_faqs' => 'array',
            'report_semester' => 'required|string|max:255',
            'available_subjects' => 'required|array',
            'subjects_required' => 'required|array',
            'achievement_scores' => 'array',
            'popup_banner_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'opt_pendidikan' => 'required|array',
            'opt_pekerjaan' => 'required|array',
            'opt_penghasilan' => 'required|array',
            'opt_tempat_tinggal' => 'required|array',
            'opt_ekstrakurikuler' => 'required|array',
            'opt_peminatan' => 'required|array',
            'opt_moda_transportasi' => 'required|array',
            'opt_alasan_kip' => 'required|array',
            'social_tiktok' => 'nullable|string',
            'social_instagram' => 'nullable|string',
            'social_facebook' => 'nullable|string',
            'social_x' => 'nullable|string',
            'social_youtube' => 'nullable|string',
            'contact_whatsapp' => 'nullable|string',
            'contact_email' => 'nullable|email',
        ], [
            'popup_banner_file.max' => 'Ukuran gambar banner tidak boleh lebih dari 10MB (10240 KB).',
            'popup_banner_file.image' => 'File banner harus berupa gambar.',
            'popup_banner_file.mimes' => 'Format gambar banner harus berupa JPEG, PNG, atau JPG.',
        ]);

        $settingsToSave = [
            'registration_status' => $request->registration_status,
            'registration_start_date' => $request->registration_start_date,
            'registration_end_date' => $request->registration_end_date,
            'ppdb_agenda' => json_encode($request->ppdb_agenda ?? []),
            'frontend_faqs' => json_encode($request->frontend_faqs ?? []),
            'quota' => $request->quota,
            'report_semester' => $request->report_semester,
            'available_subjects' => json_encode($request->available_subjects),
            'subjects_required' => json_encode($request->subjects_required),
            'achievement_scores' => json_encode($request->achievement_scores ?? []),
            
            'opt_pendidikan' => json_encode($request->opt_pendidikan),
            'opt_pekerjaan' => json_encode($request->opt_pekerjaan),
            'opt_penghasilan' => json_encode($request->opt_penghasilan),
            'opt_kebutuhan_khusus' => json_encode($request->opt_kebutuhan_khusus),
            'opt_tempat_tinggal' => json_encode($request->opt_tempat_tinggal),
            'opt_ekstrakurikuler' => json_encode($request->opt_ekstrakurikuler),
            'opt_peminatan' => json_encode($request->opt_peminatan),
            'opt_moda_transportasi' => json_encode($request->opt_moda_transportasi),
            'opt_alasan_kip' => json_encode($request->opt_alasan_kip),
            'social_tiktok' => $request->social_tiktok,
            'social_instagram' => $request->social_instagram,
            'social_facebook' => $request->social_facebook,
            'social_x' => $request->social_x,
            'social_youtube' => $request->social_youtube,
            'contact_whatsapp' => $request->contact_whatsapp,
            'contact_email' => $request->contact_email,
        ];

        if ($request->hasFile('popup_banner_file')) {
            $file = $request->file('popup_banner_file');
            $path = $file->store('banners', 'public');
            
            $oldBanner = Setting::where('key', 'popup_banner')->first();
            if ($oldBanner && $oldBanner->value) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldBanner->value);
            }
            Setting::updateOrCreate(['key' => 'popup_banner'], ['value' => $path]);
        }

        foreach ($settingsToSave as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->logActivity('update_admission_settings', 'Setting', [
            'registration_status' => $request->registration_status,
            'quota' => $request->quota,
        ]);

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function deleteBanner()
    {
        $banner = Setting::where('key', 'popup_banner')->first();
        if ($banner && $banner->value) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->value);
            $banner->delete();
        }
        return back()->with('success', 'Banner berhasil dihapus.');
    }
}
