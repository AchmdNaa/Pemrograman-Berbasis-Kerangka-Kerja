<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\MatakuliahModel;
use App\Models\NilaiModel;
use App\Models\PesananModel;
use App\Models\ProdukModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $mahasiswa = new MahasiswaModel();
        $ipk = (new NilaiModel())->ipkSemua();
        $rataIpk = $ipk === [] ? 0 : round(array_sum(array_column($ipk, 'ipk')) / count($ipk), 2);

        $perProdi = $mahasiswa->select('prodi, COUNT(*) AS jumlah')->groupBy('prodi')->orderBy('jumlah', 'DESC')->findAll();

        $pesanan = new PesananModel();

        return view('admin/dashboard', [
            'title'         => 'Dashboard',
            'jumlahMhs'     => (new MahasiswaModel())->countAllResults(),
            'jumlahMk'      => (new MatakuliahModel())->countAllResults(),
            'rataIpk'       => $rataIpk,
            'perProdi'      => $perProdi,
            'jumlahProduk'  => (new ProdukModel())->countAllResults(),
            'stokHabis'     => (new ProdukModel())->where('stok', 0)->countAllResults(),
            'pesananBaru'   => (new PesananModel())->where('status', 'baru')->countAllResults(),
            'omzet'         => (int) ($pesanan->selectSum('total')->where('status !=', 'batal')->first()['total'] ?? 0),
            'pesananAkhir'  => (new PesananModel())->orderBy('id', 'DESC')->findAll(5),
            'mhsTerbaru'    => (new MahasiswaModel())->orderBy('id', 'DESC')->findAll(5),
        ]);
    }
}
