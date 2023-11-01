<?php

namespace ChrisReedIO\Bastion\Resources\RoleResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserRelationManager extends RelationManager
{
	protected static string $relationship = 'users';

	protected static ?string $recordTitleAttribute = 'name';

	/*
     * Support changing tab title in RelationManager.
     */
	public static function getTitle(Model $ownerRecord, string $pageClass): string
	{
		return __('bastion::messages.section.users') ?? (string) str(static::getRelationshipName())
			->kebab()
			->replace('-', ' ')
			->headline();
	}

	protected static function getModelLabel(): string
	{
		return __('bastion::messages.section.users');
	}

	protected static function getPluralModelLabel(): string
	{
		return __('bastion::messages.section.users');
	}

	public function form(Form $form): Form
	{
		return $form
			->schema([
				TextInput::make(config('bastion.user_name_column'))
					->label(__('bastion::messages.field.name')),
			]);
	}

	public function table(Table $table): Table
	{
		return $table
			// Support changing table heading by translations.
			->heading(__('bastion::messages.section.users'))
			->columns([
				TextColumn::make(config('bastion.user_name_column'))
					->label(__('bastion::messages.field.name'))
					->searchable(),
			])
			->filters([])->headerActions([
				AttachAction::make(),
			])->actions([
				DetachAction::make(),
			])->bulkActions([
				//
			]);
	}
}
