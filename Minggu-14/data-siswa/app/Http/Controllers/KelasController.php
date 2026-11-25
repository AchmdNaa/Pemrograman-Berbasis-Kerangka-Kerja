<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD Laravel (2): data kelas memakai Query Builder (DB::table) tanpa Eloquent,
 * sebagai pembanding cara kerja CRUD dengan Eloquent pada SiswaController.
 */
class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $kelas = DB::table('kelas')
            ->leftJoin('siswa', 'siswa.kelas_id', '=', 'kelas.id')
            ->select('kelas.*', DB::raw('COUNT(siswa.id) AS jumlah_siswa'),
                DB::raw("SUM(CASE WHEN siswa.jenis_kelamin = 'L' THEN 1 ELSE 0 END) AS laki"),
                DB::raw("SUM(CASE WHEN siswa.jenis_kelamin = 'P' THEN 1 ELSE 0 END) AS perempuan"))
            ->groupBy('kelas.id', 'kelas.nama', 'kelas.tingkat', 'kelas.jurusan', 'kelas.wali_kelas', 'kelas.kapasitas', 'kelas.created_at', 'kelas.updated_at')
            ->orderBy('kelas.tingkat')->orderBy('kelas.nama')
            ->get();

        $edit = $request->query('edit') ? DB::table('kelas')->where('id', $request->query('edit'))->first() : null;

        return view('kelas.index', compact('kelas', 'edit'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        DB::table('kelas')->insert($data + ['created_at' => now(), 'updated_at' => now()]);

        return redirect()->route('kelas.index')->with('success', "Kelas {$data['nama']} ditambahkan.");
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        abort_unless(DB::table('kelas')->where('id', $id)->exists(), 404);
        $data = $this->validated($request, $id);
        DB::table('kelas')->where('id', $id)->update($data + ['updated_at' => now()]);

        return redirect()->route('kelas.index')->with('success', "Kelas {$data['nama']} diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $jumlah = DB::table('siswa')->where('kelas_id', $id)->count();
        if ($jumlah > 0) {
            return back()->with('error', "Kelas masih memiliki {$jumlah} siswa. Pindahkan siswa terlebih dahulu.");
        }
        DB::table('kelas')->where('id', $id)->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nama'       => ['required', 'string', 'max:20', Rule::unique('kelas', 'nama')->ignore($id)],
            'tingkat'    => ['required', Rule::in([10, 11, 12])],
            'jurusan'    => ['required', Rule::in(['IPA', 'IPS', 'Bahasa'])],
            'wali_kelas' => ['required', 'string', 'min:3', 'max:100'],
            'kapasitas'  => ['required', 'integer', 'between:10,40'],
        ], [], ['nama' => 'nama kelas', 'wali_kelas' => 'wali kelas']);
    }
}
