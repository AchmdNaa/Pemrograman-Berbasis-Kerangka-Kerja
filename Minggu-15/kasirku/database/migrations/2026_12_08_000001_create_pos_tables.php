<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Peran pengguna: admin (pemilik toko) & kasir
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 10)->default('kasir')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('color', 7)->default('#2563eb');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 20)->unique();                 // kode barang / barcode
            $table->string('name', 120);
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('cost_price');                // harga beli
            $table->unsignedInteger('price');                     // harga jual
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(5);     // batas peringatan stok menipis
            $table->string('unit', 10)->default('pcs');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 20)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();   // kasir
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total');
            $table->string('payment_method', 10);                 // tunai | qris | debit
            $table->unsignedInteger('paid');
            $table->unsignedInteger('change')->default(0);
            $table->string('note', 200)->nullable();
            $table->timestamps();
            $table->index('created_at');
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name', 120);                   // salinan nama & harga saat transaksi
            $table->unsignedInteger('price');
            $table->unsignedInteger('cost_price');
            $table->unsignedInteger('qty');
            $table->unsignedInteger('subtotal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['role', 'is_active']));
    }
};
