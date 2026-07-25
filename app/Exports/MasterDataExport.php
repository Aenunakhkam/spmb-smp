<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class MasterDataExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    private int $rowNumber = 0;

    public function title(): string
    {
        return 'Data Master Siswa';
    }

    public function collection()
    {
        return Registration::with(['studentDetail', 'parentDetail', 'grade', 'documents'])
            ->orderBy('registration_number', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            // A - Info Pendaftaran
            'No',
            'No. Pendaftaran',
            'Kode Akses',
            'Tahun Ajaran',
            'Peminatan',
            'Rata-rata Nilai',
            'Status',
            'Tgl. Daftar',

            // B - Biodata Siswa
            'NISN',
            'NIK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'No. HP / WA',
            'Email',

            // C - Alamat Siswa
            'Provinsi',
            'Kota / Kabupaten',
            'Kecamatan',
            'Desa / Kelurahan',
            'Alamat Lengkap',
            'Kode Pos',

            // D - Asal Sekolah
            'Nama Sekolah Asal',
            'NPSN Sekolah Asal',
            'Alamat Sekolah Asal',

            // E - Nilai Rapor
            'Nilai Matematika',
            'Nilai B. Indonesia',
            'Nilai B. Inggris',
            'Nilai Agama',
            'Nilai IPA',
            'Nilai IPS',
            'Nilai PKN',

            // F - Data Orang Tua
            'Nama Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu',
            'Pekerjaan Ibu',
            'No. HP Ortu',
            'Alamat Ortu',
            'No. KPS / KIP',

            // G - Dokumen
            'Jumlah Dokumen Diupload',
        ];
    }

    public function map($reg): array
    {
        $this->rowNumber++;
        $s  = $reg->studentDetail;
        $p  = $reg->parentDetail;
        $g  = $reg->grade;
        $ad = $reg->additional_data ?? [];

        return [
            // Info Pendaftaran
            $this->rowNumber,
            $reg->registration_number ?? '',
            $reg->access_code ?? '',
            $reg->academic_year ?? '',
            $ad['major'] ?? '',
            $reg->average_score ?? '',
            $this->statusLabel($reg->status),
            $reg->created_at ? $reg->created_at->format('d/m/Y H:i') : '',

            // Biodata Siswa
            $s->nisn ?? '',
            $s->nik ?? '',
            $s->full_name ?? '',
            $s ? ($s->gender === 'L' ? 'Laki-laki' : ($s->gender === 'P' ? 'Perempuan' : '')) : '',
            $s->place_of_birth ?? '',
            $s->date_of_birth ? \Carbon\Carbon::parse($s->date_of_birth)->format('d/m/Y') : '',
            $s->religion ?? '',
            $s->phone ?? '',
            $s->email ?? '',

            // Alamat
            $s->province ?? '',
            $s->city ?? '',
            $s->district ?? '',
            $s->village ?? '',
            $s->address ?? '',
            $s->postal_code ?? '',

            // Sekolah Asal
            $s->origin_school_name ?? '',
            $s->origin_school_npsn ?? '',
            $s->origin_school_address ?? '',

            // Nilai
            $g->mathematics ?? '',
            $g->indonesian ?? '',
            $g->english ?? '',
            $g->religion ?? '',
            $g->ipa ?? '',
            $g->ips ?? '',
            $g->pkn ?? '',

            // Ortu
            $p->father_name ?? '',
            $p->father_occupation ?? '',
            $p->mother_name ?? '',
            $p->mother_occupation ?? '',
            $p->parent_phone ?? '',
            $p->parent_address ?? '',
            $p->aid_card_number ?? '',

            // Dokumen
            $reg->documents ? $reg->documents->count() : 0,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 18,  // No Pendaftaran
            'C' => 14,  // Kode Akses
            'D' => 12,  // Tahun Ajaran
            'E' => 16,  // Peminatan
            'F' => 14,  // Rata-rata
            'G' => 20,  // Status
            'H' => 18,  // Tgl Daftar
            'I' => 14,  // NISN
            'J' => 18,  // NIK
            'K' => 28,  // Nama Lengkap
            'L' => 14,  // Jenis Kelamin
            'M' => 18,  // Tempat Lahir
            'N' => 14,  // Tgl Lahir
            'O' => 12,  // Agama
            'P' => 18,  // No HP
            'Q' => 24,  // Email
            'R' => 16,  // Provinsi
            'S' => 20,  // Kota
            'T' => 18,  // Kecamatan
            'U' => 18,  // Desa
            'V' => 30,  // Alamat
            'W' => 10,  // Kode Pos
            'X' => 28,  // Nama Sekolah
            'Y' => 14,  // NPSN
            'Z' => 28,  // Alamat Sekolah
            'AA' => 16, // Matematika
            'AB' => 16, // B.Indonesia
            'AC' => 16, // B.Inggris
            'AD' => 14, // Agama (nilai)
            'AE' => 12, // IPA
            'AF' => 12, // IPS
            'AG' => 12, // PKN
            'AH' => 24, // Nama Ayah
            'AI' => 20, // Pekerjaan Ayah
            'AJ' => 24, // Nama Ibu
            'AK' => 20, // Pekerjaan Ibu
            'AL' => 18, // HP Ortu
            'AM' => 30, // Alamat Ortu
            'AN' => 18, // KPS/KIP
            'AO' => 14, // Jumlah Dok
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow  = $sheet->getHighestRow();
        $lastCol  = $sheet->getHighestColumn();
        $dataRange = 'A1:' . $lastCol . $lastRow;

        // ── Freeze panes: row 1 & column A ──
        $sheet->freezePane('B2');

        // ── Row height header ──
        $sheet->getRowDimension(1)->setRowHeight(36);

        // ── Zebra striping data rows ──
        for ($r = 2; $r <= $lastRow; $r++) {
            $fill = ($r % 2 === 0) ? 'FFF8F9FA' : 'FFFFFFFF';
            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fill]],
            ]);
        }

        // ── Group header colors ──
        // Pendaftaran: biru tua
        $sheet->getStyle('A1:H1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A8A']],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        // Biodata: hijau tua
        $sheet->getStyle('I1:Q1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1B5E20']],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        // Alamat: teal
        $sheet->getStyle('R1:W1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6E5E']],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        // Sekolah Asal: ungu
        $sheet->getStyle('X1:Z1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF5B21B6']],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        // Nilai: oranye
        $sheet->getStyle('AA1:AG1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC2410C']],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        // Ortu: merah tua
        $sheet->getStyle('AH1:AN1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF9F1239']],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        // Dokumen: abu
        $sheet->getStyle('AO1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF374151']],
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);

        // ── Borders semua cell ──
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);

        // ── Vertical align data rows ──
        $sheet->getStyle('A2:' . $lastCol . $lastRow)->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        return [];
    }

    private function statusLabel(string $status): string
    {
        return match($status) {
            'incomplete' => 'Belum Lengkap',
            'pending'    => 'Menunggu Verifikasi',
            'revision'   => 'Perlu Perbaikan',
            'verified'   => 'Terverifikasi',
            'passed'     => 'Diterima',
            'failed'     => 'Tidak Diterima',
            default      => strtoupper($status),
        };
    }
}
