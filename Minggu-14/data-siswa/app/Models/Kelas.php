<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['nama', 'tingkat', 'jurusan', 'wali_kelas', 'kapasitas'];

    /** Satu kelas memiliki banyak siswa. */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }
}
