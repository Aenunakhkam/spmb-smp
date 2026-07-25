<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExcellentProgram;

class ExcellentProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'title' => "Program Tahfidz Al-Qur'an",
                'description' => "Merupakan program unggulan berkelanjutan (excellent program) yang didesain secara khusus untuk melahirkan generasi penghafal Al-Qur'an (Hafidz/Hafidzah). Pembinaan dilakukan melalui metode setoran (ziyadah) dan pengulangan (muraja'ah) secara intensif dengan target capaian juz yang terukur setiap semesternya, didampingi langsung oleh para asatidz yang bersanad.",
                'icon' => 'mdi-book-open-page-variant',
                'color_theme' => 'blue',
            ],
            [
                'title' => 'Kajian Kitab Kuning',
                'description' => "Mengintegrasikan kurikulum pesantren salaf dengan pendidikan formal. Santri akan dididik untuk memiliki kompetensi unggul dalam membaca, memahami, serta menganalisis turats (kitab kuning) seperti Nahwu, Shorof, Fiqih, dan Akhlaq sebagai fondasi utama dalam merespons tantangan zaman berlandaskan nilai-nilai Ahlussunnah Wal Jamaah.",
                'icon' => 'mdi-library',
                'color_theme' => 'amber',
            ],
            [
                'title' => 'Kelas MIPA (Olimpiade & Sains)',
                'description' => "Program percepatan dan pendalaman materi di bidang Matematika dan Ilmu Pengetahuan Alam. Kelas ini diorientasikan untuk mempersiapkan peserta didik berkompetisi di berbagai ajang kejuaraan sains tingkat nasional maupun internasional (OSN/KSM), dibekali dengan fasilitas laboratorium modern dan bimbingan eksklusif.",
                'icon' => 'mdi-flask',
                'color_theme' => 'emerald',
            ]
        ];

        foreach ($programs as $program) {
            ExcellentProgram::create($program);
        }
    }
}
