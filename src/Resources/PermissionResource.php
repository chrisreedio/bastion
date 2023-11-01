<?php

namespace ChrisReedIO\Bastion\Resources;

use ChrisReedIO\Bastion\Resources\PermissionResource\Pages;
use ChrisReedIO\Bastion\Resources\PermissionResource\RelationManagers;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
		return $form
			->schema([
				Card::make()
					->schema([
						Grid::make(2)->schema([
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
					]),
			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('id')
					->label('ID')
					->searchable(),
				TextColumn::make('name')
					->label(__('bastion::messages.field.name'))
					->searchable(),
				TextColumn::make('guard_name')
					->toggleable(isToggledHiddenByDefault: config('bastion.toggleable_guard_names.permissions.isToggledHiddenByDefault', true))
					->label(__('bastion::messages.field.guard_name'))
					->searchable(),
			])
			->filters([
				//
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
