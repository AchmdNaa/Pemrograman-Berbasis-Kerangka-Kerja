<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiModel extends Model
{
    /** Bobot nilai huruf sesuai pedoman akademik. */
    public const BOBOT = ['A' => 4.0, 'AB' => 3.5, 'B' => 3.0, 'BC' => 2.5, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0];

    protected $table         = 'nilai';
    protected $allowedFields = ['mahasiswa_id', 'matakuliah_id', 'nilai_huruf'];
    protected $useTimestamps = true;
    protected $updatedField  = '';
    protected $returnType    = 'array';

    /** Transkrip satu mahasiswa: nilai + data mata kuliah. */
    public function transkrip(int $mahasiswaId): array
    {
        return $this->select('nilai.id, nilai.nilai_huruf, matakuliah.kode, matakuliah.nama, matakuliah.sks, matakuliah.semester')
            ->join('matakuliah', 'matakuliah.id = nilai.matakuliah_id')
            ->where('nilai.mahasiswa_id', $mahasiswaId)
            ->orderBy('matakuliah.semester')->orderBy('matakuliah.kode')
            ->findAll();
    }

    /** IPK = Σ(bobot × sks) / Σ sks. */
    public static function hitungIpk(array $transkrip): array
    {
        $totalSks = 0;
        $totalMutu = 0.0;
        foreach ($transkrip as $row) {
            $totalSks += (int) $row['sks'];
            $totalMutu += self::BOBOT[$row['nilai_huruf']] * (int) $row['sks'];
        }

        return [
            'sks' => $totalSks,
            'ipk' => $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.0,
        ];
    }

    /** IPK seluruh mahasiswa sekaligus (satu query) untuk dashboard dan daftar. */
    public function ipkSemua(): array
    {
        $rows = $this->select('nilai.mahasiswa_id, nilai.nilai_huruf, matakuliah.sks')
            ->join('matakuliah', 'matakuliah.id = nilai.matakuliah_id')
            ->findAll();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['mahasiswa_id']][] = $row;
        }

        return array_map(static fn ($t) => self::hitungIpk($t), $grouped);
    }
}
