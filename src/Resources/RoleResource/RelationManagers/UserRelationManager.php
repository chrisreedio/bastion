<?php

namespace ChrisReedIO\Bastion\Resources\RoleResource\RelationManagers;

use ChrisReedIO\Bastion\BastionPlugin;
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
        return __('bastion::messages.section.users') ?? (string)str(static::getRelationshipName())
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
        $plugin = BastionPlugin::get();
        $sso_enabled = $plugin->getSsoEnabled();

        return $table
            // Support changing table heading by translations.
            ->heading(__('bastion::messages.section.users'))
            ->columns([
                TextColumn::make(config('bastion.user_name_column', 'name'))
                    ->label(__('bastion::messages.field.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make(config('bastion.user_email_column', 'email'))
                    ->label(__('bastion::messages.field.email'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([])->headerActions($sso_enabled ? [] : [
                AttachAction::make(),
            ])->actions($sso_enabled ? [] : [
                DetachAction::make(),
            ])->bulkActions([
                //
            ]);
    }
}
