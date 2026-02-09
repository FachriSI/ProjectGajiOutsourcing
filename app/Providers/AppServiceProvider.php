<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Paginator::useBootstrapFive();

        // Register Global Observer for Master Data
        \App\Models\Area::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Bidang::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Departemen::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Fungsi::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Harianshift::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Jabatan::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Karyawan::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Kuotajam::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Lokasi::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Masakerja::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Pakaian::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Paket::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Penempatan::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Penyesuaian::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Perusahaan::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Resiko::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Ump::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\UnitKerja::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\NilaiKontrak::observe(\App\Observers\GlobalActivityObserver::class);
        \App\Models\Lebaran::observe(\App\Observers\GlobalActivityObserver::class);
    }
}
