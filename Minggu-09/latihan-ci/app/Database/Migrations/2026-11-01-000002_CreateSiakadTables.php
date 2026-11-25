<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel SIAKAD: mahasiswa, matakuliah, dan nilai (KRS + nilai akhir).
 */
class CreateSiakadTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nim'        => ['type' => 'VARCHAR', 'constraint' => 10],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'prodi'      => ['type' => 'VARCHAR', 'constraint' => 60],
            'angkatan'   => ['type' => 'SMALLINT', 'constraint' => 4],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'foto'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nim');
        $this->forge->createTable('mahasiswa');

        $this->forge->addField([
            'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode'     => ['type' => 'VARCHAR', 'constraint' => 10],
            'nama'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'sks'      => ['type' => 'TINYINT', 'constraint' => 1],
            'semester' => ['type' => 'TINYINT', 'constraint' => 1],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('matakuliah');

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'mahasiswa_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'matakuliah_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nilai_huruf'   => ['type' => 'VARCHAR', 'constraint' => 2],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['mahasiswa_id', 'matakuliah_id']);
        $this->forge->addForeignKey('mahasiswa_id', 'mahasiswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('matakuliah_id', 'matakuliah', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('nilai');
    }

    public function down(): void
    {
        $this->forge->dropTable('nilai', true);
        $this->forge->dropTable('matakuliah', true);
        $this->forge->dropTable('mahasiswa', true);
    }
}
