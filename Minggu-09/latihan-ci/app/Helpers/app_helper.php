<?php

/**
 * Helper umum aplikasi (dimuat otomatis lewat BaseController).
 */

if (! function_exists('rupiah')) {
    function rupiah(int|float|string $angka): string
    {
        return 'Rp' . number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('placeholder_img')) {
    /** Gambar pengganti SVG (data URI) berisi inisial, dengan latar pastel. */
    function placeholder_img(string $teks, string $warna = '#4f46e5', string $latar = '#eef2ff'): string
    {
        $kata = preg_split('/\s+/', trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $teks))) ?: ['?'];
        $inisial = strtoupper(implode('', array_map(static fn ($w) => mb_substr($w, 0, 1), array_slice(array_filter($kata), 0, 2))));
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">'
             . '<rect width="400" height="300" fill="' . $latar . '"/>'
             . '<circle cx="340" cy="40" r="90" fill="' . $warna . '" fill-opacity=".08"/>'
             . '<circle cx="40" cy="280" r="70" fill="' . $warna . '" fill-opacity=".08"/>'
             . '<text x="50%" y="54%" font-family="Arial,sans-serif" font-size="88" font-weight="700" fill="' . $warna . '" text-anchor="middle" dominant-baseline="middle">'
             . esc($inisial ?: '?') . '</text></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

if (! function_exists('produk_img')) {
    function produk_img(array $produk): string
    {
        if (! empty($produk['gambar'])) {
            return base_url('uploads/produk/' . $produk['gambar']);
        }
        $palet = [['#4338ca', '#eef2ff'], ['#0e7490', '#ecfeff'], ['#047857', '#ecfdf5'], ['#b45309', '#fffbeb'], ['#be185d', '#fdf2f8'], ['#6d28d9', '#f5f3ff']];
        [$warna, $latar] = $palet[crc32($produk['nama']) % count($palet)];

        return placeholder_img($produk['nama'], $warna, $latar);
    }
}

if (! function_exists('foto_mhs')) {
    function foto_mhs(array $mhs): string
    {
        return ! empty($mhs['foto'])
            ? base_url('uploads/mahasiswa/' . $mhs['foto'])
            : placeholder_img($mhs['nama'], '#334155', '#e2e8f0');
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool
    {
        return session('role') === 'admin';
    }
}
