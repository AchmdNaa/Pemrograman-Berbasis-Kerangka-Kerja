<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Siswa extends Model
{
    /** Nama tabel tidak mengikuti konvensi bahasa Inggris (siswas), jadi ditulis eksplisit. */
    protected $table = 'siswa';

    /** Kolom yang boleh diisi lewat create()/update() (perlindungan mass assignment). */
    protected $fillable = [
        'nis', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'kelas_id', 'email', 'no_hp', 'alamat', 'foto',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    /** Setiap siswa berada di satu kelas. */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /** Accessor: umur siswa ($siswa->umur). */
    protected function umur(): Attribute
    {
        return Attribute::get(fn () => $this->tanggal_lahir?->age);
    }

    /** Accessor: URL foto atau avatar inisial ($siswa->foto_url). */
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->foto && Storage::disk('public')->exists($this->foto)) {
                return Storage::url($this->foto);
            }

            return null;
        });
    }

    protected function inisial(): Attribute
    {
        return Attribute::get(fn () => collect(explode(' ', $this->nama))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode(''));
    }

    /** Local scope: Siswa::cari('budi')->get() */
    public function scopeCari(Builder $query, ?string $keyword): Builder
    {
        return $query->when($keyword, fn ($q) => $q->where(function ($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")->orWhere('nis', 'like', "%{$keyword}%");
        }));
    }
}
