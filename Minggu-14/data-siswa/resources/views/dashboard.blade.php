@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="row g-3 mb-4">
    @foreach ([['Total siswa', $totalSiswa, 'bi-people', '#dc2626'], ['Laki-laki', $laki, 'bi-gender-male', '#2563eb'], ['Perempuan', $perempuan, 'bi-gender-female', '#db2777'], ['Kelas', $totalKelas, 'bi-door-open', '#16a34a']] as [$label, $num, $icon, $color])
        <div class="col-6 col-lg-3">
            <div class="card stat p-3 h-100" style="--c: {{ $color }}">
                <div class="d-flex justify-content-between small text-muted">{{ $label }} <i class="bi {{ $icon }}" style="color: {{ $color }}"></i></div>
                <div class="fs-2 fw-bold">{{ $num }}</div>
            </div>
        </div>
    @endforeach
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card p-3 h-100">
            <h2 class="h6 fw-bold">Jumlah Siswa per Kelas</h2>
            @php($max = max($perKelas->max('siswa_count'), 1))
            @foreach ($perKelas as $k)
                <div class="mb-2">
                    <div class="d-flex justify-content-between small"><span>{{ $k->nama }} <span class="text-muted">· {{ $k->wali_kelas }}</span></span><strong>{{ $k->siswa_count }}/{{ $k->kapasitas }}</strong></div>
                    <div class="progress" style="height: 6px"><div class="progress-bar bg-danger" style="width: {{ round($k->siswa_count / $max * 100) }}%"></div></div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between mb-2"><h2 class="h6 fw-bold mb-0">Siswa Terbaru</h2><a href="{{ route('siswa.create') }}" class="small"><i class="bi bi-plus"></i> Tambah</a></div>
            @foreach ($terbaru as $s)
                <a href="{{ route('siswa.show', $s) }}" class="d-flex align-items-center gap-2 border-bottom py-2 text-dark">
                    @include('siswa._avatar', ['s' => $s])
                    <div class="small"><div class="fw-semibold">{{ $s->nama }}</div><div class="text-muted">{{ $s->nis }} · {{ $s->kelas->nama }}</div></div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
