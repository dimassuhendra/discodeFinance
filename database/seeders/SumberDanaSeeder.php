<?php

namespace Database\Seeders;

use App\Models\SumberDanaPengeluaran;
use Illuminate\Database\Seeder;

class SumberDanaSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['nama_sumber_dana' => 'Uang Makan', 'budget' => 0, 'keterangan' => 'Alokasi kebutuhan makan harian'],
            ['nama_sumber_dana' => 'Uang Bulanan', 'budget' => 0, 'keterangan' => 'Kebutuhan rutin dan perlengkapan harian'],
            ['nama_sumber_dana' => 'Uang Investasi', 'budget' => 0, 'keterangan' => 'Alokasi portofolio investasi'],
        ];

        foreach ($defaults as $data) {
            SumberDanaPengeluaran::firstOrCreate(['nama_sumber_dana' => $data['nama_sumber_dana']], $data);
        }
    }
}