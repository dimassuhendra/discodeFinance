<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_investasi', function (Blueprint $table) {
            $table->id();

            // Relasi opsional ke pengeluaran
            $table->foreignId('id_pengeluaran')
                ->nullable()
                ->constrained('pengeluaran')
                ->nullOnDelete();

            // Info Instrumen
            $table->string('nama_instrumen');
            $table->string('jenis_instrumen')->nullable();
            $table->string('platform')->nullable();

            // Unit / Quantities
            $table->decimal('jumlah_unit', 18, 4)->nullable();

            // Pembelian (Harga/Total Opsional tergantung kasus)
            $table->decimal('harga_beli_satuan', 15, 2)->nullable();
            $table->decimal('total_beli', 15, 2)->nullable();
            $table->date('tanggal_beli');

            // Penjualan
            $table->decimal('harga_jual_satuan', 15, 2)->nullable();
            $table->decimal('total_jual', 15, 2)->nullable();
            $table->date('tanggal_jual')->nullable();

            // Catatan Evaluasi Strategi
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_investasi');
    }
};