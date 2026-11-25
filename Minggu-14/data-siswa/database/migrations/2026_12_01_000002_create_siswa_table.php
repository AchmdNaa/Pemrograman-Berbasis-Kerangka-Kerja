<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 10)->unique();
            $table->string('nama', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 60);
            $table->date('tanggal_lahir');
            $table->foreignId('kelas_id')->constrained('kelas')->restrictOnDelete();
            $table->string('email', 120)->nullable()->unique();
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat');
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
