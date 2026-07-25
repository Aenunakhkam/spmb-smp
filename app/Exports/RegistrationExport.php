<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RegistrationExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private $rowNumber = 0;

    public function collection()
    {
        return Registration::with(['studentDetail', 'grade'])->orderBy('created_at', 'asc')->get();
    }

    public function map($registration): array
    {
        $this->rowNumber++;
        $statusLabels = [
            'incomplete' => 'Belum Lengkap',
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'passed' => 'Diterima',
            'failed' => 'Tidak Diterima',
        ];

        return [
            $this->rowNumber,
            $registration->registration_number,
            isset($registration->studentDetail->nisn) ? "'" . $registration->studentDetail->nisn : '-',
            $registration->studentDetail->full_name ?? '-',
            $registration->studentDetail->gender == 'L' ? 'Laki-laki' : ($registration->studentDetail->gender == 'P' ? 'Perempuan' : '-'),
            $registration->studentDetail->origin_school_name ?? '-',
            $registration->additional_data['major'] ?? 'UMUM',
            $registration->average_score ?? '-',
            $statusLabels[$registration->status] ?? strtoupper($registration->status),
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'No Pendaftaran',
            'NISN',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Asal Sekolah',
            'Peminatan',
            'Rata-rata Nilai',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Style for headings
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF1B5E20', // Dark green header
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style for entire table (Borders)
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Style for data rows alignment
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'wrapText' => true
            ],
        ]);

        return [];
    }
}
