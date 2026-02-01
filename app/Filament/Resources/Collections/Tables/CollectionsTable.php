<?php

declare(strict_types=1);

namespace App\Filament\Resources\Collections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

final class CollectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('hero_image')
                    ->label('Thumbnail')
                    ->circular()
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Collection Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('parent.name')
                    ->label('Parent Collection')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Primary'),
                TextColumn::make('position')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->icon(fn (bool $state) => $state ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedXCircle)
                    ->sortable(),

                TextColumn::make('is_featured')
                    ->label('Featured')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Featured' : 'Standard')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray')
                    ->icon(fn (bool $state) => $state ? Heroicon::OutlinedStar : Heroicon::OutlinedTag)
                    ->sortable(),

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
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only')
                    ->native(false),
                TernaryFilter::make('is_featured')
                    ->label('Featured Status')
                    ->boolean()
                    ->trueLabel('Featured Only')
                    ->falseLabel('Non-Featured Only')
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
