<?php

use App\Models\Ormawa;
use Database\Seeders\MasterDataSeeder;

it('seeds each ormawa and remains idempotent when run repeatedly', function () {
    $seeder = app(MasterDataSeeder::class);

    $seeder->run();
    $seeder->run();

    expect(Ormawa::query()->orderBy('code')->pluck('code')->all())
        ->toBe(['BEM', 'HIMANERS', 'HIMATIF'])
        ->and(Ormawa::query()->count())->toBe(3);
});
