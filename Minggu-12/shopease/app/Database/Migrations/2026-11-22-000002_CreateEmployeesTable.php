<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Latihan: Model untuk menyimpan data karyawan ShopEase.
 */
class CreateEmployeesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nip'        => ['type' => 'VARCHAR', 'constraint' => 12],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'position'   => ['type' => 'VARCHAR', 'constraint' => 60],
            'department' => ['type' => 'VARCHAR', 'constraint' => 40],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 120],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'hire_date'  => ['type' => 'DATE'],
            'salary'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'aktif'],
            'photo'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('nip');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('employees');
    }

    public function down(): void
    {
        $this->forge->dropTable('employees', true);
    }
}
