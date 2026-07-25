<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\StudentDetail;
use App\Models\ParentDetail;
use App\Models\Grade;
use App\Models\Document;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function start()
    {
        return Inertia::render('Registration/Start');
    }

    public function storeInitial(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'nisn' => 'required|string|size:10|unique:student_details,nisn',
            'phone' => 'required|string|max:15',
        ], [
            'nisn.unique' => 'NISN YANG ANDA MASUKAN SUDAH TERDAFTAR SILAKAN HUBUNGI CS ATAU PANITIA SPMB',
        ]);

        return DB::transaction(function () use ($request) {
            $year = Setting::where('key', 'academic_year')->first()?->value ?? date('Y');
            $yearClean = str_replace(['/', '-', ' '], '', $year);
            
            // Generate registration number (Format: YYYYMMDDXXXX or simplified)
            $count = Registration::where('academic_year', $year)->count() + 1;
            $regNumber = $yearClean . str_pad($count, 4, '0', STR_PAD_LEFT);
            $accessCode = strtoupper(Str::random(8));

            $registration = Registration::create([
                'registration_number' => $regNumber,
                'access_code' => $accessCode,
                'status' => 'incomplete',
                'step' => 1,
                'academic_year' => $year,
            ]);

            StudentDetail::create([
                'registration_id' => $registration->id,
                'nisn' => $request->nisn,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                // Required fields with placeholders for initial step
                'nik' => 'PENDING-' . $regNumber, 
                'gender' => 'L',
                'place_of_birth' => 'Belum Diisi',
                'date_of_birth' => now(),
                'religion' => 'Islam',
            ]);

            try {
                $waMessage = "Halo {$request->full_name}, pendaftaran awal Anda berhasil!\n\n"
                           . "Berikut adalah data akses Anda:\n"
                           . "Nomor Pendaftaran: *{$regNumber}*\n"
                           . "Kode Akses: *{$accessCode}*\n\n"
                           . "Gunakan data ini untuk masuk ke Portal Siswa dan melengkapi berkas.";
                
                Http::timeout(5)->post('http://localhost:3000/send-message', [
                    'number' => $request->phone,
                    'message' => $waMessage
                ]);
            } catch (\Exception $e) {
                // Ignore if WA server is down to not block registration flow
            }

            return redirect()->route('student.dashboard', [$regNumber, $accessCode])
                ->with('success', 'Registrasi awal berhasil! Nomor Pendaftaran dan Kode Akses Anda juga telah dikirim via WhatsApp.');
        });
    }

    public function showForm($number, $code)
    {
        $registration = Registration::where('registration_number', $number)
            ->where('access_code', $code)
            ->with(['studentDetail', 'parentDetail', 'grade', 'documents'])
            ->firstOrFail();

        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Default options
        $defaultPendidikan = json_encode(['Tidak Sekolah', 'SD/MI', 'SMP/MTs', 'SMA/SMK/MA', 'Diploma', 'S1', 'S2', 'S3']);
        $defaultPekerjaan = json_encode(['Tidak Bekerja', 'Nelayan', 'Petani', 'Peternak', 'PNS/TNI/POLRI', 'Karyawan Swasta', 'Pedagang Kecil', 'Pedagang Besar', 'Wiraswasta', 'Wirausaha', 'Buruh', 'Pensiunan', 'Tenaga Kerja Indonesia', 'Karyawan BUMN', 'Lainnya']);
        $defaultPenghasilan = json_encode(['Kurang dari 500.000', '500.000 - 999.999', '1.000.000 - 1.999.999', '2.000.000 - 4.999.999', '5.000.000 - 20.000.000', 'Lebih dari 20.000.000', 'Tidak Berpenghasilan']);
        $defaultKebutuhanKhusus = json_encode(['Tidak', 'Netra', 'Rungu', 'Grahita Ringan', 'Grahita Sedang', 'Daksa Ringan', 'Daksa Sedang', 'Laras', 'Wicara', 'Tuna Ganda', 'Hiper Aktif', 'Cerdas Istimewa', 'Bakat Istimewa', 'Kesulitan Belajar', 'Narkoba', 'Indigo', 'Down Syndrome', 'Autis']);
        $defaultTempatTinggal = json_encode(['Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan']);
        $defaultEkstra = json_encode(['Tidak Ada', 'Pramuka', 'PMR', 'Paskibra', 'Olah Raga', 'Seni']);
        $defaultTransportasi = json_encode(['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Mobil Pribadi', 'Angkutan Umum', 'Antar Jemput']);
        $defaultPeminatan = json_encode(['Reguler', 'Sains', 'Bahasa', 'Olahraga/Seni', 'Tahfidz']);

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
        $subjectsRequiredKeys = json_decode($settings['subjects_required'] ?? $defaultSubjects, true);
        
        $subjectsRequiredDetails = array_filter($availableSubjects, function($subj) use ($subjectsRequiredKeys) {
            return in_array($subj['key'], $subjectsRequiredKeys);
        });

        return Inertia::render('Registration/MainForm', [
            'registration' => $registration,
            'options' => [
                'pendidikan' => json_decode($settings['opt_pendidikan'] ?? $defaultPendidikan, true),
                'pekerjaan' => json_decode($settings['opt_pekerjaan'] ?? $defaultPekerjaan, true),
                'penghasilan' => json_decode($settings['opt_penghasilan'] ?? $defaultPenghasilan, true),
                'tempat_tinggal' => json_decode($settings['opt_tempat_tinggal'] ?? $defaultTempatTinggal, true),
                'ekstrakurikuler' => json_decode($settings['opt_ekstrakurikuler'] ?? $defaultEkstra, true),
                'moda_transportasi' => json_decode($settings['opt_moda_transportasi'] ?? $defaultTransportasi, true),
                'peminatan' => json_decode($settings['opt_peminatan'] ?? $defaultPeminatan, true),
                'subjects_required' => array_values($subjectsRequiredDetails),
                'available_subjects' => $availableSubjects,
                'report_semester' => $settings['report_semester'] ?? 'Kelas 6 Semester 2',
            ]
        ]);
    }

    public function saveBiodata(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'nik' => 'required|numeric|digits:16',
            'no_kk' => 'required|numeric|digits:16',
            'gender' => 'required|in:L,P',
            'place_of_birth' => 'required|string',
            'date_of_birth' => 'required|date',
            'religion' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'village' => 'required|string',
            'address' => 'required|string',
            'postal_code' => 'required|string',
            'origin_school_name' => 'required|string',
        ]);

        $detail = StudentDetail::where('registration_id', $request->registration_id)->firstOrFail();
        
        $baseData = $request->only([
            'nik', 'gender', 'place_of_birth', 'date_of_birth', 'religion', 
            'province', 'city', 'district', 'village', 'address', 'postal_code', 'origin_school_name'
        ]);

        $additionalData = $request->only(['no_kk', 'special_needs', 'residence', 'extracurricular', 'transportation', 'minat']);
        
        $detail->update(array_merge($baseData, [
            'additional_data' => array_merge($detail->additional_data ?? [], $additionalData)
        ]));

        Registration::find($request->registration_id)->update(['step' => max(2, Registration::find($request->registration_id)->step)]);

        return back()->with('success', 'Biodata berhasil disimpan.');
    }

    public function saveParent(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'father_name' => 'required|string',
            'father_occupation' => 'nullable|string',
            'mother_name' => 'required|string',
            'mother_occupation' => 'nullable|string',
            'parent_phone' => 'required|string',
            'parent_address' => 'required|string',
        ]);

        $baseData = $request->only([
            'registration_id', 'father_name', 'father_occupation', 'mother_name', 'mother_occupation', 'parent_phone', 'parent_address', 'aid_card_number'
        ]);

        $additionalData = $request->only(['father_education', 'mother_education', 'parent_income']);

        $parent = ParentDetail::updateOrCreate(
            ['registration_id' => $request->registration_id],
            $baseData
        );
        
        $parent->update([
            'additional_data' => array_merge($parent->additional_data ?? [], $additionalData)
        ]);

        Registration::find($request->registration_id)->update(['step' => max(3, Registration::find($request->registration_id)->step)]);

        return back()->with('success', 'Data orang tua berhasil disimpan.');
    }

    public function saveSection(Request $request)
    {
        $checkReg = Registration::findOrFail($request->registration_id);
        if (!in_array($checkReg->status, ['incomplete', 'revision'])) {
            return back()->withErrors(['error' => 'Data pendaftaran sudah dikunci dan tidak dapat diubah.']);
        }
        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'student_details.full_name' => 'nullable|string',
            'student_details.nisn' => 'nullable|string',
            'student_details.nik' => 'nullable|numeric|digits:16',
            'student_details.phone' => 'nullable|string',
            'student_details.place_of_birth' => 'nullable|string',
            'student_details.date_of_birth' => 'nullable|date',
            'student_details.religion' => 'nullable|string',
            'student_details.address' => 'nullable|string',
            'student_details.province' => 'nullable|string',
            'student_details.city' => 'nullable|string',
            'student_details.district' => 'nullable|string',
            'student_details.village' => 'nullable|string',
            'student_details.origin_school_name' => 'nullable|string',
            'parent_details.father_name' => 'nullable|string',
            'parent_details.mother_name' => 'nullable|string',
            'parent_details.parent_phone' => 'nullable|string',
            'student_details.additional_data.kk_number' => 'nullable|numeric|digits:16',
            'parent_details.additional_data.father_nik' => 'nullable|numeric|digits:16',
            'parent_details.additional_data.mother_nik' => 'nullable|numeric|digits:16',
        ], [
            'student_details.nik.numeric' => 'NIK harus berupa angka.',
            'student_details.nik.digits' => 'NIK harus berjumlah 16 angka.',
            'student_details.additional_data.kk_number.digits' => 'Nomor Kartu Keluarga harus berjumlah 16 angka.',
            'parent_details.additional_data.father_nik.digits' => 'NIK Ayah harus berjumlah 16 angka.',
            'parent_details.additional_data.mother_nik.digits' => 'NIK Ibu harus berjumlah 16 angka.',
            'student_details.additional_data.kk_number.numeric' => 'Nomor Kartu Keluarga harus berupa angka.',
            'parent_details.additional_data.father_nik.numeric' => 'NIK Ayah harus berupa angka.',
            'parent_details.additional_data.mother_nik.numeric' => 'NIK Ibu harus berupa angka.',
        ]);
        
        $regId = $request->registration_id;
        
        if ($request->has('student_details')) {
            $student = StudentDetail::where('registration_id', $regId)->firstOrFail();
            $studentData = $request->all()['student_details'] ?? $request->input('student_details');
            
            if (isset($studentData['additional_data'])) {
                $existing = $student->additional_data ?? [];
                
                // Handle file uploads for achievements if any
                if (isset($studentData['additional_data']['achievements']) && is_array($studentData['additional_data']['achievements'])) {
                    foreach ($studentData['additional_data']['achievements'] as $index => &$achievement) {
                        $fileKey = "student_details.additional_data.achievements.{$index}.certificate_file";
                        if ($request->hasFile($fileKey)) {
                            $path = $request->file($fileKey)->store('certificates', 'public');
                            $achievement['certificate_path'] = $path;
                        }
                        // Remove the File object from being stored in JSON
                        unset($achievement['certificate_file']);
                    }
                }

                $studentData['additional_data'] = array_merge($existing, $studentData['additional_data']);
            }
            $student->update($studentData);
        }
        
        if ($request->has('parent_details')) {
            $parent = ParentDetail::firstOrCreate(['registration_id' => $regId]);
            $parentData = $request->input('parent_details');
            if (isset($parentData['additional_data'])) {
                $existing = $parent->additional_data ?? [];
                $parentData['additional_data'] = array_merge($existing, $parentData['additional_data']);
            }
            $parent->update($parentData);
        }
        
        if ($request->has('registration')) {
            $registration = Registration::findOrFail($regId);
            $regData = $request->input('registration');
            if (isset($regData['additional_data'])) {
                $existing = $registration->additional_data ?? [];
                $regData['additional_data'] = array_merge($existing, $regData['additional_data']);
            }
            $registration->update($regData);
        }

        return back()->with('success', 'Data berhasil disimpan.');
    }

    public function saveGrades(Request $request)
    {
        $checkReg = Registration::findOrFail($request->registration_id);
        if (!in_array($checkReg->status, ['incomplete', 'revision'])) {
            return back()->withErrors(['error' => 'Data pendaftaran sudah dikunci dan tidak dapat diubah.']);
        }
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $defaultSubjects = json_encode(['mathematics', 'indonesian', 'english', 'religion']);
        $subjectsRequiredKeys = json_decode($settings['subjects_required'] ?? $defaultSubjects, true);

        $rules = [
            'registration_id' => 'required|exists:registrations,id',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        foreach ($subjectsRequiredKeys as $subj) {
            $rules[$subj] = 'nullable|numeric|between:0,100';
        }

        $request->validate($rules);

        // Core grade columns that exist in the database table
        $coreColumns = ['mathematics', 'indonesian', 'english', 'religion', 'ipa', 'ips', 'pkn'];
        
        $gradeData = ['registration_id' => $request->registration_id];
        $additionalGrades = [];

        foreach ($subjectsRequiredKeys as $subj) {
            if (in_array($subj, $coreColumns)) {
                $gradeData[$subj] = $request->input($subj);
            } else {
                $additionalGrades[$subj] = $request->input($subj);
            }
        }

        $grade = Grade::firstOrNew(['registration_id' => $request->registration_id]);
        foreach ($gradeData as $col => $val) {
            if ($col !== 'registration_id') {
                $grade->$col = $val;
            }
        }
        
        if (!empty($additionalGrades)) {
            $existingAdditional = $grade->additional_data ?? [];
            $grade->additional_data = array_merge($existingAdditional, $additionalGrades);
        }

        // Handle Prestasi List
        if ($request->has('prestasiList')) {
            $prestasiList = json_decode($request->input('prestasiList'), true) ?? [];
            $existingAdditional = $grade->additional_data ?? [];
            $existingAdditional['prestasiList'] = $prestasiList;
            $grade->additional_data = $existingAdditional;
        }
        
        $grade->save();

        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('grades_proof', 'public');
            $grade->update(['proof_file_path' => $path]);
        }

        // Calculate average dynamically
        $total = 0;
        $count = 0;
        foreach ($subjectsRequiredKeys as $subj) {
            $val = in_array($subj, $coreColumns) ? $grade->$subj : ($grade->additional_data[$subj] ?? null);
            if (!is_null($val)) {
                $total += $val;
                $count++;
            }
        }
        $avg = $count > 0 ? $total / $count : 0;

        Registration::find($request->registration_id)->update([
            'average_score' => $avg,
            'step' => max(4, Registration::find($request->registration_id)->step)
        ]);

        return back()->with('success', 'Nilai rapor berhasil disimpan.');
    }

    public function uploadDocument(Request $request)
    {
        $checkReg = Registration::findOrFail($request->registration_id);
        if (!in_array($checkReg->status, ['incomplete', 'revision'])) {
            return back()->withErrors(['error' => 'Data pendaftaran sudah dikunci dan tidak dapat diubah.']);
        }
        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'type' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        Document::updateOrCreate(
            ['registration_id' => $request->registration_id, 'type' => $request->type],
            ['file_path' => $path]
        );

        Registration::find($request->registration_id)->update(['step' => 5]);

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);
        $registration = Registration::findOrFail($document->registration_id);
        if (!in_array($registration->status, ['incomplete', 'revision'])) {
            return back()->withErrors(['error' => 'Data pendaftaran sudah dikunci dan tidak dapat diubah.']);
        }
        
        // Delete the physical file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function deleteGradeProof($id)
    {
        $grade = Grade::findOrFail($id);
        $registration = Registration::findOrFail($grade->registration_id);
        if (!in_array($registration->status, ['incomplete', 'revision'])) {
            return back()->withErrors(['error' => 'Data pendaftaran sudah dikunci dan tidak dapat diubah.']);
        }
        
        // Delete the physical file from storage
        if ($grade->proof_file_path && Storage::disk('public')->exists($grade->proof_file_path)) {
            Storage::disk('public')->delete($grade->proof_file_path);
        }
        
        $grade->update(['proof_file_path' => null]);

        return back()->with('success', 'Bukti rapor berhasil dihapus.');
    }

    public function finalize(Request $request)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail', 'grade'])->findOrFail($request->registration_id);
        if (!in_array($registration->status, ['incomplete', 'revision'])) {
            return back()->withErrors(['error' => 'Pendaftaran sudah difinalisasi.']);
        }
        
        // Basic backend check before finalization (frontend has full 100% check)
        if (!$registration->studentDetail || !$registration->parentDetail || !$registration->studentDetail->nik || !$registration->studentDetail->full_name) {
            return back()->withErrors(['error' => 'Data pendaftaran belum lengkap, silakan cek kembali sebelum finalisasi.']);
        }

        $registration->update([
            'status' => 'pending',
            'finalized_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran Anda telah difinalisasi dan sedang menunggu verifikasi.');
    }

    public function checkStatus()
    {
        return Inertia::render('CheckStatus');
    }

    public function processCheckStatus(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string',
            'access_code' => 'required|string',
        ]);

        $registration = Registration::where('registration_number', $request->registration_number)
            ->where('access_code', $request->access_code)
            ->first();

        if (!$registration) {
            return back()->withErrors(['message' => 'Nomor Pendaftaran atau Kode Akses salah.']);
        }

        return redirect()->route('student.dashboard', [$registration->registration_number, $registration->access_code]);
    }

    public function recoverAccess(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|size:10',
            'phone' => 'required|string',
        ]);

        $student = StudentDetail::where('nisn', $request->nisn)
            ->where('phone', $request->phone)
            ->with('registration')
            ->first();

        if (!$student || !$student->registration) {
            return back()->withErrors(['recover_message' => 'Data tidak ditemukan. Silakan periksa kembali NISN dan Nomor HP yang Anda masukkan.']);
        }

        try {
            $waMessage = "Halo {$student->full_name},\n"
                       . "Berikut adalah data akses pendaftaran Anda yang terlupa:\n\n"
                       . "Nomor Pendaftaran: *{$student->registration->registration_number}*\n"
                       . "Kode Akses: *{$student->registration->access_code}*\n\n"
                       . "Gunakan data ini untuk masuk ke Portal Siswa. Jangan berikan kode ini kepada siapa pun.";
            
            Http::timeout(5)->post('http://localhost:3000/send-message', [
                'number' => $student->phone,
                'message' => $waMessage
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['recover_message' => 'Gagal mengirim pesan WhatsApp. Pastikan layanan pesan aktif.']);
        }

        return back()->with('recover_success', [
            'registration_number' => $student->registration->registration_number,
            'full_name' => $student->full_name,
        ]);
    }

    public function dashboard($number, $code)
    {
        $registration = Registration::where('registration_number', $number)
            ->where('access_code', $code)
            ->with(['studentDetail', 'parentDetail', 'grade', 'documents'])
            ->firstOrFail();

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
        $subjectsRequiredKeys = json_decode($settings['subjects_required'] ?? $defaultSubjects, true);
        
        $subjectsRequiredDetails = array_filter($availableSubjects, function($subj) use ($subjectsRequiredKeys) {
            return in_array($subj['key'], $subjectsRequiredKeys);
        });

        $defaultPendidikan = json_encode(['Tidak Sekolah', 'SD/MI', 'SMP/MTs', 'SMA/SMK/MA', 'Diploma', 'S1', 'S2', 'S3']);
        $defaultPekerjaan = json_encode(['Tidak Bekerja', 'Nelayan', 'Petani', 'Peternak', 'PNS/TNI/POLRI', 'Karyawan Swasta', 'Pedagang Kecil', 'Pedagang Besar', 'Wiraswasta', 'Wirausaha', 'Buruh', 'Pensiunan', 'Tenaga Kerja Indonesia', 'Karyawan BUMN', 'Lainnya']);
        $defaultPenghasilan = json_encode(['Kurang dari 500.000', '500.000 - 999.999', '1.000.000 - 1.999.999', '2.000.000 - 4.999.999', '5.000.000 - 20.000.000', 'Lebih dari 20.000.000', 'Tidak Berpenghasilan']);
        $defaultAlasanKip = json_encode(['Pemegang KPS/PKH/KIP', 'Keluarga Miskin/Rentan Miskin', 'Yatim Piatu/Panti Asuhan/Panti Sosial', 'Dampak Bencana Alam', 'Pernah Drop Out', 'Siswa Miskin']);
        $defaultKebutuhanKhusus = json_encode(['Tidak', 'Netra', 'Rungu', 'Grahita Ringan', 'Grahita Sedang', 'Daksa Ringan', 'Daksa Sedang', 'Laras', 'Wicara', 'Tuna Ganda', 'Hiper Aktif', 'Cerdas Istimewa', 'Bakat Istimewa', 'Kesulitan Belajar', 'Narkoba', 'Indigo', 'Down Syndrome', 'Autis']);
        $defaultTempatTinggal = json_encode(['Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan']);
        $defaultEkstra = json_encode(['Tidak Ada', 'Pramuka', 'PMR', 'Paskibra', 'Olah Raga', 'Seni']);
        $defaultTransportasi = json_encode(['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Mobil Pribadi', 'Angkutan Umum', 'Antar Jemput']);
        $defaultPeminatan = json_encode(['IPA', 'IPS', 'Bahasa', 'Agama', 'Umum']);

        return Inertia::render('Student/Dashboard', [
            'registration' => $registration,
            'reportSemester' => $settings['report_semester'] ?? 'Kelas 6 Semester 2',
            'subjectsRequired' => array_values($subjectsRequiredDetails),
            'availableSubjects' => $availableSubjects,
            'options' => [
                'pendidikan' => json_decode($settings['opt_pendidikan'] ?? $defaultPendidikan, true),
                'pekerjaan' => json_decode($settings['opt_pekerjaan'] ?? $defaultPekerjaan, true),
                'penghasilan' => json_decode($settings['opt_penghasilan'] ?? $defaultPenghasilan, true),
                'tempat_tinggal' => json_decode($settings['opt_tempat_tinggal'] ?? $defaultTempatTinggal, true),
                'ekstrakurikuler' => json_decode($settings['opt_ekstrakurikuler'] ?? $defaultEkstra, true),
                'moda_transportasi' => json_decode($settings['opt_moda_transportasi'] ?? $defaultTransportasi, true),
                'peminatan' => json_decode($settings['opt_peminatan'] ?? $defaultPeminatan, true),
                'alasan_kip' => json_decode($settings['opt_alasan_kip'] ?? $defaultAlasanKip, true),
                'achievement_scores' => json_decode($settings['achievement_scores'] ?? json_encode([
                    'Tingkat Kecamatan' => ['Juara 1' => 10, 'Juara 2' => 7, 'Juara 3' => 5],
                    'Tingkat Kabupaten / Kota' => ['Juara 1' => 25, 'Juara 2' => 20, 'Juara 3' => 15],
                    'Tingkat Provinsi' => ['Juara 1' => 50, 'Juara 2' => 40, 'Juara 3' => 30],
                    'Tingkat Nasional' => ['Juara 1' => 75, 'Juara 2' => 60, 'Juara 3' => 45],
                    'Tingkat Internasional' => ['Juara 1' => 100, 'Juara 2' => 80, 'Juara 3' => 60],
                ]), true),
            ]
        ]);
    }

    public function announcement($number, $code)
    {
        $registration = Registration::where('registration_number', $number)
            ->where('access_code', $code)
            ->with(['studentDetail', 'grade'])
            ->firstOrFail();

        return Inertia::render('Announcement', [
            'registration' => $registration,
        ]);
    }
}
