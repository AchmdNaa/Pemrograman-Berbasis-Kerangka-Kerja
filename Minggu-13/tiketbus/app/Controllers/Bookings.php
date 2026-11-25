<?php

namespace App\Controllers;

use App\Models\BankAccountModel;
use App\Models\BookingModel;
use App\Models\PassengerModel;
use App\Models\PaymentModel;
use App\Models\ScheduleModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Panel pelanggan: memesan kursi, membayar, membatalkan, dan mencetak e-tiket.
 */
class Bookings extends BaseController
{
    private const MAX_SEATS = 6;

    /** POST /jadwal/{id}/pesan — membuat pemesanan dari kursi yang dicentang. */
    public function create(int $scheduleId): RedirectResponse
    {
        $bookings = new BookingModel();
        $bookings->expireStale();

        $s = (new ScheduleModel())->findDetail($scheduleId) ?? throw PageNotFoundException::forPageNotFound();
        if ($s['status'] !== 'aktif' || strtotime($s['depart_at']) <= time()) {
            return redirect()->back()->with('error', 'Jadwal ini sudah tidak bisa dipesan.');
        }

        $seats = array_values(array_unique((array) $this->request->getPost('seats')));
        $passengers = (array) $this->request->getPost('passengers');
        $bankId = (int) $this->request->getPost('bank_account_id');

        // ---- Validasi
        $errors = [];
        $valid = seat_labels((int) $s['seat_rows'], (int) $s['seat_cols']);
        if ($seats === []) {
            $errors[] = 'Pilih minimal satu kursi.';
        } elseif (count($seats) > self::MAX_SEATS) {
            $errors[] = 'Maksimal ' . self::MAX_SEATS . ' kursi per pemesanan.';
        }
        foreach ($seats as $seat) {
            if (! in_array($seat, $valid, true)) {
                $errors[] = "Kursi {$seat} tidak ada pada bus ini.";
                continue;
            }
            $p = $passengers[$seat] ?? [];
            if (mb_strlen(trim((string) ($p['name'] ?? ''))) < 3) {
                $errors[] = "Nama penumpang kursi {$seat} minimal 3 karakter.";
            }
            if (! preg_match('/^\d{16}$/', (string) ($p['id_number'] ?? ''))) {
                $errors[] = "NIK penumpang kursi {$seat} harus 16 digit angka.";
            }
            if (($p['phone'] ?? '') !== '' && ! preg_match('/^(08|\+628)\d{7,11}$/', (string) $p['phone'])) {
                $errors[] = "Nomor HP penumpang kursi {$seat} tidak valid.";
            }
        }
        if (! (new BankAccountModel())->where(['id' => $bankId, 'is_active' => 1])->first()) {
            $errors[] = 'Pilih metode pembayaran (rekening tujuan).';
        }
        if ($errors) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // ---- Simpan di dalam transaksi + cek ulang ketersediaan kursi
        $db = db_connect();
        $db->transStart();

        $taken = array_intersect($seats, $bookings->bookedSeats($scheduleId));
        if ($taken !== []) {
            $db->transRollback();

            return redirect()->back()->withInput()
                ->with('error', 'Kursi ' . implode(', ', $taken) . ' baru saja dipesan orang lain. Silakan pilih kursi lain.');
        }

        $code = BookingModel::newCode();
        $bookingId = $bookings->insert([
            'code'            => $code,
            'user_id'         => auth_user('id'),
            'schedule_id'     => $scheduleId,
            'bank_account_id' => $bankId,
            'total'           => count($seats) * (int) $s['price'],
            'status'          => 'menunggu_pembayaran',
            'expires_at'      => date('Y-m-d H:i:s', strtotime(BookingModel::PAYMENT_WINDOW)),
        ]);
        $pm = new PassengerModel();
        foreach ($seats as $seat) {
            $p = $passengers[$seat];
            $pm->insert([
                'booking_id' => $bookingId,
                'seat_no'    => $seat,
                'name'       => trim($p['name']),
                'id_number'  => $p['id_number'],
                'phone'      => trim((string) ($p['phone'] ?? '')) ?: null,
            ]);
        }
        $db->transComplete();

        return redirect()->to(url_to('booking.show', $code))
            ->with('success', 'Pemesanan berhasil dibuat. Selesaikan pembayaran sebelum batas waktu.');
    }

    /** GET /pesanan — riwayat pemesanan milik pelanggan. */
    public function index(): string
    {
        (new BookingModel())->expireStale();

        return view('bookings/index', [
            'title'    => 'Pesanan Saya',
            'bookings' => (new BookingModel())->withDetails()->where('bookings.user_id', auth_user('id'))
                ->orderBy('bookings.created_at', 'DESC')->findAll(),
        ]);
    }

    /** GET /pesanan/{kode} — detail + instruksi & form pembayaran. */
    public function show(string $code): string
    {
        (new BookingModel())->expireStale();
        $b = $this->ownBooking($code);

        return view('bookings/show', [
            'title'      => 'Pesanan ' . $b['code'],
            'b'          => $b,
            'passengers' => (new PassengerModel())->where('booking_id', $b['id'])->findAll(),
            'payments'   => (new PaymentModel())->where('booking_id', $b['id'])->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    /** POST /pesanan/{kode}/bayar — upload bukti transfer. */
    public function pay(string $code): RedirectResponse
    {
        (new BookingModel())->expireStale();
        $b = $this->ownBooking($code);
        if ($b['status'] !== 'menunggu_pembayaran') {
            return redirect()->back()->with('error', 'Pesanan ini tidak sedang menunggu pembayaran.');
        }

        $rules = [
            'sender_name'    => ['label' => 'Nama pengirim', 'rules' => 'required|min_length[3]|max_length[100]'],
            'sender_bank'    => ['label' => 'Bank pengirim', 'rules' => 'required|max_length[40]'],
            'sender_account' => ['label' => 'Nomor rekening pengirim', 'rules' => 'required|numeric|min_length[8]|max_length[30]'],
            'amount'         => ['label' => 'Jumlah transfer', 'rules' => "required|is_natural_no_zero|greater_than_equal_to[{$b['total']}]", 'errors' => ['greater_than_equal_to' => 'Jumlah transfer kurang dari total tagihan ' . rupiah($b['total']) . '.']],
            'proof'          => ['label' => 'Bukti transfer', 'rules' => 'uploaded[proof]|max_size[proof,2048]|ext_in[proof,jpg,jpeg,png,pdf]|mime_in[proof,image/jpeg,image/png,application/pdf]'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Bukti bayar disimpan di writable/ (tidak bisa diakses publik), ditampilkan lewat controller.
        $file = $this->request->getFile('proof');
        $name = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/payments', $name);

        (new PaymentModel())->insert([
            'booking_id'     => $b['id'],
            'sender_name'    => trim((string) $this->request->getPost('sender_name')),
            'sender_bank'    => trim((string) $this->request->getPost('sender_bank')),
            'sender_account' => trim((string) $this->request->getPost('sender_account')),
            'amount'         => (int) $this->request->getPost('amount'),
            'proof'          => $name,
            'status'         => 'menunggu',
        ]);
        (new BookingModel())->update($b['id'], ['status' => 'menunggu_verifikasi']);

        return redirect()->to(url_to('booking.show', $b['code']))->with('success', 'Bukti pembayaran terkirim. Admin akan memverifikasi dalam 1×24 jam.');
    }

    /** POST /pesanan/{kode}/batal — hanya sebelum pembayaran diverifikasi. */
    public function cancel(string $code): RedirectResponse
    {
        $b = $this->ownBooking($code);
        if (! in_array($b['status'], ['menunggu_pembayaran', 'menunggu_verifikasi'], true)) {
            return redirect()->back()->with('error', 'Pesanan dengan status ini tidak dapat dibatalkan.');
        }
        (new BookingModel())->update($b['id'], ['status' => 'dibatalkan']);
        (new PaymentModel())->where('booking_id', $b['id'])->where('status', 'menunggu')->set(['status' => 'ditolak', 'note' => 'Dibatalkan pelanggan'])->update();

        return redirect()->to(url_to('booking.show', $b['code']))->with('success', 'Pesanan dibatalkan dan kursi dilepas.');
    }

    /** GET /pesanan/{kode}/tiket — e-tiket (cetak / simpan PDF), hanya bila lunas. */
    public function ticket(string $code): string|RedirectResponse
    {
        $b = $this->ownBooking($code, allowAdmin: true);
        if ($b['status'] !== 'lunas') {
            return redirect()->to(url_to('booking.show', $b['code']))->with('error', 'E-tiket tersedia setelah pembayaran diverifikasi.');
        }

        return view('bookings/ticket', [
            'title'      => 'E-Tiket ' . $b['code'],
            'b'          => $b,
            'passengers' => (new PassengerModel())->where('booking_id', $b['id'])->findAll(),
        ]);
    }

    /** GET /bukti/{paymentId} — menampilkan file bukti transfer (pemilik & admin saja). */
    public function proof(int $paymentId)
    {
        $p = (new PaymentModel())->find($paymentId) ?? throw PageNotFoundException::forPageNotFound();
        $b = (new BookingModel())->find($p['booking_id']);
        if (! is_admin() && (int) $b['user_id'] !== (int) auth_user('id')) {
            throw PageNotFoundException::forPageNotFound();
        }
        $path = WRITEPATH . 'uploads/payments/' . $p['proof'];
        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->setHeader('Content-Type', mime_content_type($path) ?: 'application/octet-stream')
            ->setHeader('Content-Disposition', 'inline; filename="' . $p['proof'] . '"')
            ->setBody(file_get_contents($path));
    }

    // ------------------------------------------------------------ Profil

    public function profile(): string
    {
        return view('bookings/profile', ['title' => 'Profil', 'user' => (new UserModel())->find(auth_user('id'))]);
    }

    public function updateProfile(): RedirectResponse
    {
        $id = (int) auth_user('id');
        $rules = [
            'name'  => UserModel::RULES['name'],
            'phone' => UserModel::RULES['phone'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        (new UserModel())->update($id, ['name' => trim((string) $this->request->getPost('name')), 'phone' => trim((string) $this->request->getPost('phone'))]);
        session()->set('user_name', trim((string) $this->request->getPost('name')));

        return redirect()->back()->with('success', 'Profil diperbarui.');
    }

    public function changePassword(): RedirectResponse
    {
        $user = (new UserModel())->find(auth_user('id'));
        $data = $this->request->getPost(['current_password', 'password', 'password_confirm']);
        $rules = ['current_password' => 'required', 'password' => UserModel::RULES['password'], 'password_confirm' => UserModel::RULES['password_confirm']];
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
        if (! password_verify((string) $data['current_password'], $user['password_hash'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }
        (new UserModel())->update($user['id'], ['password_hash' => password_hash((string) $data['password'], PASSWORD_DEFAULT)]);
        session()->regenerate(true);

        return redirect()->back()->with('success', 'Password berhasil diganti.');
    }

    private function ownBooking(string $code, bool $allowAdmin = false): array
    {
        $b = (new BookingModel())->findByCode($code);
        if ($b === null || ((int) $b['user_id'] !== (int) auth_user('id') && ! ($allowAdmin && is_admin()))) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        return $b;
    }
}
