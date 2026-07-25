<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Tanda Peserta - {{ $registration->registration_number }}</title>
    <style>
        @page { 
            size: 215mm 330mm portrait; 
            margin: 15mm; 
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: transparent !important; }
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

        /* KARTU WRAPPER */
        .kartu-wrapper {
            width: 100%;
            margin: 0 auto 20px auto;
            border: 2px dashed #000;
            padding: 20px;
            background: #fff;
            position: relative;
            box-sizing: border-box;
        }

        /* WATERMARK */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            z-index: 0;
            width: 300px;
        }
        
        /* KOP SURAT */
        .kop-surat { width: 100%; margin-bottom: 12px; position: relative; z-index: 1; }
        .kop-table { width: 100%; border-collapse: collapse; border: none;}
        .kop-table td { border: none; padding: 0; }
        .kop-logo { width: 75px; text-align: left; vertical-align: middle; }
        .kop-logo img { width: 70px; height: 70px; object-fit: contain; }
        .kop-text { text-align: center; vertical-align: middle; }
        .kop-yayasan { font-size: 10.5pt; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; }
        .kop-sekolah { font-size: 15pt; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; color: #1B5E20; }
        .kop-alamat { font-size: 8.5pt; margin-bottom: 2px; }
        .kop-kontak { font-size: 8pt; }
        .garis-kop-1 { border-top: 3px solid #000; margin-top: 8px; margin-bottom: 2px; }
        .garis-kop-2 { border-top: 1px solid #000; margin-top: 0; margin-bottom: 15px; }

        /* JUDUL */
        .judul-dokumen { text-align: center; margin-bottom: 15px; position: relative; z-index: 1; }
        .judul-teks { font-size: 12pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin-bottom: 3px; }
        .sub-judul { font-size: 9.5pt; font-weight: bold; }

        /* KONTEN KARTU - TABLE LAYOUT UNTUK DOMPDF */
        .kartu-layout-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            position: relative;
            z-index: 1;
        }
        .kartu-layout-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .biodata-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px;
        }
        .biodata-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 9pt;
        }
        .td-label { width: 35%; font-weight: bold; text-transform: uppercase; background-color: #f6f9f6; }
        .td-value { width: 65%; font-weight: bold; text-transform: uppercase; }
        
        .no-pendaftaran {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 12px;
            letter-spacing: 1px;
            background: #fff;
        }

        /* INFORMASI PENTING */
        .info-box {
            border: 1px solid #000;
            padding: 10px 12px;
            background-color: #fff;
        }
        .info-title { font-weight: bold; margin-bottom: 4px; font-size: 8.5pt; text-transform: uppercase; text-decoration: underline; }
        .info-list { margin: 0; padding-left: 14px; font-size: 8pt;}
        .info-list li { margin-bottom: 3px; }

        /* KOTAK FOTO & CAP (SEBELAH KANAN) */
        .foto-box {
            width: 3cm;
            height: 4cm;
            border: 1px solid #000;
            text-align: center;
            font-size: 8pt;
            background: #fff;
            margin: 0 auto 12px auto;
            padding-top: 1.3cm;
            box-sizing: border-box;
        }
        .cap-box {
            width: 2.2cm;
            height: 2.2cm;
            border: 1px dashed #000;
            text-align: center;
            font-size: 8pt;
            color: #333;
            border-radius: 50%;
            margin: 0 auto 12px auto;
            padding-top: 0.6cm;
            box-sizing: border-box;
        }

        /* TANDA TANGAN */
        .ttd-box {
            width: 100%;
            text-align: center;
            font-size: 8.5pt;
        }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 45px; text-transform: uppercase; font-size: 8.5pt; }

        /* TOMBOL CETAK */
        .btn-print { 
            display: block; width: 280px; margin: 15px auto; padding: 10px; 
            background: #1B5E20; color: white; text-align: center; border-radius: 8px; 
            font-weight: bold; cursor: pointer; border: none; font-size: 11pt;
            box-shadow: 0 4px 10px rgba(27,94,32,0.3);
        }
        
        /* FOOTER KERTAS */
        .page-footer {
            width: 100%;
            margin-top: 15px;
            padding-top: 6px;
            border-top: 1px solid #000;
            font-size: 7.5pt;
            color: #333;
            display: table;
        }
        .footer-left { display: table-cell; text-align: left; }
        .footer-right { display: table-cell; text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    @if(!isset($isPdf) || !$isPdf)
        <button class="btn-print no-print" onclick="window.print()">Cetak / Print Kartu Peserta</button>
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
        
        <!-- BINGKAI KARTU PUTUS-PUTUS -->
        <div class="kartu-wrapper">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="watermark" alt="">
            @endif

            <!-- KOP SURAT KARTU -->
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

            <!-- JUDUL KARTU -->
            <div class="judul-dokumen">
                <div class="judul-teks">KARTU TANDA PESERTA SELEKSI</div>
                <div class="sub-judul">TAHUN PELAJARAN {{ \App\Models\Setting::where('key', 'academic_year')->first()?->value ?? date('Y').'/'.(date('Y')+1) }}</div>
            </div>

            <!-- ISI KARTU (2 KOLOM TABLE) -->
            <table class="kartu-layout-table">
                <tr>
                    <td style="width: 70%; padding-right: 15px;">
                        <div class="no-pendaftaran">NO: {{ $registration->registration_number }}</div>

                        <table class="biodata-table">
                            <tr>
                                <td class="td-label">NAMA LENGKAP</td>
                                <td class="td-value">{{ $registration->studentDetail->full_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="td-label">NISN</td>
                                <td class="td-value">{{ $registration->studentDetail->nisn ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="td-label">TEMPAT, TGL LAHIR</td>
                                <td class="td-value">{{ $registration->studentDetail->place_of_birth ?? '-' }}, {{ $registration->studentDetail->date_of_birth ? \Carbon\Carbon::parse($registration->studentDetail->date_of_birth)->isoFormat('D MMMM YYYY') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="td-label">ASAL SEKOLAH</td>
                                <td class="td-value">{{ $registration->studentDetail->origin_school_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="td-label">PILIHAN MINAT</td>
                                <td class="td-value">{{ $registration->additional_data['major'] ?? 'UMUM' }}</td>
                            </tr>
                        </table>

                        <div class="info-box">
                            <div class="info-title">PERHATIAN:</div>
                            <ul class="info-list">
                                <li>Kartu ini adalah bukti sah pendaftaran.</li>
                                <li>Wajib dibawa saat mengikuti seleksi, tes, atau daftar ulang.</li>
                                <li>Harap menempelkan pas foto ukuran 3x4 berwarna.</li>
                            </ul>
                        </div>
                    </td>
                    <td style="width: 30%; text-align: center;">
                        <div class="foto-box">Pas Foto<br>3 x 4</div>
                        <div class="cap-box">Cap<br>Panitia</div>
                        <div class="ttd-box">
                            {{ $settings['city'] ?? 'Brebes' }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>Panitia Pendaftaran,
                            <div class="ttd-nama">_______________________</div>
                        </div>
                    </td>
                </tr>
            </table>

        </div> <!-- end kartu-wrapper -->

        <!-- FOOTER KERTAS -->
        <div class="page-footer">
            <div class="footer-left">
                <b>Kartu Peserta Resmi</b> - {{ $settings['app_name'] ?? 'SMP BUSTANUL ULUM NU JATIROKEH' }}<br>
                Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY HH:mm') }} WIB
            </div>
            <div class="footer-right">
                Lembar Cetak (Gunting Sesuai Garis Putus-putus)
            </div>
        </div>

    </div>
</body>
</html>
