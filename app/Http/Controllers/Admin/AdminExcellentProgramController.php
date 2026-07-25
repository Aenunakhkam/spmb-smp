<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExcellentProgram;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminExcellentProgramController extends Controller
{
    public function index()
    {
        $programs = ExcellentProgram::orderBy('id')->get();
        
        return Inertia::render('Admin/ExcellentPrograms', [
            'programs' => $programs
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:100',
            'color_theme' => 'required|string|max:50',
        ]);

        ExcellentProgram::create($validated);

        return redirect()->back()->with('success', 'Program unggulan berhasil ditambahkan.');
    }

    public function update(Request $request, ExcellentProgram $excellentProgram)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:100',
            'color_theme' => 'required|string|max:50',
        ]);

        $excellentProgram->update($validated);

        return redirect()->back()->with('success', 'Program unggulan berhasil diperbarui.');
    }

    public function destroy(ExcellentProgram $excellentProgram)
    {
        $excellentProgram->delete();

        return redirect()->back()->with('success', 'Program unggulan berhasil dihapus.');
    }
}
