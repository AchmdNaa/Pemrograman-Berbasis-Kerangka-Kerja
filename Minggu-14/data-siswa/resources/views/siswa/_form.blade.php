{{-- Form dipakai bersama oleh create & edit --}}
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="nis">NIS</label>
                    <input class="form-control @error('nis') is-invalid @enderror" id="nis" name="nis" maxlength="10" value="{{ old('nis', $siswa->nis) }}">
                    @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="nama">Nama lengkap</label>
                    <input class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label d-block">Jenis kelamin</label>
                    @foreach (['L' => 'Laki-laki', 'P' => 'Perempuan'] as $v => $l)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio" name="jenis_kelamin" id="jk{{ $v }}" value="{{ $v }}" @checked(old('jenis_kelamin', $siswa->jenis_kelamin) === $v)>
                            <label class="form-check-label" for="jk{{ $v }}">{{ $l }}</label>
                        </div>
                    @endforeach
                    @error('jenis_kelamin')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="tempat_lahir">Tempat lahir</label>
                    <input class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}">
                    @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="tanggal_lahir">Tanggal lahir</label>
                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir?->format('Y-m-d')) }}">
                    @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="kelas_id">Kelas</label>
                    <select class="form-select @error('kelas_id') is-invalid @enderror" id="kelas_id" name="kelas_id">
                        <option value="">— pilih —</option>
                        @foreach ($kelas as $k)<option value="{{ $k->id }}" @selected(old('kelas_id', $siswa->kelas_id) == $k->id)>{{ $k->nama }}</option>@endforeach
                    </select>
                    @error('kelas_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="email">Email <span class="text-muted small">(opsional)</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $siswa->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="no_hp">Nomor HP <span class="text-muted small">(opsional)</span></label>
                    <input class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}">
                    @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="alamat">Alamat</label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $siswa->alamat) }}</textarea>
                    @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4 text-center">
            <label class="form-label fw-semibold">Foto</label>
            <div class="mb-3">@include('siswa._avatar', ['s' => $siswa->exists ? $siswa : new \App\Models\Siswa(['nama' => 'Foto Siswa']), 'size' => 140])</div>
            <input type="file" class="form-control @error('foto') is-invalid @enderror" name="foto" accept="image/png,image/jpeg,image/webp">
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">JPG/PNG/WEBP, maks 1 MB</div>
            @if ($siswa->foto)
                <div class="form-check text-start mt-2"><input class="form-check-input" type="checkbox" name="hapus_foto" value="1" id="hapus_foto"><label class="form-check-label small" for="hapus_foto">Hapus foto</label></div>
            @endif
        </div>
        <button class="btn btn-primary btn-lg w-100 mt-3"><i class="bi bi-save"></i> Simpan</button>
        <a href="{{ url()->previous() === url()->current() ? route('siswa.index') : url()->previous() }}" class="btn btn-light w-100 mt-2">Batal</a>
    </div>
</div>
