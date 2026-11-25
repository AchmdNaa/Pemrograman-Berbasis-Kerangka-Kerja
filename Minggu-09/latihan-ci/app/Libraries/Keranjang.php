<?php

namespace App\Libraries;

use App\Models\ProdukModel;

/**
 * Keranjang belanja berbasis session.
 * Disimpan sebagai [produk_id => qty]; harga & stok selalu dibaca ulang dari database
 * sehingga perubahan harga/stok oleh admin langsung tercermin di keranjang.
 */
class Keranjang
{
    private const KEY = 'keranjang';

    public function all(): array
    {
        return session(self::KEY) ?? [];
    }

    public function count(): int
    {
        return array_sum($this->all());
    }

    public function add(int $produkId, int $qty = 1): void
    {
        $items = $this->all();
        $items[$produkId] = ($items[$produkId] ?? 0) + max(1, $qty);
        session()->set(self::KEY, $items);
    }

    public function update(int $produkId, int $qty): void
    {
        $items = $this->all();
        if ($qty <= 0) {
            unset($items[$produkId]);
        } else {
            $items[$produkId] = $qty;
        }
        session()->set(self::KEY, $items);
    }

    public function remove(int $produkId): void
    {
        $this->update($produkId, 0);
    }

    public function clear(): void
    {
        session()->remove(self::KEY);
    }

    /**
     * Detail isi keranjang lengkap dengan data produk terbaru.
     * Produk yang sudah dihapus dibuang, jumlah yang melebihi stok diturunkan.
     *
     * @return array{items: list<array>, total: int, catatan: list<string>}
     */
    public function detail(): array
    {
        $ids = array_keys($this->all());
        if ($ids === []) {
            return ['items' => [], 'total' => 0, 'catatan' => []];
        }

        $produk = array_column((new ProdukModel())->whereIn('id', $ids)->findAll(), null, 'id');
        $items = [];
        $catatan = [];
        $total = 0;

        foreach ($this->all() as $id => $qty) {
            if (! isset($produk[$id])) {
                $this->remove($id);
                $catatan[] = 'Sebuah produk di keranjang sudah tidak tersedia dan dihapus.';
                continue;
            }
            $p = $produk[$id];
            if ($qty > $p['stok']) {
                $qty = (int) $p['stok'];
                $this->update($id, $qty);
                $catatan[] = $qty === 0
                    ? "Stok {$p['nama']} habis sehingga dihapus dari keranjang."
                    : "Jumlah {$p['nama']} disesuaikan dengan stok tersedia ({$qty}).";
                if ($qty === 0) {
                    continue;
                }
            }
            $subtotal = $qty * (int) $p['harga'];
            $total += $subtotal;
            $items[] = $p + ['qty' => $qty, 'subtotal' => $subtotal];
        }

        return compact('items', 'total', 'catatan');
    }
}
