<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalSiswa' => Siswa::count(),
            'laki'       => Siswa::where('jenis_kelamin', 'L')->count(),
            'perempuan'  => Siswa::where('jenis_kelamin', 'P')->count(),
            'totalKelas' => Kelas::count(),
            'perKelas'   => Kelas::withCount('siswa')->orderBy('tingkat')->orderBy('nama')->get(),
            'terbaru'    => Siswa::with('kelas')->latest()->take(5)->get(),
        ]);
    }
}
