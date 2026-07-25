<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pendaftar SPMB - {{ date('Y') }}</title>
    <style>
        @page {
            size: 330mm 215mm landscape;
            margin: 10mm 12mm 12mm 12mm;
        }
        body { font-family: 'Arial', Helvetica, sans-serif; font-size: 8.5pt; line-height: 1.3; color: #000; margin: 0; padding: 0; }
        .kop-surat { width: 100%; margin-bottom: 10px; position: relative; }
        .kop-table { width: 100%; border-collapse: collapse; border: none;}
        .kop-table td { border: none; padding: 0; }
        .kop-logo { width: 75px; text-align: left; vertical-align: middle; }
        .kop-logo img { width: 70px; height: 70px; object-fit: contain; }
        .kop-text { text-align: center; vertical-align: middle; }
        .kop-yayasan { font-size: 10.5pt; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; }
        .kop-sekolah { font-size: 15pt; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; color: #1B5E20; }
        .kop-alamat { font-size: 8.5pt; margin-bottom: 2px; }
        .kop-kontak { font-size: 8pt; }
        .garis-kop-1 { border-top: 3px solid #000; margin-top: 6px; margin-bottom: 2px; }
        .garis-kop-2 { border-top: 1px solid #000; margin-top: 0; margin-bottom: 10px; }

        .judul-dokumen { text-align: center; margin-bottom: 12px; font-weight: bold; font-size: 11pt; text-transform: uppercase; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 8pt; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 4px 6px; text-align: left; vertical-align: middle; }
        table.data-table th { background-color: #f0f4f0; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 7.5pt; }
        table.data-table td.text-center { text-align: center; }

        .ttd-container { width: 100%; margin-top: 20px; page-break-inside: avoid; }
        .ttd-table { width: 100%; border: none; border-collapse: collapse; }
        .ttd-table td { width: 50%; border: none; padding: 0; text-align: center; vertical-align: top; font-size: 8.5pt; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 45px; font-size: 8.5pt; text-transform: uppercase; }
    </style>
</head>
<body>

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

        $statusLabels = [
            'incomplete' => 'Belum Lengkap',
            'pending'    => 'Menunggu Verifikasi',
            'verified'   => 'Terverifikasi',
            'passed'     => 'Diterima',
            'failed'     => 'Tidak Diterima',
        ];
    @endphp

    <!-- KOP SURAT -->
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

    <!-- JUDUL -->
    <div class="judul-dokumen">
        LAPORAN DATA PENDAFTAR CALON PESERTA DIDIK BARU<br>
        TAHUN PELAJARAN {{ $settings['academic_year'] ?? date('Y').'/'.(date('Y')+1) }}
    </div>

    <!-- DATA TABEL LENGKAP -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="9%">No. Daftar</th>
                <th width="8%">NISN</th>
                <th width="10%">NIK</th>
                <th width="14%">Nama Lengkap</th>
                <th width="3%">L/P</th>
                <th width="13%">Tempat, Tgl Lahir</th>
                <th width="13%">Sekolah Asal</th>
                <th width="7%">Peminatan</th>
                <th width="5%">Jalur</th>
                <th width="8%">No. HP</th>
                <th width="4%">Skor</th>
                <th width="7%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registrations as $index => $reg)
            @php
                $s = $reg->studentDetail;
                $ttl = ($s?->place_of_birth ?? '-') . ', ' . ($s?->date_of_birth ? \Carbon\Carbon::parse($s->date_of_birth)->isoFormat('DD/MM/YYYY') : '-');
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center"><b>{{ $reg->registration_number }}</b></td>
                <td class="text-center">{{ $s?->nisn ?? '-' }}</td>
                <td class="text-center">{{ $s?->nik ?? '-' }}</td>
                <td><b>{{ $s?->full_name ?? '-' }}</b></td>
                <td class="text-center">{{ $s?->gender ?? '-' }}</td>
                <td>{{ $ttl }}</td>
                <td>{{ $s?->origin_school_name ?? '-' }}</td>
                <td class="text-center">{{ $reg->additional_data['major'] ?? 'UMUM' }}</td>
                <td class="text-center">{{ $reg->additional_data['registration_type'] ?? 'BARU' }}</td>
                <td class="text-center">{{ $s?->phone ?? '-' }}</td>
                <td class="text-center"><b>{{ $reg->final_score ?? '-' }}</b></td>
                <td class="text-center"><b>{{ $statusLabels[$reg->status] ?? strtoupper($reg->status) }}</b></td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="text-center">Belum ada data pendaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd-container">
        <table class="ttd-table">
            <tr>
                <td width="60%"></td>
                <td width="40%">
                    {{ $settings['city'] ?? 'Brebes' }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>
                    Ketua Panitia SPMB,<br>
                    <div class="ttd-nama">_______________________</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
