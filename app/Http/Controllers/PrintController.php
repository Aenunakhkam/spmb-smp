<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    /**
     * Get settings as key-value pairs
     */
    private function getSettings()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    /**
     * Preview Kartu Pendaftaran (HTML)
     */
    public function kartu($id)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail'])
            ->findOrFail($id);
        $settings = $this->getSettings();
        return view('print.kartu', compact('registration', 'settings'));
    }

    /**
     * Preview Formulir Pendaftaran (HTML)
     */
    public function formulir($id)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail', 'grade'])
            ->findOrFail($id);
        $settings = $this->getSettings();
        return view('print.formulir', compact('registration', 'settings'));
    }

    /**
     * Download Kartu Peserta sebagai PDF
     */
    public function downloadKartu($id)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail'])
            ->findOrFail($id);
        $settings = $this->getSettings();

        $pdf = Pdf::loadView('print.kartu', compact('registration', 'settings'))
            ->setPaper([0, 0, 609.45, 935.43], 'portrait');

        $filename = 'Kartu_Peserta_' . $registration->registration_number . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Download Formulir Pendaftaran sebagai PDF
     */
    public function downloadFormulir($id)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail', 'grade'])
            ->findOrFail($id);
        $settings = $this->getSettings();

        $pdf = Pdf::loadView('print.formulir', compact('registration', 'settings'))
            ->setPaper([0, 0, 609.45, 935.43], 'portrait');

        $filename = 'Formulir_Pendaftaran_' . $registration->registration_number . '.pdf';
        return $pdf->download($filename);
    }
}
