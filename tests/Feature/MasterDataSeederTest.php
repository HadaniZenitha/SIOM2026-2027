<?php

use App\Models\Ormawa;
use App\Models\ProgramStudi;
use Database\Seeders\MasterDataSeeder;

it('seeds each ormawa and program studi and remains idempotent when run repeatedly', function () {
    $seeder = app(MasterDataSeeder::class);

    $seeder->run();
    $seeder->run();

    expect(Ormawa::query()->orderBy('code')->pluck('code')->all())
        ->toBe([
            'BEM',
            'DENSTISTRY',
            'DPM',
            'HIMADAN',
            'HIMAFAR',
            'HIMAFIS',
            'HIMAKEP',
            'HIMAKU',
            'HIMANERS',
            'HIMARMIK',
            'HIMATIF',
            'HIMKANES',
            'HIMS1FAR',
            'UKM_AMICI_ET_FIDEI',
            'UKM_GITA_CAHYA',
            'UKM_KSR_PMI',
            'UKM_LKIM',
            'UKM_MODELLING',
            'UKM_MUSIK_BAND',
            'UKM_ROHIS_BANJARI',
            'UKM_TAEKWONDO',
            'UKM_TARI_SPECTRA',
            'UKM_TEATER_SARAF',
            'UKM_TENIS_MEJA',
        ])
        ->and(Ormawa::query()->count())->toBe(24)
        ->and(ProgramStudi::query()->count())->toBe(11);

    // Pastikan seluruh 11 HIMA bertipe hima dan terhubung dengan Program Studi masing-masing
    $himas = Ormawa::query()->where('type', 'hima')->with('programStudi')->get();
    expect($himas)->toHaveCount(11);

    foreach ($himas as $hima) {
        expect($hima->isHima())->toBeTrue()
            ->and($hima->isUkm())->toBeFalse()
            ->and($hima->isBem())->toBeFalse()
            ->and($hima->isDpm())->toBeFalse()
            ->and($hima->leadershipTitle())->toBe('Ketua Himpunan')
            ->and($hima->program_studi_id)->not->toBeNull()
            ->and($hima->programStudi)->not->toBeNull();
    }

    // Pastikan seluruh 11 UKM bertipe ukm
    $ukms = Ormawa::query()->where('type', 'ukm')->get();
    expect($ukms)->toHaveCount(11);
    foreach ($ukms as $ukm) {
        expect($ukm->isUkm())->toBeTrue()
            ->and($ukm->isHima())->toBeFalse()
            ->and($ukm->leadershipTitle())->toBe('Ketua UKM');
    }
});
