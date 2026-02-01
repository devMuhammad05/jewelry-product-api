<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('avatar')
                    ->label('Image'),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                // TextColumn::make('role')
                //     ->badge()
                //     ->searchable(),
                TextColumn::make('email_verified_at')
                    // ->dateTime()
                    ->badge()
                    ->sortable(),

                // TextColumn::make('email_verified_at')
                //     ->label('Status')
                //     ->badge()
                //     ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                //     ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                //     ->icon(fn (bool $state) => $state ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedXCircle)
                //     ->sortable(),

                TextColumn::make('Date of birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
