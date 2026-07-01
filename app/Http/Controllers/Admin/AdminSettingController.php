<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminSettingController extends Controller
{
    use Loggable;

    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Admin/Settings', [
            'settings' => [
                'app_name' => $settings['app_name'] ?? 'SPMB Online',
                'school_name' => $settings['school_name'] ?? 'Bustanul Ulum',
                'developer_name' => $settings['developer_name'] ?? 'Tim IT',
                'app_version' => $settings['app_version'] ?? '1.0.0',
                'academic_year' => $settings['academic_year'] ?? '2026/2027',
                'school_logo_path' => $settings['school_logo_path'] ?? null,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string',
            'school_name' => 'required|string',
            'developer_name' => 'required|string',
            'app_version' => 'required|string',
            'academic_year' => 'required|string',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $settingsToSave = [
            'app_name' => $request->app_name,
            'school_name' => $request->school_name,
            'developer_name' => $request->developer_name,
            'app_version' => $request->app_version,
            'academic_year' => $request->academic_year,
        ];

        if ($request->hasFile('school_logo')) {
            $path = $request->file('school_logo')->store('logo', 'public');
            $settingsToSave['school_logo_path'] = $path;
        }

        foreach ($settingsToSave as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->logActivity('update_app_identity', 'Setting', [
            'app_name' => $request->app_name,
            'school_name' => $request->school_name,
        ]);

        return back()->with('success', 'Identitas Aplikasi berhasil diperbarui.');
    }

    public function deleteLogo()
    {
        $logoSetting = Setting::where('key', 'school_logo_path')->first();
        
        if ($logoSetting && $logoSetting->value) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($logoSetting->value);
            $logoSetting->delete();
            
            $this->logActivity('delete_school_logo', 'Setting', []);
            
            return back()->with('success', 'Logo sekolah berhasil dihapus.');
        }

        return back()->with('error', 'Logo tidak ditemukan.');
    }
}
