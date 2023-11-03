<?php

namespace ChrisReedIO\Bastion\Resources;

use ChrisReedIO\Bastion\Enums\DefaultPermissions;
use ChrisReedIO\Bastion\Resources\PermissionResource\Pages;
use ChrisReedIO\Bastion\Resources\PermissionResource\RelationManagers;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use function __;
use function class_basename;
use function collect;
use function explode;

class PermissionResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    public static function shouldRegisterNavigation(): bool
    {
        return config('bastion.should_register_on_navigation.permissions', true);
    }

    public static function getModel(): string
    {
        return config('permission.models.permission', Permission::class);
    }

    public static function getLabel(): string
    {
        return __('bastion::messages.section.permission');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('bastion.navigation_section_group', 'bastion::messages.navigation_group'));
    }

    public static function getPluralLabel(): string
    {
        return __('bastion::messages.section.permissions');
    }

    public static function form(Form $form): Form
    {
        $resources = collect(Filament::getResources())
            ->mapWithKeys(fn($resource) => [
                $resource => Str::remove('Resource', class_basename($resource)),
            ])->all();

        return $form
            ->schema([
                Section::make()
                    ->heading()
                    ->columns(2)
                    ->schema([
                        Select::make('resource')
                            ->label(__('bastion::messages.field.resource'))
                            ->options($resources),
                        TextInput::make('name')
                            ->label(__('bastion::messages.field.name'))
                            ->required(),
                        Select::make('guard_name')
                            ->label(__('bastion::messages.field.guard_name'))
                            ->options(config('bastion.guards'))
                            ->default(config('bastion.default_guard'))
                            ->required(),
                        Select::make('roles')
                            ->multiple()
                            ->label(__('bastion::messages.field.roles'))
                            ->relationship('roles', 'name')
                            ->preload(config('bastion.preload_roles', true)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $resources = Filament::getResources();
        $resourceOptions = collect($resources)->mapWithKeys(fn($resource) => [$resource => Str::title($resource::getModelLabel())])->all();

        return $table
            ->columns([
                // TextColumn::make('id')
                //     ->label('ID')
                //     ->sortable()
                //     ->searchable(),
                TextColumn::make('name')
                    ->label(__('bastion::messages.field.name'))
                    ->formatStateUsing(function (string $state): string {
                        // Split upon the :: delimiter and return the first element then convert to title case from snake case
                        $permission = explode('::', $state)[0];

                        // dd($permission);
                        return Str::of($permission)->snake()->replace('_', ' ')->title();
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('resource')
                    ->label(__('bastion::messages.field.resource'))
                    ->formatStateUsing(function (string $state): string {
                        if (Str::startsWith($state, 'App')) {
                            return Str::remove('App\\Filament\\Resources\\', $state);
                        }

                        return $state;
                    })
                    ->color(fn($state) => Str::startsWith($state, 'App') ? 'info' : 'warning')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label(__('bastion::messages.field.roles'))
                    // ->formatStateUsing(function (string $state): string {
                    //     return Str::remove('App\\Filament\\Resources\\', $state);
                    // })
                    // ->color(fn ($state) => Str::startsWith($state, 'App') ? 'info' : 'warning')
                    ->badge(),
                TextColumn::make('guard_name')
                    ->toggleable(isToggledHiddenByDefault: config('bastion.toggleable_guard_names.permissions.isToggledHiddenByDefault', true))
                    ->label(__('bastion::messages.field.guard_name'))
                    ->searchable(),

            ])
            ->defaultGroup('resource')
            ->groups(['resource', 'display_name'])
            ->filters([
                SelectFilter::make('resource')
                    ->label(__('bastion::messages.field.resource'))
                    ->options($resourceOptions)
                    ->multiple(),
                SelectFilter::make('display_name')
                    ->label(__('bastion::messages.field.short_name'))
                    ->options(DefaultPermissions::class)
                    ->multiple(),
            ])->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                BulkAction::make('Attach Role')
                    ->action(function (Collection $records, array $data): void {
                        /** @var Permission $record */
                        foreach ($records as $record) {
                            $record->roles()->sync($data['role']);
                            $record->save();
                        }
                    })
                    ->form([
                        Select::make('role')
                            ->label(__('bastion::messages.field.role'))
                            ->options(Role::query()->pluck('name', 'id'))
                            ->required(),
                    ])->deselectRecordsAfterCompletion(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RoleRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
            'view' => Pages\ViewPermission::route('/{record}'),
        ];
    }
}
