<?php

namespace ChrisReedIO\Bastion;

use ChrisReedIO\Bastion\Commands\BastionCommand;
use ChrisReedIO\Bastion\Commands\BastionGenerate;
use ChrisReedIO\Bastion\Testing\TestsBastion;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BastionServiceProvider extends PackageServiceProvider
{
    public static string $name = 'bastion';

    public static string $viewNamespace = 'bastion';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->comment('Publishing Spatie\'s Permission\'s config and migration(s)...');
                        $command->call('vendor:publish', [
                            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
                        ]);
                    })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
                // ->askToStarRepoOnGitHub('chrisreedio/bastion');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/bastion/{$file->getFilename()}"),
                ], 'bastion-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsBastion());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'chrisreedio/bastion';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('bastion', __DIR__ . '/../resources/dist/components/bastion.js'),
            // Css::make('bastion-styles', __DIR__ . '/../resources/dist/bastion.css'),
            // Js::make('bastion-scripts', __DIR__ . '/../resources/dist/bastion.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            BastionCommand::class,
            BastionGenerate::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'modify_roles_table_add_sso_group_column',
        ];
    }
}
