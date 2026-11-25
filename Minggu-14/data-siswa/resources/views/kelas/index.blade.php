@extends('layouts.app')
@section('title', 'Data Kelas')
@section('content')
<h1 class="h4 fw-bold mb-1">Data Kelas</h1>
<p class="text-muted small">CRUD dengan <strong>Query Builder</strong> (<code>DB::table('kelas')</code>), sebagai pembanding CRUD Eloquent pada data siswa.</p>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-3">
            <table class="table align-middle small mb-0">
                <thead class="table-light"><tr><th>Kelas</th><th>Wali kelas</th><th class="text-center">L</th><th class="text-center">P</th><th style="width: 160px">Terisi</th><th class="text-end">Aksi</th></tr></thead>
                @foreach ($kelas as $k)
                    <tr>
                        <td><span class="fw-semibold">{{ $k->nama }}</span><div class="text-muted">Tingkat {{ $k->tingkat }} · {{ $k->jurusan }}</div></td>
                        <td>{{ $k->wali_kelas }}</td>
                        <td class="text-center">{{ (int) $k->laki }}</td>
                        <td class="text-center">{{ (int) $k->perempuan }}</td>
                        <td><div class="small">{{ $k->jumlah_siswa }}/{{ $k->kapasitas }}</div><div class="progress" style="height: 5px"><div class="progress-bar bg-danger" style="width: {{ min(100, round($k->jumlah_siswa / $k->kapasitas * 100)) }}%"></div></div></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('siswa.index', ['kelas' => $k->id]) }}" class="btn btn-sm btn-outline-secondary" title="Lihat siswa"><i class="bi bi-people"></i></a>
                            <a href="{{ route('kelas.index', ['edit' => $k->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas {{ $k->nama }}?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h2 class="h6 fw-bold">{{ $edit ? 'Edit Kelas' : 'Tambah Kelas' }}</h2>
            <form action="{{ $edit ? route('kelas.update', $edit->id) : route('kelas.store') }}" method="POST" novalidate>
                @csrf
                @if ($edit) @method('PUT') @endif
                <div class="mb-2">
                    <label class="form-label small" for="nama">Nama kelas</label>
                    <input class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" placeholder="X IPA 1" value="{{ old('nama', $edit->nama ?? '') }}">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col mb-2">
                        <label class="form-label small" for="tingkat">Tingkat</label>
                        <select class="form-select @error('tingkat') is-invalid @enderror" id="tingkat" name="tingkat">@foreach ([10, 11, 12] as $t)<option @selected(old('tingkat', $edit->tingkat ?? '') == $t)>{{ $t }}</option>@endforeach</select>
                        @error('tingkat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col mb-2">
                        <label class="form-label small" for="jurusan">Jurusan</label>
                        <select class="form-select @error('jurusan') is-invalid @enderror" id="jurusan" name="jurusan">@foreach (['IPA', 'IPS', 'Bahasa'] as $j)<option @selected(old('jurusan', $edit->jurusan ?? '') === $j)>{{ $j }}</option>@endforeach</select>
                        @error('jurusan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small" for="wali_kelas">Wali kelas</label>
                    <input class="form-control @error('wali_kelas') is-invalid @enderror" id="wali_kelas" name="wali_kelas" value="{{ old('wali_kelas', $edit->wali_kelas ?? '') }}">
                    @error('wali_kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small" for="kapasitas">Kapasitas</label>
                    <input type="number" class="form-control @error('kapasitas') is-invalid @enderror" id="kapasitas" name="kapasitas" value="{{ old('kapasitas', $edit->kapasitas ?? 36) }}">
                    @error('kapasitas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                @if ($edit)<a href="{{ route('kelas.index') }}" class="btn btn-light">Batal</a>@endif
            </form>
        </div>
    </div>
</div>
@endsection
