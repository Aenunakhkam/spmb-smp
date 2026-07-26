<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('student_registration_id')) {
            return redirect()->route('check-status')->withErrors(['message' => 'Sesi berakhir atau Anda belum login. Silakan masukkan Nomor Pendaftaran dan Kode Akses.']);
        }

        return $next($request);
    }
}
