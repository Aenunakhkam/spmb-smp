<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran - {{ $registration->registration_number }}</title>
    <style>
        @page { 
            size: 215mm 330mm portrait; /* Ukuran F4 / Folio */
            margin: 15mm; 
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: transparent !important; margin: 0 !important; padding: 0 !important;}
            .print-container { 
                border: none !important; 
                padding: 0 !important; 
                box-shadow: none !important; 
                margin: 0 !important; 
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
            }
            .btn-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        
        body { 
            font-family: 'Arial', Helvetica, sans-serif; 
            font-size: 10pt; 
            line-height: 1.4; 
            color: #000; 
            background: #e9ecef; 
            margin: 0; 
            padding: 20px 0; 
        }
        
        .print-container { 
            max-width: 215mm; 
            min-height: 330mm;
            margin: 0 auto; 
            padding: 15mm; 
            background: #fff;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            box-sizing: border-box; 
            position: relative;
        }

        /* WATERMARK */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            z-index: 0;
            width: 450px;
            pointer-events: none;
        }

        /* KOP SURAT */
        .kop-surat { width: 100%; margin-bottom: 15px; position: relative; z-index: 1; }
        .kop-table { width: 100%; border-collapse: collapse; border: none; }
        .kop-table td { border: none; padding: 0; }
        .kop-logo { width: 15%; text-align: left; vertical-align: middle; }
        .kop-logo img { max-width: 100px; max-height: 100px; width: auto; height: auto; object-fit: contain; }
        .kop-text { width: 85%; text-align: center; vertical-align: middle; }
        .kop-yayasan { font-size: 13pt; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; }
        .kop-sekolah { font-size: 18pt; font-weight: bold; letter-spacing: 1px; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; color: #1B5E20; }
        .kop-alamat { font-size: 9.5pt; margin-bottom: 2px; }
        .kop-kontak { font-size: 9pt; }
        .garis-kop-1 { border-top: 4px solid #000; margin-top: 10px; margin-bottom: 2px; }
        .garis-kop-2 { border-top: 1px solid #000; margin-top: 0; margin-bottom: 20px; }

        /* JUDUL */
        .judul-dokumen { text-align: center; margin-bottom: 20px; position: relative; z-index: 1; }
        .judul-teks { font-size: 14pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin-bottom: 5px; letter-spacing: 1px; }
        .sub-judul { font-size: 11pt; font-weight: bold; }

        /* KONTEN BENTUK FORM (TABEL RAPI) */
        .content-wrapper { position: relative; z-index: 1; }
        
        .section-title { 
            font-weight: bold; 
            font-size: 10.5pt; 
            background-color: #1B5E20; 
            color: #fff;
            padding: 5px 10px; 
            margin: 15px 0 0 0; 
            text-transform: uppercase; 
            border: 1px solid #000; 
            border-bottom: none;
            page-break-after: avoid; /* Jangan memotong judul dengan tabel di bawahnya */
            page-break-inside: avoid;
        }
        
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
            page-break-inside: auto; /* Memungkinkan tabel berlanjut ke halaman berikutnya */
        }
        table.data-table tr {
            page-break-inside: avoid; /* Jangan potong baris (isinya) di tengah-tengah halaman */
            page-break-after: auto;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #000; 
            padding: 5px 8px; 
            vertical-align: middle; 
            font-size: 9.5pt;
        }
        .td-label { 
            width: 35%; 
            font-weight: bold; 
            background-color: #f8faf8; 
        }
        .td-value { 
            width: 65%; 
            text-transform: uppercase;
        }

        /* TANDA TANGAN */
        .ttd-container { width: 100%; margin-top: 30px; position: relative; z-index: 1; page-break-inside: avoid; }
        .ttd-table { width: 100%; border: none; }
        .ttd-table td { width: 50%; text-align: center; vertical-align: top; border: none; padding: 0; font-size: 10pt;}
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 70px; text-transform: uppercase; }

        /* TOMBOL CETAK */
        .btn-print { 
            display: block; width: 280px; margin: 0 auto 20px auto; padding: 12px; 
            background: #1B5E20; color: white; text-align: center; border-radius: 8px; 
            font-weight: bold; cursor: pointer; border: none; font-size: 12pt;
            box-shadow: 0 4px 10px rgba(27,94,32,0.3);
            transition: 0.3s;
        }
        .btn-print:hover { background: #144d18; transform: translateY(-2px);}
        
        /* FOOTER */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px 15mm 15mm 15mm; /* Sesuaikan dengan margin kertas F4 */
            border-top: 2px solid #1B5E20;
            font-size: 8pt;
            color: #555;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: #fff;
            box-sizing: border-box;
            z-index: 10;
        }
        .footer-left { text-align: left; }
        .footer-right { text-align: right; font-weight: bold; color: #1B5E20; }
        
        .content-wrapper { 
            position: relative; 
            z-index: 1; 
            padding-bottom: 25mm; /* Memberi ruang agar teks tidak tertutup footer di halaman terakhir */
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Formulir (Ukuran F4)</button>
    
    <div class="print-container">
        
        @if(isset($settings['school_logo_path']) && $settings['school_logo_path'])
            <img src="{{ asset('storage/' . $settings['school_logo_path']) }}" class="watermark" alt="Watermark">
        @endif

        <!-- KOP SURAT -->
        <div class="kop-surat">
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if(isset($settings['school_logo_path']) && $settings['school_logo_path'])
                            <img src="{{ asset('storage/' . $settings['school_logo_path']) }}" alt="Logo">
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="Logo">
                        @endif
                    </td>
                    <td class="kop-text">
                        <div class="kop-yayasan">PANITIA PENERIMAAN PESERTA DIDIK BARU</div>
                        <div class="kop-sekolah">{{ $settings['app_name'] ?? 'SMP BUSTANUL ULUM NU JATIROKEH' }}</div>
                        <div class="kop-alamat">{{ $settings['address'] ?? 'Jln. Pramuka No. 01 Jatirokeh Kec. Songgom Kab. Brebes 52266' }}</div>
                        <div class="kop-kontak">
                            Telepon/WA: {{ $settings['phone'] ?? '-' }} | Email: {{ $settings['email'] ?? '-' }}
                        </div>
                    </td>
                </tr>
            </table>
            <div class="garis-kop-1"></div>
            <div class="garis-kop-2"></div>
        </div>

        <!-- JUDUL -->
        <div class="judul-dokumen">
            <div class="judul-teks">FORMULIR PENDAFTARAN PESERTA DIDIK BARU</div>
            <div class="sub-judul">TAHUN PELAJARAN {{ \App\Models\Setting::where('key', 'academic_year')->first()?->value ?? date('Y').'/'.(date('Y')+1) }}</div>
        </div>

        <div class="content-wrapper">
            <div class="section-title">A. KETERANGAN PENDAFTARAN</div>
            <table class="data-table">
                <tr><td class="td-label">Nomor Pendaftaran</td><td class="td-value">{{ $registration->registration_number }}</td></tr>
                <tr><td class="td-label">Pilihan Minat</td><td class="td-value">{{ $registration->additional_data['major'] ?? 'Belum Memilih' }}</td></tr>
                <tr><td class="td-label">Jalur Pendaftaran</td><td class="td-value">{{ $registration->additional_data['registration_type'] ?? 'UMUM' }}</td></tr>
                <tr><td class="td-label">Sumber Informasi</td><td class="td-value">{{ $registration->additional_data['information_source'] ?? '-' }}</td></tr>
            </table>

            <div class="section-title">B. IDENTITAS DIRI PESERTA DIDIK</div>
            <table class="data-table">
                <tr><td class="td-label">Nama Lengkap</td><td class="td-value">{{ $registration->studentDetail->full_name ?? '-' }}</td></tr>
                <tr><td class="td-label">Jenis Kelamin</td><td class="td-value">{{ ($registration->studentDetail->gender ?? '') == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</td></tr>
                <tr><td class="td-label">NISN / NIK (No. KTP)</td><td class="td-value">{{ $registration->studentDetail->nisn ?? '-' }} / {{ $registration->studentDetail->nik ?? '-' }}</td></tr>
                <tr><td class="td-label">Nomor Kartu Keluarga (KK)</td><td class="td-value">{{ $registration->studentDetail->additional_data['kk_number'] ?? '-' }}</td></tr>
                <tr><td class="td-label">No. Akta Kelahiran</td><td class="td-value">{{ $registration->studentDetail->additional_data['akta_number'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Tempat, Tanggal Lahir</td><td class="td-value">{{ $registration->studentDetail->place_of_birth ?? '-' }}, {{ $registration->studentDetail->date_of_birth ? \Carbon\Carbon::parse($registration->studentDetail->date_of_birth)->isoFormat('D MMMM YYYY') : '-' }}</td></tr>
                <tr><td class="td-label">Agama / Kepercayaan</td><td class="td-value">{{ $registration->studentDetail->religion ?? '-' }}</td></tr>
                <tr><td class="td-label">Kewarganegaraan / Negara</td><td class="td-value">{{ $registration->studentDetail->additional_data['citizenship'] ?? 'WNI' }} / {{ $registration->studentDetail->additional_data['country_name'] ?? 'INDONESIA' }}</td></tr>
                <tr><td class="td-label">Berkebutuhan Khusus</td><td class="td-value">{{ $registration->studentDetail->additional_data['special_needs'] ?? 'TIDAK ADA' }}</td></tr>
                <tr><td class="td-label">Anak Ke / Jml Saudara</td><td class="td-value">ANAK KE-{{ $registration->studentDetail->additional_data['child_order'] ?? '-' }} DARI {{ $registration->studentDetail->additional_data['siblings_count'] ?? '-' }} BERSAUDARA</td></tr>
                <tr><td class="td-label">Alamat Lengkap (Jalan)</td><td class="td-value">{{ $registration->studentDetail->address ?? '-' }}</td></tr>
                <tr><td class="td-label">RT / RW</td><td class="td-value">{{ $registration->studentDetail->additional_data['rt'] ?? '-' }} / {{ $registration->studentDetail->additional_data['rw'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Kelurahan / Desa</td><td class="td-value">{{ $registration->studentDetail->village ?? '-' }}</td></tr>
                <tr><td class="td-label">Kecamatan</td><td class="td-value">{{ $registration->studentDetail->district ?? '-' }}</td></tr>
                <tr><td class="td-label">Kabupaten/Kota - Provinsi</td><td class="td-value">{{ $registration->studentDetail->city ?? '-' }} - {{ $registration->studentDetail->province ?? '-' }}</td></tr>
                <tr><td class="td-label">Kode Pos</td><td class="td-value">{{ $registration->studentDetail->postal_code ?? '-' }}</td></tr>
                <tr><td class="td-label">Tempat Tinggal</td><td class="td-value">{{ $registration->studentDetail->additional_data['residence_type'] ?? '-' }}</td></tr>
                <tr><td class="td-label">No. Telepon / WhatsApp</td><td class="td-value">{{ $registration->studentDetail->phone ?? '-' }}</td></tr>
                <tr><td class="td-label">Email</td><td class="td-value">{{ $registration->studentDetail->email ?? '-' }}</td></tr>
            </table>
            
            <div class="section-title">C. FISIK & TAMBAHAN</div>
            <table class="data-table">
                <tr><td class="td-label">Tinggi Badan / Berat Badan</td><td class="td-value">{{ $registration->studentDetail->additional_data['height'] ?? '-' }} CM / {{ $registration->studentDetail->additional_data['weight'] ?? '-' }} KG</td></tr>
                <tr><td class="td-label">Lingkar Kepala</td><td class="td-value">{{ $registration->studentDetail->additional_data['head_circumference'] ?? '-' }} CM</td></tr>
                <tr><td class="td-label">Jarak ke Sekolah</td><td class="td-value">{{ $registration->studentDetail->additional_data['distance_to_school'] ?? '-' }} ({{ $registration->studentDetail->additional_data['distance_km'] ?? '-' }} KM)</td></tr>
                <tr><td class="td-label">Moda Transportasi</td><td class="td-value">{{ $registration->studentDetail->additional_data['transportation'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Minat Ekstrakurikuler</td><td class="td-value">{{ $registration->studentDetail->additional_data['extracurricular_interest'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Hobi</td><td class="td-value">{{ $registration->studentDetail->additional_data['hobby'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Cita - Cita</td><td class="td-value">{{ $registration->studentDetail->additional_data['ambition'] ?? '-' }}</td></tr>
            </table>



            <div class="section-title">D. ASAL SEKOLAH, PRESTASI & NILAI</div>
            <table class="data-table">
                <tr><td class="td-label" colspan="2" style="background-color: #e8eee8; text-align: center;"><b>DATA ASAL SEKOLAH</b></td></tr>
                <tr><td class="td-label">Nama Sekolah Asal</td><td class="td-value">{{ $registration->studentDetail->origin_school_name ?? '-' }}</td></tr>
                <tr><td class="td-label">Jenis / Status Sekolah</td><td class="td-value">{{ $registration->additional_data['school_type'] ?? '-' }} / {{ $registration->additional_data['school_status'] ?? '-' }}</td></tr>
                <tr><td class="td-label">NPSN Asal Sekolah</td><td class="td-value">{{ $registration->studentDetail->additional_data['origin_school_npsn'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Kabupaten/Kota Sekolah</td><td class="td-value">{{ $registration->additional_data['school_city'] ?? '-' }}</td></tr>
                
                <tr><td class="td-label" colspan="2" style="background-color: #e8eee8; text-align: center;"><b>DATA PRESTASI</b></td></tr>
                <tr><td class="td-label">Jenis Prestasi</td><td class="td-value">{{ $registration->studentDetail->additional_data['achievement_type'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Nama Prestasi</td><td class="td-value">{{ $registration->studentDetail->additional_data['achievement_name'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Tahun / Penyelenggara</td><td class="td-value">{{ $registration->studentDetail->additional_data['achievement_year'] ?? '-' }} / {{ $registration->studentDetail->additional_data['achievement_organizer'] ?? '-' }}</td></tr>
            </table>

            <div class="section-title">E. NILAI RAPOR TERAKHIR</div>
            <table class="data-table" style="text-align: center;">
                <tr style="background-color: #f8faf8;">
                    <th>Agama</th>
                    <th>PKn</th>
                    <th>B. Indonesia</th>
                    <th>Matematika</th>
                    <th>IPA</th>
                    <th>IPS</th>
                </tr>
                <tr>
                    <td><b>{{ $registration->grade->religion ?? '-' }}</b></td>
                    <td><b>{{ $registration->grade->pkn ?? '-' }}</b></td>
                    <td><b>{{ $registration->grade->indonesian ?? '-' }}</b></td>
                    <td><b>{{ $registration->grade->mathematics ?? '-' }}</b></td>
                    <td><b>{{ $registration->grade->ipa ?? '-' }}</b></td>
                    <td><b>{{ $registration->grade->ips ?? '-' }}</b></td>
                </tr>
                <tr style="background-color: #f8faf8;">
                    <td colspan="6" style="text-align: right; padding-right: 15px;">Rata-rata Keseluruhan: <b style="font-size: 11pt;">{{ $registration->average_score ?? '-' }}</b></td>
                </tr>
            </table>

            <div class="section-title">F. KESEJAHTERAAN & BANTUAN SOSIAL</div>
            <table class="data-table">
                <tr><td class="td-label">Nomor KKS</td><td class="td-value">{{ $registration->studentDetail->additional_data['kks_number'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Penerima KKS / PKH</td><td class="td-value">{{ $registration->studentDetail->additional_data['pkh_receiver'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Nomor KSS / Nomor PKH</td><td class="td-value">{{ $registration->studentDetail->additional_data['kps_number'] ?? '-' }} / {{ $registration->studentDetail->additional_data['pkh_number'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Penerima KIP / Fisik KIP</td><td class="td-value">{{ $registration->studentDetail->additional_data['kip_receiver'] ?? '-' }} / {{ $registration->studentDetail->additional_data['kip_physical'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Nomor KIP</td><td class="td-value">{{ $registration->studentDetail->additional_data['kip_number'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Nama di KIP</td><td class="td-value">{{ $registration->studentDetail->additional_data['kip_name'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Usulan / Alasan Layak PIP</td><td class="td-value">{{ $registration->studentDetail->additional_data['pip_eligible'] ?? '-' }} / {{ $registration->studentDetail->additional_data['pip_reason'] ?? '-' }}</td></tr>
            </table>

            <div class="section-title">G. IDENTITAS ORANG TUA / WALI</div>
            <table class="data-table">
                <tr><td class="td-label" colspan="2" style="background-color: #e8eee8; text-align: center;"><b>DATA AYAH KANDUNG</b></td></tr>
                <tr><td class="td-label">Nama Ayah / NIK Ayah</td><td class="td-value">{{ $registration->parentDetail->father_name ?? '-' }} / {{ $registration->parentDetail->additional_data['father_nik'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Tahun Lahir / Pendidikan Ayah</td><td class="td-value">{{ $registration->parentDetail->additional_data['father_birth_year'] ?? '-' }} / {{ $registration->parentDetail->additional_data['father_education'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Pekerjaan / Penghasilan Ayah</td><td class="td-value">{{ $registration->parentDetail->father_occupation ?? '-' }} / {{ $registration->parentDetail->additional_data['father_income'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Berkebutuhan Khusus Ayah</td><td class="td-value">{{ $registration->parentDetail->additional_data['father_special_needs'] ?? '-' }}</td></tr>
                
                <tr><td class="td-label" colspan="2" style="background-color: #e8eee8; text-align: center;"><b>DATA IBU KANDUNG</b></td></tr>
                <tr><td class="td-label">Nama Ibu / NIK Ibu</td><td class="td-value">{{ $registration->parentDetail->mother_name ?? '-' }} / {{ $registration->parentDetail->additional_data['mother_nik'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Tahun Lahir / Pendidikan Ibu</td><td class="td-value">{{ $registration->parentDetail->additional_data['mother_birth_year'] ?? '-' }} / {{ $registration->parentDetail->additional_data['mother_education'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Pekerjaan / Penghasilan Ibu</td><td class="td-value">{{ $registration->parentDetail->mother_occupation ?? '-' }} / {{ $registration->parentDetail->additional_data['mother_income'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Berkebutuhan Khusus Ibu</td><td class="td-value">{{ $registration->parentDetail->additional_data['mother_special_needs'] ?? '-' }}</td></tr>
                
                <tr><td class="td-label" colspan="2" style="background-color: #e8eee8; text-align: center;"><b>DATA WALI & KONTAK PENTING</b></td></tr>
                <tr><td class="td-label">Nama Wali</td><td class="td-value">{{ $registration->parentDetail->additional_data['guardian_name'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Tahun Lahir / Pendidikan Wali</td><td class="td-value">{{ $registration->parentDetail->additional_data['guardian_birth_year'] ?? '-' }} / {{ $registration->parentDetail->additional_data['guardian_education'] ?? '-' }}</td></tr>
                <tr><td class="td-label">Pekerjaan / Penghasilan Wali</td><td class="td-value">{{ $registration->parentDetail->additional_data['guardian_occupation'] ?? '-' }} / {{ $registration->parentDetail->additional_data['guardian_income'] ?? '-' }}</td></tr>
                <tr><td class="td-label">No. Telepon Rumah / HP (Ortu)</td><td class="td-value">{{ $registration->parentDetail->parent_phone ?? '-' }}</td></tr>
            </table>

            <!-- Tanda Tangan Section -->
            <div class="ttd-container">
                <table class="ttd-table">
                    <tr>
                        <td>
                            Mengetahui,<br>Orang Tua/Wali Calon Peserta Didik,
                            <div class="ttd-nama">_______________________</div>
                        </td>
                        <td>
                            Brebes, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>Calon Peserta Didik Baru,
                            <div class="ttd-nama">{{ $registration->studentDetail->full_name ?? '_______________________' }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            
        </div> <!-- End of content-wrapper -->

        <!-- FOOTER (Sekarang posisinya FIXED di ujung bawah kertas) -->
        <div class="page-footer">
            <div class="footer-left">
                <b>Dokumen Resmi PPDB</b> - {{ $settings['app_name'] ?? 'SMP BUSTANUL ULUM NU JATIROKEH' }}<br>
                Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY HH:mm:ss') }} WIB
            </div>
            <div class="footer-right">
                Lembar Pendaftaran (F4)
            </div>
        </div>
    </div>
</body>
</html>
