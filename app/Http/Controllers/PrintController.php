<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Http\Request;

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
     * Cetak Kartu Pendaftaran
     */
    public function kartu($id)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail'])
            ->findOrFail($id);
            
        $settings = $this->getSettings();
        
        return view('print.kartu', compact('registration', 'settings'));
    }

    /**
     * Cetak Formulir Pendaftaran
     */
    public function formulir($id)
    {
        $registration = Registration::with(['studentDetail', 'parentDetail', 'grade'])
            ->findOrFail($id);
            
        $settings = $this->getSettings();
        
        return view('print.formulir', compact('registration', 'settings'));
    }
}
