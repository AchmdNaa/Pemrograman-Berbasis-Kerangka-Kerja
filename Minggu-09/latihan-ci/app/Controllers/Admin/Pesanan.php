<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PesananItemModel;
use App\Models\PesananModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Pesanan extends BaseController
{
    public function index(): string
    {
        $status = (string) $this->request->getGet('status');
        $model = new PesananModel();
        if (in_array($status, PesananModel::STATUS, true)) {
            $model->where('status', $status);
        }

        return view('admin/pesanan/index', [
            'title'   => 'Pesanan',
            'pesanan' => $model->orderBy('id', 'DESC')->paginate(10),
            'pager'   => $model->pager,
            'status'  => $status,
        ]);
    }

    public function show(int $id): string
    {
        $pesanan = (new PesananModel())->find($id) ?? throw PageNotFoundException::forPageNotFound();

        return view('admin/pesanan/show', [
            'title'   => 'Pesanan ' . $pesanan['kode'],
            'pesanan' => $pesanan,
            'items'   => (new PesananItemModel())->where('pesanan_id', $id)->findAll(),
        ]);
    }

    public function status(int $id): RedirectResponse
    {
        $model = new PesananModel();
        $pesanan = $model->find($id) ?? throw PageNotFoundException::forPageNotFound();
        $baru = (string) $this->request->getPost('status');

        if (! in_array($baru, PesananModel::STATUS, true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }
        if (in_array($pesanan['status'], ['selesai', 'batal'], true)) {
            return redirect()->back()->with('error', 'Pesanan yang sudah selesai/batal tidak dapat diubah.');
        }

        $db = db_connect();
        $db->transStart();
        $model->update($id, ['status' => $baru]);

        // Pembatalan mengembalikan stok produk.
        if ($baru === 'batal') {
            foreach ((new PesananItemModel())->where('pesanan_id', $id)->findAll() as $item) {
                if ($item['produk_id']) {
                    $db->table('produk')->set('stok', 'stok + ' . (int) $item['qty'], false)
                        ->where('id', $item['produk_id'])->update();
                }
            }
        }
        $db->transComplete();

        return redirect()->to("/admin/pesanan/{$id}")->with('success', "Status pesanan menjadi \"{$baru}\".");
    }
}
