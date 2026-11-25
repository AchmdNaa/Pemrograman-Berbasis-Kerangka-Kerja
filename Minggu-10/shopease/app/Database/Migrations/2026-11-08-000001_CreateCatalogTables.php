<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel katalog ShopEase: kategori, produk, dan pesan kontak.
 */
class CreateCatalogTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 60],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 80],
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'bi-tag'],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('categories');

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug'         => ['type' => 'VARCHAR', 'constraint' => 170],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'price'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'old_price'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'stock'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'sold'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'rating'       => ['type' => 'DECIMAL', 'constraint' => '2,1', 'default' => 0],
            'is_featured'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('category_id');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('products');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 120],
            'subject'    => ['type' => 'VARCHAR', 'constraint' => 150],
            'message'    => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('contact_messages');
    }

    public function down(): void
    {
        $this->forge->dropTable('contact_messages', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('categories', true);
    }
}
