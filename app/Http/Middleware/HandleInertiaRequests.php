<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Setting;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Fetch only necessary settings to share globally to avoid overhead
        $settings = Setting::whereIn('key', ['app_name', 'school_name', 'school_logo_path', 'subjects_required', 'contact_whatsapp', 'contact_email', 'phone', 'email'])->pluck('value', 'key')->toArray();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'recover_success' => fn () => $request->session()->get('recover_success'),
                'registration_success' => fn () => $request->session()->get('registration_success'),
            ],
            'app_settings' => [
                'app_name' => $settings['app_name'] ?? 'SPMB Online',
                'school_name' => $settings['school_name'] ?? 'Bustanul Ulum',
                'school_logo_path' => isset($settings['school_logo_path']) ? '/storage/' . $settings['school_logo_path'] : null,
                'subjects_required' => json_decode($settings['subjects_required'] ?? '[]', true),
                'contact_whatsapp' => $settings['contact_whatsapp'] ?? ($settings['phone'] ?? ''),
                'contact_email' => $settings['contact_email'] ?? ($settings['email'] ?? ''),
            ],
        ];
    }
}
