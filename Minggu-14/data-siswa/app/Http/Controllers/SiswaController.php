<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiswaRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * CRUD Laravel (1): data siswa dengan Eloquent ORM + Resource Controller.
 * Route::resource('siswa', SiswaController::class) membuat 7 route sekaligus.
 */
class SiswaController extends Controller
{
    /** GET /siswa — daftar + pencarian + filter + pagination. */
    public function index(Request $request): View
    {
        $siswa = Siswa::with('kelas')                         // eager loading: hindari N+1 query
            ->cari($request->query('q'))                      // local scope
            ->when($request->query('kelas'), fn ($q, $k) => $q->where('kelas_id', $k))
            ->when($request->query('jk'), fn ($q, $jk) => $q->where('jenis_kelamin', $jk))
            ->orderBy($this->sortColumn($request), $request->query('arah') === 'desc' ? 'desc' : 'asc')
            ->paginate(10)
            ->withQueryString();                               // pertahankan ?q=&kelas= saat pindah halaman

        return view('siswa.index', [
            'siswa' => $siswa,
            'kelas' => Kelas::orderBy('nama')->get(),
        ]);
    }

    /** GET /siswa/create */
    public function create(): View
    {
        return view('siswa.create', ['siswa' => new Siswa(), 'kelas' => Kelas::orderBy('nama')->get()]);
    }

    /** POST /siswa */
    public function store(SiswaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-siswa', 'public');
        }

        $siswa = Siswa::create($data);

        return redirect()->route('siswa.index')->with('success', "Siswa {$siswa->nama} berhasil ditambahkan.");
    }

    /** GET /siswa/{siswa} — route model binding: Laravel otomatis mencari Siswa berdasarkan id. */
    public function show(Siswa $siswa): View
    {
        return view('siswa.show', ['siswa' => $siswa->load('kelas')]);
    }

    /** GET /siswa/{siswa}/edit */
    public function edit(Siswa $siswa): View
    {
        return view('siswa.edit', ['siswa' => $siswa, 'kelas' => Kelas::orderBy('nama')->get()]);
    }

    /** PUT /siswa/{siswa} */
    public function update(SiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $this->hapusFoto($siswa->foto);
            $data['foto'] = $request->file('foto')->store('foto-siswa', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            $this->hapusFoto($siswa->foto);
            $data['foto'] = null;
        } else {
            unset($data['foto']);
        }

        $siswa->update($data);

        return redirect()->route('siswa.show', $siswa)->with('success', "Data {$siswa->nama} berhasil diperbarui.");
    }

    /** DELETE /siswa/{siswa} */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        $this->hapusFoto($siswa->foto);
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', "Data {$siswa->nama} telah dihapus.");
    }

    private function hapusFoto(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /** Kolom pengurutan hanya dari daftar yang diizinkan. */
    private function sortColumn(Request $request): string
    {
        return in_array($request->query('urut'), ['nis', 'nama', 'tanggal_lahir', 'created_at'], true)
            ? $request->query('urut')
            : 'nama';
    }
}
