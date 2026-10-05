<?php

use App\Models\Pemasukkan;
use App\Models\Pengeluaran;
use App\Models\SumberDanaPemasukkan;
use App\Models\SumberDanaPengeluaran;
use App\Services\FinanceDashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard totals and chart data are limited to month to date while balances remain current', function () {
    $this->travelTo(Carbon::parse('2026-05-15 12:00:00'));

    $food = SumberDanaPengeluaran::create(['nama_sumber_dana' => 'Uang Makan', 'budget' => 1000]);
    $transport = SumberDanaPengeluaran::create(['nama_sumber_dana' => 'Transportasi', 'budget' => 2000]);
    $food->created_at = Carbon::parse('2026-05-01');
    $food->saveQuietly();
    $salary = SumberDanaPemasukkan::create(['nama_sumber_dana' => 'Gaji']);

    Pengeluaran::create(['tanggal' => '2026-04-30', 'nama_pengeluaran' => 'Bulan lalu', 'jumlah' => 500, 'sumber_dana_id' => $food->id]);
    Pengeluaran::create(['tanggal' => '2026-05-03', 'nama_pengeluaran' => 'Sarapan', 'jumlah' => 100, 'sumber_dana_id' => $food->id]);
    Pengeluaran::create(['tanggal' => '2026-05-10', 'nama_pengeluaran' => 'Belanja', 'jumlah' => 200, 'sumber_dana_id' => $food->id]);
    Pengeluaran::create(['tanggal' => '2026-05-11', 'nama_pengeluaran' => 'Ongkos', 'jumlah' => 50, 'sumber_dana_id' => $transport->id]);
    Pengeluaran::create(['tanggal' => '2026-05-15', 'nama_pengeluaran' => 'Makan siang', 'jumlah' => 75, 'sumber_dana_id' => $food->id]);
    Pemasukkan::create(['tanggal' => '2026-04-30', 'nama_pemasukkan' => 'Gaji bulan lalu', 'jumlah' => 500, 'sumber_dana_id' => $salary->id]);
    Pemasukkan::create(['tanggal' => '2026-05-12', 'nama_pemasukkan' => 'Gaji', 'jumlah' => 700, 'sumber_dana_id' => $salary->id]);

    $summary = app(FinanceDashboardService::class)->getSummaryData();

    expect((float) $summary['total_pemasukkan'])->toBe(700.0)
        ->and((float) $summary['total_pengeluaran'])->toBe(425.0)
        ->and((float) $summary['total_saldo'])->toBe(2075.0)
        ->and($summary['period_label'])->toBe('May 2026')
        ->and((float) $summary['stats_all']['highest']['total'])->toBe(200.0)
        ->and((float) $summary['stats_all']['lowest']['total'])->toBe(50.0)
        ->and($summary['charts']['food_budget'])->toMatchArray([
            'budget' => 600000,
            'spent' => 375.0,
            'remaining' => 599625.0,
            'daily_budget' => 40000,
        ])
        ->and($summary['charts']['daily_expenses'][2])->toBe(['day' => 3, 'amount' => 100.0])
        ->and($summary['charts']['daily_expenses'][14])->toBe(['day' => 15, 'amount' => 75.0])
        ->and($summary['charts']['food_budget_forecast'])->toHaveCount(10);
});

test('recent transactions are restricted to the current month and the requested limit', function () {
    $this->travelTo(Carbon::parse('2026-05-15 12:00:00'));

    $source = SumberDanaPengeluaran::create(['nama_sumber_dana' => 'Uang Makan', 'budget' => 1000]);

    Pengeluaran::create(['tanggal' => '2026-04-30', 'nama_pengeluaran' => 'Bulan lalu', 'jumlah' => 10, 'sumber_dana_id' => $source->id]);
    foreach (range(1, 6) as $day) {
        Pengeluaran::create([
            'tanggal' => Carbon::create(2026, 5, $day)->toDateString(),
            'nama_pengeluaran' => 'Transaksi '.$day,
            'jumlah' => 10,
            'sumber_dana_id' => $source->id,
        ]);
    }
    Pengeluaran::create(['tanggal' => '2026-05-15', 'nama_pengeluaran' => 'Hari ini', 'jumlah' => 10, 'sumber_dana_id' => $source->id]);

    $transactions = app(FinanceDashboardService::class)->getRecentTransactions();

    expect($transactions)->toHaveCount(5)
        ->and($transactions->pluck('nama')->all())->not->toContain('Bulan lalu')
        ->and($transactions->first()['nama'])->toBe('Hari ini');
});


test('dashboard renders the mobile summary controls and month-to-date charts', function () {
    $this->travelTo(Carbon::parse('2026-05-15 12:00:00'));
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);

    $source = SumberDanaPengeluaran::create(['nama_sumber_dana' => 'Uang Makan', 'budget' => 1000]);
    Pengeluaran::create(['tanggal' => '2026-05-03', 'nama_pengeluaran' => 'Sarapan', 'jumlah' => 100, 'sumber_dana_id' => $source->id]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('aria-label="Statistik pengeluaran"', false)
        ->assertSee('bulan berjalan')
        ->assertSee('Visualisasi Keuangan')
        ->assertSee('Budget Uang Makan')
        ->assertSee('Pengeluaran harian')
        ->assertSee('Diagram donat penyerapan budget uang makan')
        ->assertSee('Rencana budget uang makan - 10 hari')
        ->assertDontSee('Arus kas per minggu')
        ->assertDontSee('Statistik Pengeluaran (Bulan Ini')
        ->assertDontSee('Statistik Pengeluaran (Uang Makan')
        ->assertSee('5 transaksi terakhir pada bulan ini.');
});


test('meal budget only carries daily overspending forward and never carries unused allowance', function () {
    $this->travelTo(Carbon::parse('2026-05-15 12:00:00'));

    $source = SumberDanaPengeluaran::create(['nama_sumber_dana' => 'Uang Makan', 'budget' => 1000]);
    $source->created_at = Carbon::parse('2026-05-01');
    $source->saveQuietly();

    Pengeluaran::create(['tanggal' => '2026-05-13', 'nama_pengeluaran' => 'Belanja hemat', 'jumlah' => 35000, 'sumber_dana_id' => $source->id]);
    Pengeluaran::create(['tanggal' => '2026-05-14', 'nama_pengeluaran' => 'Belanja berlebih', 'jumlah' => 45000, 'sumber_dana_id' => $source->id]);

    $forecast = app(FinanceDashboardService::class)->getSummaryData()['charts']['food_budget_forecast'];

    expect($forecast)->toHaveCount(10)
        ->and($forecast[0]['allowance'])->toBe(35000.0)
        ->and($forecast[0]['remaining'])->toBe(35000.0)
        ->and($forecast[1]['allowance'])->toBe(40000.0)
        ->and($forecast[2]['allowance'])->toBe(40000.0);
});

test('meal overspending today reduces tomorrow allowance and shows the deficit', function () {
    $this->travelTo(Carbon::parse('2026-05-15 12:00:00'));

    $source = SumberDanaPengeluaran::create(['nama_sumber_dana' => 'Uang Makan', 'budget' => 1000]);
    $source->created_at = Carbon::parse('2026-05-01');
    $source->saveQuietly();

    Pengeluaran::create(['tanggal' => '2026-05-15', 'nama_pengeluaran' => 'Makan siang', 'jumlah' => 45000, 'sumber_dana_id' => $source->id]);

    $forecast = app(FinanceDashboardService::class)->getSummaryData()['charts']['food_budget_forecast'];

    expect($forecast[0]['allowance'])->toBe(40000.0)
        ->and($forecast[0]['over_budget'])->toBe(5000.0)
        ->and($forecast[1]['allowance'])->toBe(35000.0)
        ->and($forecast[2]['allowance'])->toBe(40000.0);
});
