<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 20)->unique();          // contoh: X IPA 1
            $table->unsignedTinyInteger('tingkat');         // 10, 11, 12
            $table->string('jurusan', 20);                  // IPA, IPS, Bahasa
            $table->string('wali_kelas', 100);
            $table->unsignedTinyInteger('kapasitas')->default(36);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
