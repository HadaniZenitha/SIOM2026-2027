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

    /*
    |--------------------------------------------------------------------------
    | PREVIEW DOKUMEN PROPOSAL & LPJ
    |--------------------------------------------------------------------------
    */
    Route::prefix('dokumen/preview')->name('documents.preview.')->group(function () {
        // Stream binary file inline (untuk docx-preview di browser & iframe PDF)
        Route::get('/proposal/{submission}/file', [App\Http\Controllers\DocumentPreviewController::class, 'streamProposal'])
            ->name('proposal.file');
        Route::get('/nota-dinas/{submission}/file', [App\Http\Controllers\DocumentPreviewController::class, 'streamNotaDinas'])
            ->name('nota-dinas.file');
        Route::get('/signed-proposal/{submission}/file', [App\Http\Controllers\DocumentPreviewController::class, 'streamSignedProposal'])
            ->name('signed-proposal.file');
        Route::get('/lpj/{lpj}/file', [App\Http\Controllers\DocumentPreviewController::class, 'streamLpj'])
            ->name('lpj.file');
        Route::get('/signed-lpj/{lpj}/file', [App\Http\Controllers\DocumentPreviewController::class, 'streamSignedLpj'])
            ->name('signed-lpj.file');
        Route::get('/attachment/{document}/file', [App\Http\Controllers\DocumentPreviewController::class, 'streamAttachment'])
            ->name('attachment.file');

        // Halaman penuh dokumen (Dedicated Standalone Preview Page)
        Route::get('/proposal/{submission}', [App\Http\Controllers\DocumentPreviewController::class, 'viewProposal'])
            ->name('proposal');
        Route::get('/nota-dinas/{submission}', [App\Http\Controllers\DocumentPreviewController::class, 'viewNotaDinas'])
            ->name('nota-dinas');
        Route::get('/signed-proposal/{submission}', [App\Http\Controllers\DocumentPreviewController::class, 'viewSignedProposal'])
            ->name('signed-proposal');
        Route::get('/lpj/{lpj}', [App\Http\Controllers\DocumentPreviewController::class, 'viewLpj'])
            ->name('lpj');
        Route::get('/signed-lpj/{lpj}', [App\Http\Controllers\DocumentPreviewController::class, 'viewSignedLpj'])
            ->name('signed-lpj');
    });
});

require __DIR__.'/auth.php';
