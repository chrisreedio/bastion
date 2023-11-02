<?php

namespace ChrisReedIO\Bastion\Resources;

use ChrisReedIO\Bastion\Resources\RoleResource\Pages;
use ChrisReedIO\Bastion\Resources\RoleResource\RelationManagers;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

use function class_exists;
use function config;

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
        $sso_enabled = fn () => config('bastion.sso.enabled', false) || class_exists(\ChrisReedIO\Socialment\SocialmentPlugin::class, false);

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('bastion::messages.field.name'))
                                    ->required(),
                                Select::make('guard_name')
                                    ->label(__('bastion::messages.field.guard_name'))
                                    ->options(config('bastion.guards'))
                                    // ->selectablePlaceholder(false)
                                    ->default(config('bastion.default_guard'))
                                    ->required(),
                                Select::make('permissions')
                                    ->multiple()
                                    ->label(__('bastion::messages.field.permissions'))
                                    ->relationship('permissions', 'name')
                                    ->preload(config('bastion.permissions.preload', true)),

                                TextInput::make('sso_group')
                                    ->label(__('bastion::messages.field.sso_group'))
                                    ->visible($sso_enabled),
                                // Select::make(config('permission.column_names.team_foreign_key', 'team_id'))
                                // 	->label(__('bastion::messages.field.team'))
                                // 	->hidden(fn () => !config('permission.teams', false) || Filament::hasTenancy())
                                // 	->options(
                                // 		fn () => config('bastion.team_model', App\Models\Team::class)::pluck('name', 'id')
                                // 	)
                                // 	->dehydrated(fn ($state) => (int) $state <= 0)
                                // 	->placeholder(__('bastion::messages.select-team'))
                                // 	->hint(__('bastion::messages.select-team-hint')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $sso_enabled = fn () => config('bastion.sso.enabled', false) || class_exists(\ChrisReedIO\Socialment\SocialmentPlugin::class, false);

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
                    ->searchable(),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label(__('bastion::messages.field.permissions_count'))
                    ->toggleable(isToggledHiddenByDefault: config('bastion.toggleable_guard_names.roles.isToggledHiddenByDefault', true))
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->toggleable(isToggledHiddenByDefault: config('bastion.toggleable_guard_names.roles.isToggledHiddenByDefault', true))
                    ->label(__('bastion::messages.field.guard_name'))
                    ->searchable(),
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
