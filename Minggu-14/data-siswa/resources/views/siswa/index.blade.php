@extends('layouts.app')
@section('title', 'Data Siswa')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-0">Data Siswa</h1>
    <a href="{{ route('siswa.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah Siswa</a>
</div>
<div class="card p-3">
    <form method="GET" action="{{ route('siswa.index') }}" class="row g-2 mb-3">
        <div class="col-md-4"><input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Cari nama atau NIS"></div>
        <div class="col-md-2">
            <select name="kelas" class="form-select form-select-sm">
                <option value="">Semua kelas</option>
                @foreach ($kelas as $k)<option value="{{ $k->id }}" @selected(request('kelas') == $k->id)>{{ $k->nama }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="jk" class="form-select form-select-sm">
                <option value="">L / P</option>
                <option value="L" @selected(request('jk') === 'L')>Laki-laki</option>
                <option value="P" @selected(request('jk') === 'P')>Perempuan</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="urut" class="form-select form-select-sm">
                @foreach (['nama' => 'Urut nama', 'nis' => 'Urut NIS', 'tanggal_lahir' => 'Urut tanggal lahir', 'created_at' => 'Urut tanggal input'] as $v => $l)
                    <option value="{{ $v }}" @selected(request('urut', 'nama') === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1"><button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-funnel"></i></button></div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle small">
            <thead class="table-light"><tr><th>#</th><th>Siswa</th><th>NIS</th><th>L/P</th><th>Kelas</th><th>Tanggal lahir</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse ($siswa as $s)
                <tr>
                    <td class="text-muted">{{ $siswa->firstItem() + $loop->index }}</td>
                    <td><div class="d-flex align-items-center gap-2">@include('siswa._avatar', ['s' => $s])<div><div class="fw-semibold">{{ $s->nama }}</div><div class="text-muted">{{ $s->email ?? '-' }}</div></div></div></td>
                    <td><code>{{ $s->nis }}</code></td>
                    <td><span class="badge {{ $s->jenis_kelamin === 'L' ? 'text-bg-primary' : 'text-bg-danger' }}" style="{{ $s->jenis_kelamin === 'L' ? 'background:#2563eb!important' : 'background:#db2777!important' }}">{{ $s->jenis_kelamin }}</span></td>
                    <td>{{ $s->kelas->nama }}</td>
                    <td>{{ $s->tanggal_lahir->translatedFormat('d M Y') }} <span class="text-muted">({{ $s->umur }} th)</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('siswa.show', $s) }}" class="btn btn-sm btn-outline-secondary" title="Detail"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('siswa.edit', $s) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('siswa.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data {{ $s->nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data siswa yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $siswa->links() }}
</div>
@endsection
