<?php

namespace App\Controllers;

use App\Libraries\Keranjang as Cart;
use App\Models\PesananItemModel;
use App\Models\PesananModel;
use App\Models\ProdukModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Keranjang belanja (session) dan checkout.
 */
class Keranjang extends BaseController
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function index(): string
    {
        $detail = $this->cart->detail();

        return view('toko/keranjang', ['title' => 'Keranjang Belanja'] + $detail);
    }

    public function add(): RedirectResponse
    {
        $id = (int) $this->request->getPost('produk_id');
        $qty = max(1, (int) $this->request->getPost('qty'));
        $produk = (new ProdukModel())->find($id);

        if ($produk === null) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }
        $diKeranjang = $this->cart->all()[$id] ?? 0;
        if ($diKeranjang + $qty > $produk['stok']) {
            return redirect()->back()->with('error', "Stok {$produk['nama']} tidak mencukupi (tersisa {$produk['stok']}).");
        }

        $this->cart->add($id, $qty);

        return redirect()->back()->with('success', "{$produk['nama']} ditambahkan ke keranjang.");
    }

    public function update(): RedirectResponse
    {
        $qtys = (array) $this->request->getPost('qty');
        $stok = array_column((new ProdukModel())->whereIn('id', array_keys($qtys) ?: [0])->findAll(), 'stok', 'id');

        $pesan = [];
        foreach ($qtys as $id => $qty) {
            $id = (int) $id;
            $qty = (int) $qty;
            if (isset($stok[$id]) && $qty > $stok[$id]) {
                $qty = (int) $stok[$id];
                $pesan[] = 'Sebagian jumlah disesuaikan dengan stok yang tersedia.';
            }
            $this->cart->update($id, $qty);
        }

        return redirect()->to('/keranjang')->with('success', $pesan[0] ?? 'Keranjang diperbarui.');
    }

    public function remove(int $id): RedirectResponse
    {
        $this->cart->remove($id);

        return redirect()->to('/keranjang')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()->to('/keranjang')->with('success', 'Keranjang dikosongkan.');
    }

    public function checkout(): string|RedirectResponse
    {
        $detail = $this->cart->detail();
        if ($detail['items'] === []) {
            return redirect()->to('/keranjang')->with('error', 'Keranjang masih kosong.');
        }

        return view('toko/checkout', ['title' => 'Checkout'] + $detail);
    }

    public function placeOrder(): RedirectResponse
    {
        $rules = [
            'nama_pembeli' => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
            'telepon'      => ['label' => 'Telepon', 'rules' => 'required|regex_match[/^(08|\+628)\d{7,11}$/]'],
            'alamat'       => ['label' => 'Alamat', 'rules' => 'required|min_length[10]'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $detail = $this->cart->detail();
        if ($detail['items'] === []) {
            return redirect()->to('/keranjang')->with('error', 'Keranjang masih kosong.');
        }

        $db = db_connect();
        $db->transStart();

        $pesananModel = new PesananModel();
        $kode = 'INV-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $pesananId = $pesananModel->insert([
            'kode'         => $kode,
            'nama_pembeli' => trim((string) $this->request->getPost('nama_pembeli')),
            'telepon'      => trim((string) $this->request->getPost('telepon')),
            'alamat'       => trim((string) $this->request->getPost('alamat')),
            'total'        => $detail['total'],
            'status'       => 'baru',
        ]);

        $itemModel = new PesananItemModel();
        foreach ($detail['items'] as $item) {
            // Kurangi stok hanya jika masih cukup (mencegah stok minus bila dua pembeli checkout bersamaan).
            $db->table('produk')
                ->set('stok', 'stok - ' . (int) $item['qty'], false)
                ->where('id', $item['id'])
                ->where('stok >=', (int) $item['qty'])
                ->update();
            if ($db->affectedRows() !== 1) {
                $db->transRollback();

                return redirect()->to('/keranjang')->with('error', "Stok {$item['nama']} baru saja berubah. Silakan periksa keranjang.");
            }

            $itemModel->insert([
                'pesanan_id'  => $pesananId,
                'produk_id'   => $item['id'],
                'nama_produk' => $item['nama'],
                'harga'       => $item['harga'],
                'qty'         => $item['qty'],
                'subtotal'    => $item['subtotal'],
            ]);
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->to('/keranjang')->with('error', 'Pesanan gagal diproses, silakan coba lagi.');
        }

        $this->cart->clear();

        return redirect()->to('/pesanan/' . $kode)->with('success', 'Terima kasih! Pesanan Anda berhasil dibuat.');
    }

    public function sukses(string $kode): string
    {
        $pesanan = (new PesananModel())->where('kode', $kode)->first()
            ?? throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        return view('toko/sukses', [
            'title'   => 'Pesanan ' . $kode,
            'pesanan' => $pesanan,
            'items'   => (new PesananItemModel())->where('pesanan_id', $pesanan['id'])->findAll(),
        ]);
    }
}
