<?php

if (! function_exists('rupiah')) {
    function rupiah(int|float|string $value): string
    {
        return 'Rp' . number_format((float) $value, 0, ',', '.');
    }
}

if (! function_exists('discount_percent')) {
    function discount_percent(array $p): ?int
    {
        if (empty($p['old_price']) || $p['old_price'] <= $p['price']) {
            return null;
        }

        return (int) round(100 - ($p['price'] / $p['old_price'] * 100));
    }
}

if (! function_exists('product_image')) {
    /**
     * Ilustrasi produk berupa SVG (data URI) dengan ikon kategori,
     * sehingga katalog tetap rapi tanpa perlu file gambar.
     */
    function product_image(array $p): string
    {
        // Gambar hasil upload admin (Minggu 12) diprioritaskan.
        if (! empty($p['image']) && is_file(FCPATH . 'uploads/products/' . $p['image'])) {
            return base_url('uploads/products/' . $p['image']);
        }

        $palettes = [
            'elektronik'      => ['#1e3a8a', '#dbeafe', 'M60 70h80v50H60z M50 125h100'],
            'fashion'         => ['#9d174d', '#fce7f3', 'M70 60l30-15 30 15 15 25-20 5v45H75V90l-20-5z'],
            'rumah-tangga'    => ['#065f46', '#d1fae5', 'M55 95l45-40 45 40v45H55z M88 140v-25h24v25'],
            'olahraga'        => ['#9a3412', '#ffedd5', 'M100 50a45 45 0 1 0 0.1 0z M55 95h90 M100 50v90'],
            'buku-alat-tulis' => ['#5b21b6', '#ede9fe', 'M60 55h55a10 10 0 0 1 10 10v75H70a10 10 0 0 1-10-10z M125 65h15v75h-15'],
        ];
        [$fg, $bg, $path] = $palettes[$p['category_slug'] ?? ''] ?? ['#334155', '#e2e8f0', 'M60 60h80v80H60z'];
        $name = esc(mb_strimwidth($p['name'], 0, 26, '…'));

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="400" height="400">'
            . '<rect width="200" height="200" fill="' . $bg . '"/>'
            . '<circle cx="170" cy="30" r="55" fill="' . $fg . '" fill-opacity=".07"/>'
            . '<path d="' . $path . '" fill="none" stroke="' . $fg . '" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" transform="translate(0,-10)"/>'
            . '<text x="100" y="176" font-family="Arial,sans-serif" font-size="11" font-weight="700" fill="' . $fg . '" text-anchor="middle">' . $name . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

if (! function_exists('stars')) {
    function stars(float|string $rating): string
    {
        $r = (float) $rating;
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= '<i class="bi ' . ($r >= $i ? 'bi-star-fill' : ($r >= $i - 0.5 ? 'bi-star-half' : 'bi-star')) . '"></i>';
        }

        return '<span class="text-warning small">' . $html . '</span>';
    }
}

// ---------------------------------------------------------------------
// Helper autentikasi
// ---------------------------------------------------------------------

if (! function_exists('auth_check')) {
    function auth_check(): bool
    {
        return (bool) session('user_id');
    }
}

if (! function_exists('auth_user')) {
    /** Data ringkas pengguna yang sedang login (disimpan di session saat login). */
    function auth_user(?string $key = null): mixed
    {
        $user = [
            'id'    => session('user_id'),
            'name'  => session('user_name'),
            'email' => session('user_email'),
            'role'  => session('user_role'),
        ];

        return $key === null ? $user : ($user[$key] ?? null);
    }
}

if (! function_exists('dashboard_url')) {
    /** Setiap peran punya halaman awal sendiri setelah login. */
    function dashboard_url(?string $role = null): string
    {
        return match ($role ?? session('user_role')) {
            'admin'    => url_to('admin.dashboard'),
            'operator' => url_to('operator.dashboard'),
            default    => url_to('account'),
        };
    }
}

if (! function_exists('role_badge')) {
    function role_badge(string $role): string
    {
        $map = ['admin' => 'danger', 'operator' => 'info', 'customer' => 'success'];

        return '<span class="badge text-bg-' . ($map[$role] ?? 'secondary') . '">' . esc(ucfirst($role)) . '</span>';
    }
}
