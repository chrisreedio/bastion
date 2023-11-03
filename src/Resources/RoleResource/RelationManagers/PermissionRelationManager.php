<?php

namespace ChrisReedIO\Bastion\Resources\RoleResource\RelationManagers;

use ChrisReedIO\Bastion\BastionPlugin;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

use function __;
use function collect;
use function explode;

class PermissionRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    protected static ?string $recordTitleAttribute = 'name';

    /*
     * Support changing tab title by translations in RelationManager.
     */
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('bastion::messages.section.permissions') ?? (string) str(static::getRelationshipName())
            ->kebab()
            ->replace('-', ' ')
            ->headline();
    }

    protected static function getModelLabel(): string
    {
        return __('bastion::messages.section.permission');
    }

    protected static function getPluralModelLabel(): string
    {
        return __('bastion::messages.section.permissions');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('bastion::messages.field.name')),
                TextInput::make('guard_name')
                    ->label(__('bastion::messages.field.guard_name')),
            ]);
    }

    public function table(Table $table): Table
    {
        $superAdminRole = BastionPlugin::get()->getSuperAdminRole();
        // dd($superAdminRole);
        $isSuperAdmin = $this->getOwnerRecord()->name === $superAdminRole;
        $resources = Filament::getResources();
        $resourceOptions = collect($resources)->mapWithKeys(fn ($resource) => [$resource => $resource::getLabel() ?? $resource])->all();

        // dd($resourceOptions);
        return $table
            // Support changing table heading by translations.
            ->heading(__('bastion::messages.section.permissions'))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('bastion::messages.field.full_name'))
                    ->color('gray'),
                TextColumn::make('display_name')
                    ->label(__('bastion::messages.field.short_name'))
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(function (string $state): string {
                        // Split upon the :: delimiter and return the first element then convert to title case from snake case
                        $permission = explode('::', $state)[0];

                        // dd($permission);
                        return Str::of($permission)->snake()->replace('_', ' ')->title();
                    })
                    ->searchable(),

                TextColumn::make('resource')
                    ->label(__('bastion::messages.field.resource'))
                    ->formatStateUsing(function (string $state): string {
                        if (Str::startsWith($state, 'App')) {
                            return Str::remove('App\\Filament\\Resources\\', $state);
                        }

                        return $state;
                    })
                    ->color(fn ($state) => Str::startsWith($state, 'App') ? 'info' : 'warning')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('guard_name')
                    ->searchable()
                    ->label(__('bastion::messages.field.guard_name'))
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('resource')
                    ->label(__('bastion::messages.field.resource'))
                    ->options($resourceOptions),
            ])
            ->groups($isSuperAdmin ? [] : ['resource', 'display_name'])
            ->defaultGroup('resource')
            ->emptyStateHeading(fn () => $isSuperAdmin
                ? __('bastion::messages.table.empty.permissions_super_admin')
                : __('bastion::messages.table.empty.permissions'))
            ->emptyStateIcon(fn () => $isSuperAdmin ? 'heroicon-o-shield-check' : 'heroicon-o-x-mark')
            ->headerActions([
                AttachAction::make('Attach Permission')
                    ->preloadRecordSelect()
                    ->recordSelect(function (Select $select) {
                        return $select->multiple();
                    })
                    // ->after(fn() => app()
                    //     ->make(PermissionRegistrar::class)
                    //     ->forgetCachedPermissions()
                    // )
                    ->visible(fn ($record) => ! $isSuperAdmin),
            ])->actions([
                DetachAction::make()->after(fn () => app()->make(PermissionRegistrar::class)->forgetCachedPermissions()),
            ])->bulkActions([
                DetachBulkAction::make()->after(fn () => app()->make(PermissionRegistrar::class)->forgetCachedPermissions()),
            ]);
    }
}
