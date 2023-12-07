<?php

namespace ChrisReedIO\Bastion\Resources;

use ChrisReedIO\Bastion\BastionPlugin;
use ChrisReedIO\Bastion\Resources\RoleResource\Pages;
use ChrisReedIO\Bastion\Resources\RoleResource\RelationManagers;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

use function config;
use function ucwords;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function shouldRegisterNavigation(): bool
    {
        return config('bastion.should_register_on_navigation.roles', true);
    }

    public static function getModel(): string
    {
        return config('permission.models.role', Role::class);
    }

    public static function getLabel(): string
    {
        return __('bastion::messages.section.role');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('bastion.navigation_section_group', 'bastion::messages.navigation_group'));
    }

    public static function getPluralLabel(): string
    {
        return __('bastion::messages.section.roles');
    }

    public static function form(Form $form): Form
    {
        $plugin = BastionPlugin::get();
        $sso_enabled = $plugin->getSsoEnabled();
        $superAdminRole = $plugin->getSuperAdminRole();

        // Get a list of all guards in the app
        $configuredGuards = array_keys(config('auth.guards'));
        $configuredGuards = collect($configuredGuards)
            ->mapWithKeys(fn ($guard) => [$guard => ucwords($guard)])
            ->all();
        $defaultGuard = config('bastion.default_guard');

        return $form
            ->schema([
                Section::make()
                    ->schema([

                        TextInput::make('name')
                            ->label(__('bastion::messages.field.name'))
                            ->required(),

                        Select::make('guard_name')
                            ->label(__('bastion::messages.field.guard_name'))
                            // ->options(config('bastion.guards'))
                            ->options($configuredGuards)
                            ->default($defaultGuard)
                            ->selectablePlaceholder(false)
                            ->required(),

                        TextInput::make('sso_group')
                            ->label(__('bastion::messages.field.sso_group'))
                            ->visible($sso_enabled),

                        Placeholder::make('super_admin')
                            ->label(__('bastion::messages.field.super_admin'))
                            ->hintIcon('heroicon-o-shield-check')
                            // ->hint(__('bastion::messages.field.super_admin-hint'))
                            ->hintColor('info')
                            ->content(__('bastion::messages.field.super_admin-hint'))
                            ->columnSpan(['sm' => 1, 'lg' => 3])
                            ->visible(fn ($record) => $record?->name === $superAdminRole),

                        // Fieldset::make()
                        //     ->schema([
                        //         Toggle::make('super_admin')
                        //             // ->disabled()
                        //             ->label(__('bastion::messages.field.super_admin'))
                        //             ->afterStateHydrated(fn ($set, $record) => $set('super_admin', $record->name === $superAdminRole)),
                        //             // ->default(fn ($record) => dd($record))//$record->name === $superAdminRole),
                        //             // ->default(fn() => true),
                        //             // ->default(true),
                        //     ]),
                        // Select::make(config('permission.column_names.team_foreign_key', 'team_id'))
                        // 	->label(__('bastion::messages.field.team'))
                        // 	->hidden(fn () => !config('permission.teams', false) || Filament::hasTenancy())
                        // 	->options(
                        // 		fn () => config('bastion.team_model', App\Models\Team::class)::pluck('name', 'id')
                        // 	)
                        // 	->dehydrated(fn ($state) => (int) $state <= 0)
                        // 	->placeholder(__('bastion::messages.select-team'))
                        // 	->hint(__('bastion::messages.select-team-hint')),

                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => $sso_enabled ? 3 : 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $plugin = BastionPlugin::get();
        $sso_enabled = $plugin->getSsoEnabled();
        $superAdminRole = $plugin->getSuperAdminRole();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),

                TextColumn::make('name')
                    ->label(__('bastion::messages.field.name'))
                    ->searchable(),

                TextColumn::make('sso_group')
                    ->label(__('bastion::messages.field.sso_group'))
                    ->visible($sso_enabled)
                    ->sortable()
                    ->badge()
                    ->copyable()
                    ->searchable(),

                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label(__('bastion::messages.field.permissions_count'))
                    ->color(fn ($record) => $record->name === $superAdminRole ? 'success' : 'info')
                    ->formatStateUsing(fn ($state, $record) => $record->name === $superAdminRole ? 'Super Admin' : $state)
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: config('bastion.toggleable_guard_names.roles.isToggledHiddenByDefault', false)),
                Tables\Columns\TextColumn::make('users_count')
                    ->label(__('bastion::messages.field.users_count'))
                    ->counts('users')
                    ->badge(),

                TextColumn::make('guard_name')
                    ->toggleable(isToggledHiddenByDefault: config('bastion.toggleable_guard_names.roles.isToggledHiddenByDefault', true))
                    ->label(__('bastion::messages.field.guard_name'))
                    ->searchable(),

                // Tables\Columns\IconColumn::make('super_admin')
                //     ->label(__('bastion::messages.field.super_admin'))
                //     ->boolean()
                //     ->default(fn($record) => $record->name === $superAdminRole),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PermissionRelationManager::class,
            RelationManagers\UserRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
            'view' => Pages\ViewRole::route('/{record}'),
        ];
    }
}
