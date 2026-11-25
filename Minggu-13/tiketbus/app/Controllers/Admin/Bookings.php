<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\PassengerModel;
use App\Models\PaymentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Kelola pemesanan & verifikasi pembayaran.
 */
class Bookings extends BaseController
{
    public function index(): string
    {
        (new BookingModel())->expireStale();
        $status = (string) $this->request->getGet('status');
        $q = trim((string) $this->request->getGet('q'));
        $model = (new BookingModel())->withDetails();
        if ($status) {
            $model->where('bookings.status', $status);
        }
        if ($q) {
            $model->groupStart()->like('bookings.code', $q)->orLike('users.name', $q)->groupEnd();
        }

        return view('admin/bookings/index', [
            'title'    => 'Pemesanan',
            'bookings' => $model->orderBy('bookings.created_at', 'DESC')->paginate(15),
            'pager'    => $model->pager,
            'status'   => $status,
            'q'        => $q,
        ]);
    }

    public function show(int $id): string
    {
        $b = (new BookingModel())->withDetails()->where('bookings.id', $id)->first() ?? throw PageNotFoundException::forPageNotFound();

        return view('admin/bookings/show', [
            'title'      => 'Pemesanan ' . $b['code'],
            'b'          => $b,
            'passengers' => (new PassengerModel())->where('booking_id', $id)->findAll(),
            'payments'   => (new PaymentModel())->where('booking_id', $id)->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    /** Terima atau tolak bukti pembayaran. */
    public function verify(int $paymentId): RedirectResponse
    {
        $payments = new PaymentModel();
        $p = $payments->find($paymentId) ?? throw PageNotFoundException::forPageNotFound();
        $b = (new BookingModel())->find($p['booking_id']);
        if ($p['status'] !== 'menunggu' || $b['status'] !== 'menunggu_verifikasi') {
            return redirect()->back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        $action = (string) $this->request->getPost('action');
        $note = trim((string) $this->request->getPost('note'));
        $now = date('Y-m-d H:i:s');

        if ($action === 'terima') {
            $payments->update($paymentId, ['status' => 'diterima', 'verified_at' => $now, 'note' => $note ?: null]);
            (new BookingModel())->update($b['id'], ['status' => 'lunas', 'paid_at' => $now]);
            $msg = 'Pembayaran diterima. E-tiket sudah dapat diunduh pelanggan.';
        } elseif ($action === 'tolak') {
            if (mb_strlen($note) < 5) {
                return redirect()->back()->with('error', 'Tuliskan alasan penolakan (minimal 5 karakter).');
            }
            $payments->update($paymentId, ['status' => 'ditolak', 'verified_at' => $now, 'note' => $note]);
            // Pelanggan diberi waktu 2 jam lagi untuk mengunggah ulang bukti.
            (new BookingModel())->update($b['id'], ['status' => 'menunggu_pembayaran', 'expires_at' => date('Y-m-d H:i:s', strtotime(BookingModel::PAYMENT_WINDOW))]);
            $msg = 'Pembayaran ditolak. Pelanggan dapat mengunggah ulang bukti.';
        } else {
            return redirect()->back()->with('error', 'Aksi tidak dikenal.');
        }

        return redirect()->to(url_to('admin.bookings.show', $b['id']))->with('success', $msg);
    }
}
