<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel Toko Online: kategori, produk, pesanan, dan item pesanan.
 */
class CreateTokoTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 60],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 80],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('kategori');

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kategori_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 140],
            'deskripsi'   => ['type' => 'TEXT', 'null' => true],
            'harga'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'stok'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'gambar'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('kategori_id', 'kategori', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('produk');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode'         => ['type' => 'VARCHAR', 'constraint' => 20],
            'nama_pembeli' => ['type' => 'VARCHAR', 'constraint' => 100],
            'telepon'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'alamat'       => ['type' => 'TEXT'],
            'total'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'baru'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('pesanan');

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pesanan_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'produk_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 120],
            'harga'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'subtotal'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('pesanan_item');
    }

    public function down(): void
    {
        $this->forge->dropTable('pesanan_item', true);
        $this->forge->dropTable('pesanan', true);
        $this->forge->dropTable('produk', true);
        $this->forge->dropTable('kategori', true);
    }
}
