<?php

use App\Livewire\Approval\LpjShow;
use App\Livewire\Approval\Show;
use App\Livewire\Dashboard\Index;
use App\Livewire\Sekretaris\Notulensi\Form;
use App\Livewire\Sekretaris\Pengajuan\Create;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', Index::class)
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | SUPERADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:superadmin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/ormawa', App\Livewire\Admin\Ormawa\Index::class)
            ->name('ormawa');

        Route::get('/program-studi', App\Livewire\Admin\ProgramStudi\Index::class)
            ->name('program-studi');

        Route::get('/periode', App\Livewire\Admin\Periode\Index::class)
            ->name('periode');

        Route::get('/pengguna', App\Livewire\Admin\Pengguna\Index::class)
            ->name('pengguna');

        Route::get('/template-dokumen', App\Livewire\Admin\TemplateDokumen\Index::class)
            ->name('template-dokumen');
    });

    /*
    |--------------------------------------------------------------------------
    | SEKRETARIS
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:sekretaris_ormawa')
        ->prefix('sekretaris')
        ->name('sekretaris.')
        ->group(function () {

            Route::get('/anggota', App\Livewire\Sekretaris\Anggota\Index::class)
                ->name('anggota');

            Route::get('/pengajuan', App\Livewire\Sekretaris\Pengajuan\Index::class)
                ->name('pengajuan');

            Route::get('/pengajuan/buat', Create::class)
                ->name('pengajuan.create');

            Route::get('/pengajuan/{submission}/edit', Create::class)
                ->name('pengajuan.edit');

            Route::get('/pengajuan/{submission}/revisi', Create::class)
                ->name('pengajuan.revisi');

            Route::get('/notulensi', App\Livewire\Sekretaris\Notulensi\Index::class)
                ->name('notulensi');

            Route::get('/notulensi/buat', Form::class)
                ->name('notulensi.create');

            Route::get('/notulensi/{meeting}/edit', Form::class)
                ->name('notulensi.edit');

            Route::get('/kas', App\Livewire\Sekretaris\Kas\Index::class)
                ->name('kas');

            Route::get('/kas/tambah', App\Livewire\Sekretaris\Kas\Form::class)
                ->name('kas.create');

            Route::get('/kas/{transaction}/edit', App\Livewire\Sekretaris\Kas\Form::class)
                ->name('kas.edit');

            Route::get('/lpj', App\Livewire\Sekretaris\LPJ\Index::class)
                ->name('lpj');

            Route::get('/lpj/buat', App\Livewire\Sekretaris\LPJ\Form::class)
                ->name('lpj.create');

            Route::get('/lpj/{lpj}/edit', App\Livewire\Sekretaris\LPJ\Form::class)
                ->name('lpj.edit');
        });

    /*
    |--------------------------------------------------------------------------
    | APPROVAL
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:pembina_ormawa,kaprodi,kabag_kemahasiswaan,warek'
    )
        ->prefix('approval')
        ->name('approval.')
        ->group(function () {

            Route::get('/', App\Livewire\Approval\Index::class)
                ->name('index');

            Route::get('/{submission}', Show::class)
                ->name('show');

            Route::get('/lpj/{lpj}', LpjShow::class)
                ->name('lpj.show');

        });

    /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    */

    Route::get('/laporan', App\Livewire\Laporan\Index::class)
        ->name('laporan');
});

require __DIR__.'/auth.php';
