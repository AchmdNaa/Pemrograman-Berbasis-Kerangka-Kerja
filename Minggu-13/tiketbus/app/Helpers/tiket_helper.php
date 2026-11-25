<?php

if (! function_exists('rupiah')) {
    function rupiah(int|float|string $v): string
    {
        return 'Rp' . number_format((float) $v, 0, ',', '.');
    }
}

if (! function_exists('tgl')) {
    /** Format tanggal Indonesia, contoh: Sab, 28 Nov 2026 · 09:30 */
    function tgl(string $datetime, bool $withTime = true, bool $withDay = true): string
    {
        $t = strtotime($datetime);
        $hari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'][(int) date('w', $t)];
        $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][(int) date('n', $t)];
        $out = ($withDay ? $hari . ', ' : '') . date('j', $t) . ' ' . $bulan . ' ' . date('Y', $t);

        return $withTime ? $out . ' · ' . date('H:i', $t) : $out;
    }
}

if (! function_exists('durasi')) {
    function durasi(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return trim(($h ? "{$h}j " : '') . ($m ? "{$m}m" : ''));
    }
}

if (! function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        $map = [
            'menunggu_pembayaran' => ['warning', 'Menunggu Pembayaran'],
            'menunggu_verifikasi' => ['info', 'Menunggu Verifikasi'],
            'lunas'               => ['success', 'Lunas'],
            'dibatalkan'          => ['secondary', 'Dibatalkan'],
            'kedaluwarsa'         => ['dark', 'Kedaluwarsa'],
            'menunggu'            => ['info', 'Menunggu'],
            'diterima'            => ['success', 'Diterima'],
            'ditolak'             => ['danger', 'Ditolak'],
            'aktif'               => ['success', 'Aktif'],
            'batal'               => ['danger', 'Batal'],
        ];
        [$color, $label] = $map[$status] ?? ['secondary', $status];

        return '<span class="badge text-bg-' . $color . '">' . $label . '</span>';
    }
}

if (! function_exists('seat_labels')) {
    /**
     * Daftar kode kursi sebuah bus: baris 1..N, kolom huruf A..D.
     * Mengembalikan array baris → array kursi (null = lorong).
     */
    function seat_layout(int $rows, int $cols): array
    {
        $letters = range('A', 'Z');
        // Posisi lorong: 4 kolom → 2|2, 3 kolom (sleeper) → 1|2
        $aisleAfter = $cols === 3 ? 1 : intdiv($cols, 2);
        $layout = [];
        for ($r = 1; $r <= $rows; $r++) {
            $line = [];
            for ($c = 0; $c < $cols; $c++) {
                if ($c === $aisleAfter) {
                    $line[] = null;
                }
                $line[] = $r . $letters[$c];
            }
            $layout[] = $line;
        }

        return $layout;
    }

    function seat_labels(int $rows, int $cols): array
    {
        return array_values(array_filter(array_merge(...seat_layout($rows, $cols))));
    }
}

if (! function_exists('auth_user')) {
    function auth_user(?string $key = null): mixed
    {
        $u = ['id' => session('user_id'), 'name' => session('user_name'), 'email' => session('user_email'), 'role' => session('user_role')];

        return $key === null ? $u : ($u[$key] ?? null);
    }

    function is_admin(): bool
    {
        return session('user_role') === 'admin';
    }

    function home_url(): string
    {
        return is_admin() ? url_to('admin.dashboard') : url_to('my.bookings');
    }
}
