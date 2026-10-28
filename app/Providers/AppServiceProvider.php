<?php

namespace App\Providers;

use App\Livewire\Dashboard\Index;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('dashboard', Index::class);

        // Pass Windows user temp directories to artisan serve child PHP process
        if (class_exists(\Illuminate\Foundation\Console\ServeCommand::class)) {
            \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables = array_merge(
                \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables,
                ['TEMP', 'TMP', 'USERPROFILE', 'LOCALAPPDATA', 'APPDATA']
            );
        }

        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }
        \PhpOffice\PhpWord\Settings::setTempDir($tempDir);
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
    }
}
