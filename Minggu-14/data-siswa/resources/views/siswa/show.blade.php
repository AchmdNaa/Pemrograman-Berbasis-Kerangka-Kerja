@extends('layouts.app')
@section('title', $siswa->nama)
@section('content')
<a href="{{ route('siswa.index') }}" class="small"><i class="bi bi-arrow-left"></i> Data siswa</a>
<div class="card p-4 mt-2">
    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
        @include('siswa._avatar', ['s' => $siswa, 'size' => 96])
        <div class="flex-grow-1">
            <h1 class="h4 fw-bold mb-0">{{ $siswa->nama }}</h1>
            <div class="text-muted">NIS {{ $siswa->nis }} · {{ $siswa->kelas->nama }} ({{ $siswa->kelas->jurusan }})</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('siswa.edit', $siswa) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
            <form action="{{ route('siswa.destroy', $siswa) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button></form>
        </div>
    </div>
    <table class="table">
        <tr><th class="text-muted fw-normal w-25">Jenis kelamin</th><td>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
        <tr><th class="text-muted fw-normal">Tempat, tanggal lahir</th><td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->translatedFormat('d F Y') }} ({{ $siswa->umur }} tahun)</td></tr>
        <tr><th class="text-muted fw-normal">Wali kelas</th><td>{{ $siswa->kelas->wali_kelas }}</td></tr>
        <tr><th class="text-muted fw-normal">Email</th><td>{{ $siswa->email ?? '-' }}</td></tr>
        <tr><th class="text-muted fw-normal">Nomor HP</th><td>{{ $siswa->no_hp ?? '-' }}</td></tr>
        <tr><th class="text-muted fw-normal">Alamat</th><td>{{ $siswa->alamat }}</td></tr>
        <tr><th class="text-muted fw-normal">Dibuat / diubah</th><td>{{ $siswa->created_at->translatedFormat('d M Y H:i') }} / {{ $siswa->updated_at->diffForHumans() }}</td></tr>
    </table>
</div>
@endsection
