<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Skema Aplikasi Pemesanan Tiket Bus.
 *
 * users ─┬─< bookings >── schedules >── buses
 *        │      │  └─< booking_passengers        └── routes
 *        │      └─< payments >── bank_accounts
 */
class CreateTiketTables extends Migration
{
    private function id(): array
    {
        return ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true];
    }

    private function fk(bool $null = false): array
    {
        return ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => $null];
    }

    private function ts(): array
    {
        return [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];
    }

    public function up(): void
    {
        // Pengguna: pelanggan & admin
        $this->forge->addField(['id' => $this->id(),
            'name'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 120],
            'phone'         => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pelanggan'],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id')->addUniqueKey('email')->createTable('users');

        // Armada bus
        $this->forge->addField(['id' => $this->id(),
            'code'       => ['type' => 'VARCHAR', 'constraint' => 15],
            'operator'   => ['type' => 'VARCHAR', 'constraint' => 60],
            'class'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'seat_rows'  => ['type' => 'TINYINT', 'constraint' => 2],
            'seat_cols'  => ['type' => 'TINYINT', 'constraint' => 1],
            'facilities' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id')->addUniqueKey('code')->createTable('buses');

        // Rute
        $this->forge->addField(['id' => $this->id(),
            'origin'       => ['type' => 'VARCHAR', 'constraint' => 60],
            'destination'  => ['type' => 'VARCHAR', 'constraint' => 60],
            'distance_km'  => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
            'duration_min' => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id')->addUniqueKey(['origin', 'destination'])->createTable('routes');

        // Jadwal keberangkatan
        $this->forge->addField(['id' => $this->id(),
            'bus_id'    => $this->fk(),
            'route_id'  => $this->fk(),
            'depart_at' => ['type' => 'DATETIME'],
            'arrive_at' => ['type' => 'DATETIME'],
            'price'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status'    => ['type' => 'VARCHAR', 'constraint' => 15, 'default' => 'aktif'],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id')->addKey('depart_at');
        $this->forge->addForeignKey('bus_id', 'buses', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('route_id', 'routes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('schedules');

        // Rekening tujuan transfer
        $this->forge->addField(['id' => $this->id(),
            'bank'         => ['type' => 'VARCHAR', 'constraint' => 40],
            'account_no'   => ['type' => 'VARCHAR', 'constraint' => 30],
            'account_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id')->createTable('bank_accounts');

        // Pemesanan
        $this->forge->addField(['id' => $this->id(),
            'code'            => ['type' => 'VARCHAR', 'constraint' => 20],
            'user_id'         => $this->fk(),
            'schedule_id'     => $this->fk(),
            'bank_account_id' => $this->fk(true),
            'total'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 25, 'default' => 'menunggu_pembayaran'],
            'expires_at'      => ['type' => 'DATETIME'],
            'paid_at'         => ['type' => 'DATETIME', 'null' => true],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id')->addUniqueKey('code')->addKey(['schedule_id', 'status']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('schedule_id', 'schedules', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('bank_account_id', 'bank_accounts', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('bookings');

        // Penumpang per kursi
        $this->forge->addField(['id' => $this->id(),
            'booking_id' => $this->fk(),
            'seat_no'    => ['type' => 'VARCHAR', 'constraint' => 4],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'id_number'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('booking_passengers');

        // Konfirmasi pembayaran
        $this->forge->addField(['id' => $this->id(),
            'booking_id'  => $this->fk(),
            'sender_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'sender_bank' => ['type' => 'VARCHAR', 'constraint' => 40],
            'sender_account' => ['type' => 'VARCHAR', 'constraint' => 30],
            'amount'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'proof'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 15, 'default' => 'menunggu'],
            'note'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'verified_at' => ['type' => 'DATETIME', 'null' => true],
        ] + $this->ts());
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('booking_id', 'bookings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments');
    }

    public function down(): void
    {
        foreach (['payments', 'booking_passengers', 'bookings', 'bank_accounts', 'schedules', 'routes', 'buses', 'users'] as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
