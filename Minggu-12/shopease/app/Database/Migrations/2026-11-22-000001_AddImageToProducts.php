<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Product Management: produk kini bisa memiliki gambar hasil upload.
 */
class AddImageToProducts extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', [
            'image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'description'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', 'image');
    }
}
