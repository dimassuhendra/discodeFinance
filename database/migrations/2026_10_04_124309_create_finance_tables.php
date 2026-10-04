<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sumber Dana Pengeluaran
        Schema::create('sumber_dana_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sumber_dana'); // Uang Makan, Uang Bulanan, Uang Investasi
            $table->decimal('budget', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Sumber Dana Pemasukkan
        Schema::create('sumber_dana_pemasukkan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sumber_dana');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Pengeluaran
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_pengeluaran');
            $table->decimal('jumlah', 15, 2);
            $table->foreignId('sumber_dana_id')
                  ->constrained('sumber_dana_pengeluaran')
                  ->cascadeOnDelete();
            $table->timestamps();
        });

        // 4. Tabel Pemasukkan
        Schema::create('pemasukkan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_pemasukkan');
            $table->decimal('jumlah', 15, 2);
            $table->foreignId('sumber_dana_id')
                  ->constrained('sumber_dana_pemasukkan')
                  ->cascadeOnDelete();
            $table->timestamps();
        });

        // 5. Tabel Mutasi Saldo
        Schema::create('mutasi_saldo', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('dari_sumber_dana_id')
                  ->constrained('sumber_dana_pengeluaran')
                  ->cascadeOnDelete();
            $table->foreignId('ke_sumber_dana_id')
                  ->constrained('sumber_dana_pengeluaran')
                  ->cascadeOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 6. Pencatatan Investasi (Khusus Uang Investasi / Manual)
        Schema::create('pencatatan_investasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengeluaran')
                  ->nullable()
                  ->constrained('pengeluaran')
                  ->nullOnDelete();
            
            // Kolom Manual (Diisi jika id_pengeluaran NULL)
            $table->decimal('modal_awal_manual', 15, 2)->nullable();
            $table->date('tanggal_manual')->nullable();
            $table->string('sumber_dana_manual')->nullable();

            // Detail Tambahan
            $table->string('jenis_investasi'); // Saham, Crypto, Reksa Dana, Emas
            $table->string('platform');        // Bibit, Ajaib, Binance, dll.
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 7. Pencatatan Barang Pengeluaran (Khusus Uang Bulanan / Manual)
        Schema::create('pencatatan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengeluaran')
                  ->nullable()
                  ->constrained('pengeluaran')
                  ->nullOnDelete();

            // Kolom Manual (Diisi jika id_pengeluaran NULL)
            $table->string('nama_barang_manual')->nullable();
            $table->date('tanggal_pembelian_manual')->nullable();
            $table->decimal('harga_satuan_manual', 15, 2)->nullable();

            // Detail Tambahan
            $table->string('tempat_beli')->nullable();
            $table->string('kategori_barang')->nullable(); // Pakaian, Buku, Elektronik, dll.
            $table->enum('tipe_barang', ['barang_rutin', 'barang_mati', 'barang_hidup']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencatatan_barang');
        Schema::dropIfExists('pencatatan_investasi');
        Schema::dropIfExists('mutasi_saldo');
        Schema::dropIfExists('pemasukkan');
        Schema::dropIfExists('pengeluaran');
        Schema::dropIfExists('sumber_dana_pemasukkan');
        Schema::dropIfExists('sumber_dana_pengeluaran');
    }
};