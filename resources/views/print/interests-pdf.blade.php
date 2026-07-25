<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Peminatan Siswa PPDB - {{ date('Y') }}</title>
    <style>
        body { font-family: 'Arial', Helvetica, sans-serif; font-size: 10pt; line-height: 1.3; color: #000; margin: 0; padding: 20px; }
        .kop-surat { width: 100%; margin-bottom: 15px; position: relative; }
        .kop-table { width: 100%; border-collapse: collapse; border: none;}
        .kop-table td { border: none; padding: 0; }
        .kop-logo { width: 12%; text-align: left; vertical-align: middle; }
        .kop-logo img { max-width: 80px; max-height: 80px; width: auto; height: auto; object-fit: contain; }
        .kop-text { width: 88%; text-align: center; vertical-align: middle; }
        .kop-yayasan { font-size: 11pt; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; }
        .kop-sekolah { font-size: 16pt; font-weight: bold; letter-spacing: 1px; margin-bottom: 2px; text-transform: uppercase; font-family: 'Times New Roman', Times, serif; }
        .kop-alamat { font-size: 9pt; margin-bottom: 2px; }
        .kop-kontak { font-size: 8pt; }
        .garis-kop-1 { border-top: 3px solid #000; margin-top: 10px; margin-bottom: 2px; }
        .garis-kop-2 { border-top: 1px solid #000; margin-top: 0; margin-bottom: 20px; }

        .judul-dokumen { text-align: center; margin-bottom: 20px; font-weight: bold; font-size: 12pt; text-transform: uppercase; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 9pt; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
        table.data-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        table.data-table td.text-center { text-align: center; }

        .ttd-container { width: 100%; margin-top: 30px; }
        .ttd-table { width: 100%; border: none; }
        .ttd-table td { width: 50%; border: none; padding: 0; text-align: center; vertical-align: top; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 60px; font-size: 10pt; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @php
                        $logoPath = null;
                        if(isset($settings['school_logo_path']) && $settings['school_logo_path']) {
                            $logoPath = public_path('storage/' . $settings['school_logo_path']);
                        }
                        if(!$logoPath || !file_exists($logoPath)) {
                            $logoPath = public_path('images/logo.png');
                        }
                    @endphp
                    <img src="{{ $logoPath }}" alt="Logo">
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
        DATA PEMINATAN CALON PESERTA DIDIK BARU<br>
        TAHUN PELAJARAN {{ $settings['academic_year'] ?? date('Y').'/'.(date('Y')+1) }}<br>
        <span style="font-size: 10pt; font-weight: normal; text-transform: none;">Filter Peminatan: <strong>{{ $minatFilter }}</strong></span>
    </div>

    <!-- DATA TABEL -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. Pendaftaran</th>
                <th width="25%">Nama Lengkap</th>
                <th width="10%">L/P</th>
                <th width="15%">No. HP/WA</th>
                <th width="15%">Peminatan</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $statusLabels = [
                    'incomplete' => 'Belum Lengkap',
                    'pending' => 'Menunggu Verifikasi',
                    'verified' => 'Terverifikasi',
                    'passed' => 'Diterima',
                    'failed' => 'Tidak Diterima',
                ];
            @endphp
            @forelse($registrations as $index => $reg)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $reg->registration_number }}</td>
                <td>{{ $reg->studentDetail->full_name ?? '-' }}</td>
                <td class="text-center">{{ $reg->studentDetail->gender ?? '-' }}</td>
                <td class="text-center">{{ $reg->studentDetail->phone ?? '-' }}</td>
                <td class="text-center">{{ $reg->additional_data['major'] ?? 'Belum Memilih' }}</td>
                <td class="text-center">{{ $statusLabels[$reg->status] ?? strtoupper($reg->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="ttd-container">
        <table class="ttd-table">
            <tr>
                <td></td>
                <td>
                    Brebes, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>Ketua Panitia PPDB,<br>
                    <div class="ttd-nama">_______________________</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
