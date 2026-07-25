<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Tanda Peserta - {{ $registration->registration_number }}</title>
    <style>
        @page { 
            size: 215mm 330mm portrait; 
            margin: 15mm; 
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; background: transparent !important; }
            .print-container { border: none !important; padding: 0 !important; box-shadow: none !important; margin: 0 !important; width: 100% !important;}
            .btn-print { display: none !important; }
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

        /* KARTU WRAPPER */
        .kartu-wrapper {
            width: 175mm;
            margin: 0 auto 25mm auto;
            border: 2px dashed #000;
            padding: 25px;
            background: #fff;
            position: relative;
            z-index: 1;
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
            pointer-events: none;
        }
        
        /* KOP SURAT */
        .kop-surat { width: 100%; margin-bottom: 15px; position: relative; z-index: 1; }
        .kop-table { width: 100%; border-collapse: collapse; border: none;}
        .kop-table td { border: none; padding: 0; }
        .kop-logo { width: 16%; text-align: left; vertical-align: middle; }
        .kop-logo img { max-width: 80px; max-height: 80px; width: auto; height: auto; object-fit: contain; }
        .kop-text { width: 84%; text-align: center; vertical-align: middle; }
        .kop-yayasan { font-size: 11pt; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; }
        .kop-sekolah { font-size: 16pt; font-weight: bold; letter-spacing: 1px; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; }
        .kop-alamat { font-size: 9pt; margin-bottom: 2px; }
        .kop-kontak { font-size: 8pt; }
        .garis-kop-1 { border-top: 3px solid #000; margin-top: 10px; margin-bottom: 2px; }
        .garis-kop-2 { border-top: 1px solid #000; margin-top: 0; margin-bottom: 20px; }

        /* JUDUL */
        .judul-dokumen { text-align: center; margin-bottom: 20px; position: relative; z-index: 1; }
        .judul-teks { font-size: 13pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin-bottom: 5px; }
        .sub-judul { font-size: 10pt; font-weight: bold; }

        /* KONTEN KARTU */
        .kartu-content {
            display: flex;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .biodata-section {
            flex: 1;
        }

        .biodata-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px;
        }
        .biodata-table th, .biodata-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: middle;
            font-size: 9.5pt;
        }
        .td-label { width: 35%; font-weight: bold; text-transform: uppercase; }
        .td-value { width: 65%; font-weight: bold; text-transform: uppercase; }
        
        .no-pendaftaran {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            letter-spacing: 1px;
            background: #fff;
        }

        /* INFORMASI PENTING */
        .info-box {
            border: 1px solid #000;
            padding: 12px 15px;
            background-color: #fff;
        }
        .info-title { font-weight: bold; margin-bottom: 5px; font-size: 9pt; text-transform: uppercase; text-decoration: underline; }
        .info-list { margin: 0; padding-left: 15px; text-align: justify; font-size: 8.5pt;}
        .info-list li { margin-bottom: 4px; }

        /* KOTAK FOTO & CAP (SEBELAH KANAN) */
        .right-section {
            width: 4.5cm;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .foto-box {
            width: 3cm;
            height: 4cm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 8pt;
            background: #fff;
            margin-bottom: 15px;
        }
        .cap-box {
            width: 2.5cm;
            height: 2.5cm;
            border: 1px dashed #000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 8pt;
            color: #333;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        /* TANDA TANGAN */
        .ttd-box {
            width: 100%;
            text-align: center;
            font-size: 9pt;
        }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 50px; text-transform: uppercase; font-size: 9pt; }

        /* TOMBOL CETAK */
        .btn-print { 
            display: block; width: 280px; margin: 0 auto 20px auto; padding: 12px; 
            background: #1B5E20; color: white; text-align: center; border-radius: 8px; 
            font-weight: bold; cursor: pointer; border: none; font-size: 12pt;
            box-shadow: 0 4px 10px rgba(27,94,32,0.3);
            transition: 0.3s;
        }
        .btn-print:hover { background: #144d18; transform: translateY(-2px);}
        
        /* FOOTER KERTAS */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px 15mm 15mm 15mm;
            border-top: 1px solid #000;
            font-size: 8pt;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: #fff;
            box-sizing: border-box;
            z-index: 10;
        }
        .footer-left { text-align: left; }
        .footer-right { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Kartu Peserta</button>
    
    <div class="print-container">
        
        <!-- BINGKAI KARTU PUTUS-PUTUS (UNTUK DIGUNTING NANTI) -->
        <div class="kartu-wrapper">
            @if(isset($settings['school_logo_path']) && $settings['school_logo_path'])
                <img src="{{ asset('storage/' . $settings['school_logo_path']) }}" class="watermark" alt="Watermark">
            @endif

            <!-- KOP SURAT KARTU -->
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

            <!-- ISI KARTU (2 KOLOM: KIRI BIODATA, KANAN FOTO+TTD) -->
            <div class="kartu-content">
                
                <div class="biodata-section">
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
                            <td class="td-value">{{ $registration->studentDetail->place_of_birth ?? '-' }}, {{ \Carbon\Carbon::parse($registration->studentDetail->date_of_birth)->isoFormat('D MMMM YYYY') }}</td>
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
                            <li>Wajib dibawa saat mengikuti seleksi, tes tertulis, tes lisan atau pada saat mendaftar ulang.</li>
                            <li>Harap segera menempelkan pas foto ukuran 3x4 berwarna pada kolom yang disediakan.</li>
                        </ul>
                    </div>
                </div>

                <div class="right-section">
                    <div class="foto-box">Pas Foto<br>3 x 4</div>
                    <div class="cap-box">Cap<br>Panitia</div>
                    <div class="ttd-box">
                        Brebes, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>Panitia Pendaftaran,
                        <div class="ttd-nama">_______________________</div>
                    </div>
                </div>

            </div>

        </div> <!-- end kartu-wrapper -->

        <!-- FOOTER KERTAS -->
        <div class="page-footer">
            <div class="footer-left">
                <b>Kartu Peserta Resmi</b> - {{ $settings['app_name'] ?? 'SMP BUSTANUL ULUM NU JATIROKEH' }}<br>
                Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY HH:mm:ss') }} WIB
            </div>
            <div class="footer-right">
                Lembar Cetak (Gunting Sesuai Garis Putus-putus)
            </div>
        </div>

    </div>
</body>
</html>
