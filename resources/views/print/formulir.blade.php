<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Pendaftaran - {{ $registration->registration_number }}</title>
    <style>
        @page {
            size: 215mm 330mm portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: transparent !important; margin: 0 !important; padding: 0 !important; }
            .no-print { display: none !important; }
        }

        body {
            font-family: 'Arial', Helvetica, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .print-container {
            width: 100%;
            margin: 0 auto;
            background: #fff;
            box-sizing: border-box;
            position: relative;
        }

        /* WATERMARK */
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            z-index: 0;
            width: 400px;
        }

        /* KOP SURAT */
        .kop-surat { width: 100%; margin-bottom: 10px; position: relative; z-index: 1; }
        .kop-table { width: 100%; border-collapse: collapse; border: none; }
        .kop-table td { border: none; padding: 0; }
        .kop-logo { width: 85px; text-align: left; vertical-align: middle; }
        .kop-logo img { width: 80px; height: 80px; object-fit: contain; }
        .kop-text { text-align: center; vertical-align: middle; }
        .kop-yayasan { font-size: 11pt; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; }
        .kop-sekolah { font-size: 16pt; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; color: #1B5E20; }
        .kop-alamat { font-size: 9pt; margin-bottom: 2px; }
        .kop-kontak { font-size: 8.5pt; }
        .garis-kop-1 { border-top: 3px solid #000; margin-top: 6px; margin-bottom: 2px; }
        .garis-kop-2 { border-top: 1px solid #000; margin-top: 0; margin-bottom: 12px; }

        /* JUDUL */
        .judul-dokumen { text-align: center; margin-bottom: 12px; position: relative; z-index: 1; }
        .judul-teks { font-size: 12pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; letter-spacing: 0.5px; }
        .sub-judul { font-size: 9.5pt; font-weight: bold; margin-top: 2px; }

        /* KONTEN */
        .content-wrapper { position: relative; z-index: 1; }

        .section-title {
            font-weight: bold;
            font-size: 9pt;
            background-color: #1B5E20;
            color: #fff;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 0;
            text-transform: uppercase;
            border: 1px solid #000;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 7px;
            vertical-align: middle;
            font-size: 8.5pt;
        }
        .td-label {
            width: 36%;
            font-weight: bold;
            background-color: #f6f9f6;
        }
        .td-value {
            width: 64%;
            text-transform: uppercase;
        }
        .td-sub-header {
            background-color: #d4e8d4;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
            padding: 3px;
        }

        /* NILAI TABEL (Horizontal) */
        table.nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        table.nilai-table th, table.nilai-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 8.5pt;
        }
        table.nilai-table th { background-color: #f6f9f6; font-weight: bold; }

        /* PRESTASI */
        table.prestasi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 8.5pt;
        }
        table.prestasi-table th, table.prestasi-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        table.prestasi-table th { background-color: #f6f9f6; font-weight: bold; text-align: center; }

        /* TANDA TANGAN */
        .ttd-container { width: 100%; margin-top: 18px; position: relative; z-index: 1; page-break-inside: avoid; }
        .ttd-table { width: 100%; border: none; border-collapse: collapse; }
        .ttd-table td { width: 50%; text-align: center; vertical-align: top; border: none; padding: 0; font-size: 9pt; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 55px; text-transform: uppercase; }

        /* TOMBOL CETAK (Hanya di browser) */
        .btn-print {
            display: block; width: 280px; margin: 15px auto; padding: 10px;
            background: #1B5E20; color: white; text-align: center; border-radius: 8px;
            font-weight: bold; cursor: pointer; border: none; font-size: 11pt;
            box-shadow: 0 4px 10px rgba(27,94,32,0.3);
        }

        /* FOOTER */
        .page-footer {
            width: 100%;
            margin-top: 15px;
            padding-top: 6px;
            border-top: 2px solid #1B5E20;
            font-size: 7.5pt;
            color: #555;
            display: table;
        }
        .footer-left { display: table-cell; text-align: left; }
        .footer-right { display: table-cell; text-align: right; font-weight: bold; color: #1B5E20; }
    </style>
</head>
<body>
    @if(!isset($isPdf) || !$isPdf)
        <button class="btn-print no-print" onclick="window.print()">Cetak / Print Formulir (Ukuran F4)</button>
    @endif

    @php
        $logoSrc = null;
        $logoPath = null;
        if (!empty($settings['school_logo_path'])) {
            $storagePath = storage_path('app/public/' . $settings['school_logo_path']);
            if (file_exists($storagePath)) {
                $logoPath = $storagePath;
            }
        }
        if (!$logoPath && file_exists(public_path('images/logo.png'))) {
            $logoPath = public_path('images/logo.png');
        }
        if ($logoPath && file_exists($logoPath)) {
            $ext = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoSrc = 'data:image/' . ($ext === 'svg' ? 'svg+xml' : $ext) . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    @endphp

    <div class="print-container">

        @if($logoSrc)
            <img src="{{ $logoSrc }}" class="watermark" alt="">
        @endif

        {{-- KOP SURAT --}}
        <div class="kop-surat">
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if($logoSrc)
                            <img src="{{ $logoSrc }}" alt="Logo">
                        @endif
                    </td>
                    <td class="kop-text">
                        <div class="kop-yayasan">PANITIA SISTEM PENERIMAAN MURID BARU</div>
                        <div class="kop-sekolah">{{ $settings['app_name'] ?? 'SMP BUSTANUL ULUM NU JATIROKEH' }}</div>
                        <div class="kop-alamat">{{ $settings['address'] ?? 'Jln. Pramuka No. 01 Jatirokeh Kec. Songgom Kab. Brebes 52266' }}</div>
                        <div class="kop-kontak">Telp/WA: {{ $settings['phone'] ?? '-' }} | Email: {{ $settings['email'] ?? '-' }}</div>
                    </td>
                </tr>
            </table>
            <div class="garis-kop-1"></div>
            <div class="garis-kop-2"></div>
        </div>

        {{-- JUDUL --}}
        <div class="judul-dokumen">
            <div class="judul-teks">FORMULIR PENDAFTARAN PESERTA DIDIK BARU</div>
            <div class="sub-judul">TAHUN PELAJARAN {{ \App\Models\Setting::where('key', 'academic_year')->first()?->value ?? date('Y').'/'.(date('Y')+1) }}</div>
        </div>

        <div class="content-wrapper">

            {{-- A. DATA PENDAFTARAN --}}
            <div class="section-title">A. KETERANGAN PENDAFTARAN</div>
            <table class="data-table">
                <tr><td class="td-label">Nomor Pendaftaran</td><td class="td-value"><b>{{ $registration->registration_number }}</b></td></tr>
                @if(!empty($registration->additional_data['major']))
                <tr><td class="td-label">Pilihan Peminatan</td><td class="td-value">{{ $registration->additional_data['major'] }}</td></tr>
                @endif
                @if(!empty($registration->additional_data['registration_type']))
                <tr><td class="td-label">Jalur Pendaftaran</td><td class="td-value">{{ $registration->additional_data['registration_type'] }}</td></tr>
                @endif
                @if(!empty($registration->additional_data['school_type']))
                <tr><td class="td-label">Jenis Sekolah Asal</td><td class="td-value">{{ $registration->additional_data['school_type'] }} / {{ $registration->additional_data['school_status'] ?? '-' }}</td></tr>
                @endif
                @if(!empty($registration->additional_data['school_city']))
                <tr><td class="td-label">Kabupaten Sekolah Asal</td><td class="td-value">{{ $registration->additional_data['school_city'] }}</td></tr>
                @endif
                @if(!empty($registration->additional_data['information_source']))
                <tr><td class="td-label">Mengetahui Info Sekolah dari</td><td class="td-value">{{ $registration->additional_data['information_source'] }}</td></tr>
                @endif
            </table>

            {{-- B. IDENTITAS SISWA --}}
            @php $s = $registration->studentDetail; $s_add = $s?->additional_data ?? []; @endphp
            <div class="section-title">B. IDENTITAS DIRI PESERTA DIDIK</div>
            <table class="data-table">
                @if(!empty($s?->full_name))
                <tr><td class="td-label">Nama Lengkap</td><td class="td-value"><b>{{ $s->full_name }}</b></td></tr>
                @endif
                <tr><td class="td-label">Jenis Kelamin</td><td class="td-value">{{ ($s?->gender ?? '') == 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</td></tr>
                @if(!empty($s?->nisn))
                <tr><td class="td-label">NISN</td><td class="td-value">{{ $s->nisn }}</td></tr>
                @endif
                @if(!empty($s?->nik))
                <tr><td class="td-label">NIK</td><td class="td-value">{{ $s->nik }}</td></tr>
                @endif
                @if(!empty($s_add['kk_number']))
                <tr><td class="td-label">No. Kartu Keluarga (KK)</td><td class="td-value">{{ $s_add['kk_number'] }}</td></tr>
                @endif
                @if(!empty($s_add['akta_number']))
                <tr><td class="td-label">No. Akta Kelahiran</td><td class="td-value">{{ $s_add['akta_number'] }}</td></tr>
                @endif
                @if(!empty($s?->place_of_birth) || !empty($s?->date_of_birth))
                <tr><td class="td-label">Tempat, Tanggal Lahir</td><td class="td-value">{{ $s?->place_of_birth ?? '-' }}, {{ $s?->date_of_birth ? \Carbon\Carbon::parse($s->date_of_birth)->isoFormat('D MMMM YYYY') : '-' }}</td></tr>
                @endif
                @if(!empty($s?->religion))
                <tr><td class="td-label">Agama</td><td class="td-value">{{ $s->religion }}</td></tr>
                @endif
                @if(!empty($s_add['child_order']) || !empty($s_add['siblings_count']))
                <tr><td class="td-label">Anak ke- / Jumlah Saudara</td><td class="td-value">Anak ke-{{ $s_add['child_order'] ?? '-' }} dari {{ $s_add['siblings_count'] ?? '-' }} bersaudara</td></tr>
                @endif
                @if(!empty($s?->phone))
                <tr><td class="td-label">No. HP / WhatsApp</td><td class="td-value">{{ $s->phone }}</td></tr>
                @endif
            </table>

            {{-- C. ALAMAT --}}
            @php
                $hasAlamat = !empty($s?->address) || !empty($s?->village) || !empty($s?->district) || !empty($s?->city);
            @endphp
            @if($hasAlamat)
            <div class="section-title">C. ALAMAT TEMPAT TINGGAL</div>
            <table class="data-table">
                @if(!empty($s?->address))
                <tr><td class="td-label">Alamat Jalan</td><td class="td-value">{{ $s->address }}</td></tr>
                @endif
                @if(!empty($s_add['rt']) || !empty($s_add['rw']))
                <tr><td class="td-label">RT / RW</td><td class="td-value">{{ $s_add['rt'] ?? '-' }} / {{ $s_add['rw'] ?? '-' }}</td></tr>
                @endif
                @if(!empty($s?->village))
                <tr><td class="td-label">Desa / Kelurahan</td><td class="td-value">{{ $s->village }}</td></tr>
                @endif
                @if(!empty($s?->district))
                <tr><td class="td-label">Kecamatan</td><td class="td-value">{{ $s->district }}</td></tr>
                @endif
                @if(!empty($s?->city) || !empty($s?->province))
                <tr><td class="td-label">Kabupaten / Provinsi</td><td class="td-value">{{ $s?->city ?? '-' }} — {{ $s?->province ?? '-' }}</td></tr>
                @endif
                @if(!empty($s?->postal_code))
                <tr><td class="td-label">Kode Pos</td><td class="td-value">{{ $s->postal_code }}</td></tr>
                @endif
                @if(!empty($s_add['residence_type']))
                <tr><td class="td-label">Status Tempat Tinggal</td><td class="td-value">{{ $s_add['residence_type'] }}</td></tr>
                @endif
            </table>
            @endif

            {{-- D. SEKOLAH ASAL --}}
            @if(!empty($s?->origin_school_name))
            <div class="section-title">D. ASAL SEKOLAH</div>
            <table class="data-table">
                <tr><td class="td-label">Nama Sekolah Asal</td><td class="td-value">{{ $s->origin_school_name }}</td></tr>
                @if(!empty($registration->additional_data['school_type']))
                <tr><td class="td-label">Jenis / Status Sekolah</td><td class="td-value">{{ $registration->additional_data['school_type'] }} / {{ $registration->additional_data['school_status'] ?? '-' }}</td></tr>
                @endif
                @if(!empty($registration->additional_data['school_city']))
                <tr><td class="td-label">Kabupaten Sekolah</td><td class="td-value">{{ $registration->additional_data['school_city'] }}</td></tr>
                @endif
            </table>
            @endif

            {{-- E. DATA FISIK & TRANSPORTASI --}}
            @php
                $hasFisik = !empty($s_add['height']) || !empty($s_add['weight']) || !empty($s_add['distance_to_school']);
            @endphp
            @if($hasFisik)
            <div class="section-title">E. DATA FISIK &amp; TRANSPORTASI</div>
            <table class="data-table">
                @if(!empty($s_add['height']) || !empty($s_add['weight']))
                <tr><td class="td-label">Tinggi / Berat Badan</td><td class="td-value">{{ $s_add['height'] ?? '-' }} cm / {{ $s_add['weight'] ?? '-' }} kg</td></tr>
                @endif
                @if(!empty($s_add['distance_to_school']))
                @php
                    $jarakParts = [];
                    $jarakParts[] = $s_add['distance_to_school'];
                    if (!empty($s_add['distance_km'])) {
                        $jarakParts[] = '(' . $s_add['distance_km'] . ' km)';
                    }
                    if (!empty($s_add['travel_time'])) {
                        $jarakParts[] = 'Waktu Tempuh ~' . $s_add['travel_time'] . ' menit';
                    }
                    $jarakStr = implode(', ', $jarakParts);
                @endphp
                <tr><td class="td-label">Jarak &amp; Waktu Tempuh</td><td class="td-value">{{ $jarakStr }}</td></tr>
                @endif
                @if(!empty($s_add['transportation']))
                <tr><td class="td-label">Moda Transportasi</td><td class="td-value">{{ $s_add['transportation'] }}</td></tr>
                @endif
                @if(!empty($s_add['extracurricular_interest']))
                <tr><td class="td-label">Minat Ekstrakurikuler</td><td class="td-value">{{ $s_add['extracurricular_interest'] }}</td></tr>
                @endif
                @if(!empty($s_add['hobby']))
                <tr><td class="td-label">Hobi</td><td class="td-value">{{ $s_add['hobby'] }}</td></tr>
                @endif
                @if(!empty($s_add['ambition']))
                <tr><td class="td-label">Cita-cita</td><td class="td-value">{{ $s_add['ambition'] }}</td></tr>
                @endif
            </table>
            @endif

            {{-- F. NILAI RAPOR --}}
            @php $g = $registration->grade; @endphp
            @if($g && ($g->mathematics || $g->indonesian || $g->english || $g->religion || $g->ipa || $g->ips || $g->pkn))
            <div class="section-title">F. NILAI RAPOR TERAKHIR</div>
            <table class="nilai-table">
                <tr>
                    @if($g->religion)<th>Agama</th>@endif
                    @if($g->pkn)<th>PKn</th>@endif
                    @if($g->indonesian)<th>B. Indonesia</th>@endif
                    @if($g->mathematics)<th>Matematika</th>@endif
                    @if($g->ipa)<th>IPA</th>@endif
                    @if($g->ips)<th>IPS</th>@endif
                    @if($g->english)<th>B. Inggris</th>@endif
                    <th>Rata-rata</th>
                </tr>
                <tr>
                    @if($g->religion)<td><b>{{ $g->religion }}</b></td>@endif
                    @if($g->pkn)<td><b>{{ $g->pkn }}</b></td>@endif
                    @if($g->indonesian)<td><b>{{ $g->indonesian }}</b></td>@endif
                    @if($g->mathematics)<td><b>{{ $g->mathematics }}</b></td>@endif
                    @if($g->ipa)<td><b>{{ $g->ipa }}</b></td>@endif
                    @if($g->ips)<td><b>{{ $g->ips }}</b></td>@endif
                    @if($g->english)<td><b>{{ $g->english }}</b></td>@endif
                    <td><b>{{ $registration->average_score ?? '-' }}</b></td>
                </tr>
            </table>
            @endif

            {{-- G. PRESTASI --}}
            @php
                $prestasiList = $registration->grade?->additional_data['prestasiList'] ?? [];
            @endphp
            @if(!empty($prestasiList) && count($prestasiList) > 0)
            <div class="section-title">G. DATA PRESTASI</div>
            <table class="prestasi-table">
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Nama Prestasi</th>
                    <th width="15%">Kategori</th>
                    <th width="20%">Tingkat</th>
                    <th width="10%">Peringkat</th>
                    <th width="15%">Tahun</th>
                </tr>
                @foreach($prestasiList as $i => $prestasi)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td>{{ $prestasi['name'] ?? '-' }}<br><small style="color:#555">{{ $prestasi['organizer'] ?? '' }}</small></td>
                    <td>{{ $prestasi['category'] ?? '-' }}</td>
                    <td>{{ $prestasi['level'] ?? '-' }}</td>
                    <td style="text-align:center">{{ $prestasi['rank'] ?? '-' }}</td>
                    <td style="text-align:center">{{ $prestasi['year'] ?? '-' }}</td>
                </tr>
                @endforeach
            </table>
            @endif

            {{-- H. DATA ORANG TUA --}}
            @php $p = $registration->parentDetail; $p_add = $p?->additional_data ?? []; @endphp
            @if($p)
            <div class="section-title">H. IDENTITAS ORANG TUA / WALI</div>
            <table class="data-table">
                {{-- AYAH --}}
                @if(!empty($p->father_name))
                <tr><td colspan="2" class="td-sub-header">DATA AYAH KANDUNG</td></tr>
                <tr><td class="td-label">Nama Ayah</td><td class="td-value">{{ $p->father_name }}</td></tr>
                @if(!empty($p_add['father_nik']))<tr><td class="td-label">NIK Ayah</td><td class="td-value">{{ $p_add['father_nik'] }}</td></tr>@endif
                @if(!empty($p_add['father_education']))<tr><td class="td-label">Pendidikan Terakhir Ayah</td><td class="td-value">{{ $p_add['father_education'] }}</td></tr>@endif
                @if(!empty($p->father_occupation))<tr><td class="td-label">Pekerjaan Ayah</td><td class="td-value">{{ $p->father_occupation }}</td></tr>@endif
                @if(!empty($p_add['father_income']))<tr><td class="td-label">Penghasilan Ayah / Bulan</td><td class="td-value">{{ $p_add['father_income'] }}</td></tr>@endif
                @endif

                {{-- IBU --}}
                @if(!empty($p->mother_name))
                <tr><td colspan="2" class="td-sub-header">DATA IBU KANDUNG</td></tr>
                <tr><td class="td-label">Nama Ibu</td><td class="td-value">{{ $p->mother_name }}</td></tr>
                @if(!empty($p_add['mother_nik']))<tr><td class="td-label">NIK Ibu</td><td class="td-value">{{ $p_add['mother_nik'] }}</td></tr>@endif
                @if(!empty($p_add['mother_education']))<tr><td class="td-label">Pendidikan Terakhir Ibu</td><td class="td-value">{{ $p_add['mother_education'] }}</td></tr>@endif
                @if(!empty($p->mother_occupation))<tr><td class="td-label">Pekerjaan Ibu</td><td class="td-value">{{ $p->mother_occupation }}</td></tr>@endif
                @if(!empty($p_add['mother_income']))<tr><td class="td-label">Penghasilan Ibu / Bulan</td><td class="td-value">{{ $p_add['mother_income'] }}</td></tr>@endif
                @if(!empty($p_add['mother_phone']))<tr><td class="td-label">No. HP Ibu</td><td class="td-value">{{ $p_add['mother_phone'] }}</td></tr>@endif
                @endif

                {{-- KONTAK UTAMA --}}
                @if(!empty($p->parent_phone))
                <tr><td colspan="2" class="td-sub-header">KONTAK ORANG TUA</td></tr>
                <tr><td class="td-label">No. HP Orang Tua (Utama)</td><td class="td-value">{{ $p->parent_phone }}</td></tr>
                @if(!empty($p->parent_address))<tr><td class="td-label">Alamat Orang Tua</td><td class="td-value">{{ $p->parent_address }}</td></tr>@endif
                @endif

                {{-- WALI --}}
                @if(!empty($p_add['guardian_name']))
                <tr><td colspan="2" class="td-sub-header">DATA WALI</td></tr>
                <tr><td class="td-label">Nama Wali</td><td class="td-value">{{ $p_add['guardian_name'] }}</td></tr>
                @if(!empty($p_add['guardian_education']))<tr><td class="td-label">Pendidikan Wali</td><td class="td-value">{{ $p_add['guardian_education'] }}</td></tr>@endif
                @if(!empty($p_add['guardian_occupation']))<tr><td class="td-label">Pekerjaan Wali</td><td class="td-value">{{ $p_add['guardian_occupation'] }}</td></tr>@endif
                @if(!empty($p_add['guardian_income']))<tr><td class="td-label">Penghasilan Wali</td><td class="td-value">{{ $p_add['guardian_income'] }}</td></tr>@endif
                @endif
            </table>
            @endif

            {{-- I. DATA BANTUAN --}}
            @php
                $hasBantuan = !empty($s_add['kip_number']) || !empty($s_add['pkh_number']) || !empty($s_add['kks_number']);
            @endphp
            @if($hasBantuan)
            <div class="section-title">I. DATA BANTUAN PENDIDIKAN (KIP / PKH / KKS)</div>
            <table class="data-table">
                @if(!empty($s_add['kip_number']))<tr><td class="td-label">No. KIP</td><td class="td-value">{{ $s_add['kip_number'] }}</td></tr>@endif
                @if(!empty($s_add['pkh_number']))<tr><td class="td-label">No. PKH</td><td class="td-value">{{ $s_add['pkh_number'] }}</td></tr>@endif
                @if(!empty($s_add['kks_number']))<tr><td class="td-label">No. KKS</td><td class="td-value">{{ $s_add['kks_number'] }}</td></tr>@endif
            </table>
            @endif

            {{-- TANDA TANGAN --}}
            <div class="ttd-container">
                <table class="ttd-table">
                    <tr>
                        <td>
                            Mengetahui,<br>Orang Tua / Wali Calon Peserta Didik,
                            <div class="ttd-nama">_________________________</div>
                        </td>
                        <td>
                            {{ $settings['city'] ?? 'Brebes' }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>Calon Peserta Didik Baru,
                            <div class="ttd-nama">{{ $registration->studentDetail?->full_name ?? '_________________________' }}</div>
                        </td>
                    </tr>
                </table>
            </div>

        </div>{{-- end content-wrapper --}}

        {{-- FOOTER KERTAS --}}
        <div class="page-footer">
            <div class="footer-left">
                <b>Dokumen Resmi Panitia SPMB</b> — {{ $settings['app_name'] ?? 'SMP BUSTANUL ULUM NU JATIROKEH' }}<br>
                Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB &nbsp;|&nbsp; No. Pendaftaran: <b>{{ $registration->registration_number }}</b>
            </div>
            <div class="footer-right">
                Formulir Pendaftaran (F4)
            </div>
        </div>

    </div>
</body>
</html>
