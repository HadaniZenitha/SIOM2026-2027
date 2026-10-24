<?php

use App\Livewire\Admin\Pengguna\Index;
use Livewire\Livewire;

test('tambah pengguna membuka form modal', function () {
    Livewire::test(Index::class)
        ->assertSet('showModal', false)
        ->call('create')
        ->assertSet('showModal', true)
        ->assertSee('Buat akun pengguna baru untuk SIOM.');
});
