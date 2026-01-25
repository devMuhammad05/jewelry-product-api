<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('General Information')
                    ->description('Basic details and status of the product')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Product Name')
                            ->placeholder('e.g. LOVE Ring')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        Select::make('status')
                            ->label('Status')
                            ->options(ProductStatus::class)
                            ->required()
                            ->default(ProductStatus::Draft),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Provide a detailed description of the product...')
                            ->columnSpanFull()
                            ->rows(4),
                    ]),

                Section::make('Associations')
                    ->description('Organize products into categories, collections, and define attributes')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('categories')
                            ->label('Categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('collections')
                            ->label('Collections')
                            ->relationship('collections', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('attributeValues')
                            ->label('Attributes')
                            ->relationship('attributeValues', 'value')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Inventory & Variants')
                    ->description('Manage product variations, SKUs, and pricing')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('variants')
                            ->relationship('variants')
                            ->columns(3)
                            ->schema([
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(ignorable: fn ($record) => $record),

                                TextInput::make('price')
                                    ->label('Price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label('Stock Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                TextInput::make('compare_at_price')
                                    ->label('Compare at Price')
                                    ->numeric()
                                    ->prefix('$'),

                                TextInput::make('cost_price')
                                    ->label('Cost Price')
                                    ->numeric()
                                    ->prefix('$'),

                                TextInput::make('currency')
                                    ->label('Currency')
                                    ->default('USD')
                                    ->required()
                                    ->maxLength(3),

                                Toggle::make('is_orderable')
                                    ->label('Is Orderable')
                                    ->default(true)
                                    ->columnSpanFull(),

                                TextInput::make('size')
                                    ->label('Size'),

                                TextInput::make('weight')
                                    ->label('Weight')
                                    ->numeric(),

                                TextInput::make('height')
                                    ->label('Height')
                                    ->numeric(),

                                TextInput::make('width')
                                    ->label('Width')
                                    ->numeric(),

                                TextInput::make('depth')
                                    ->label('Depth')
                                    ->numeric(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['sku'] ?? null)
                            ->defaultItems(1)
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),
            ]);
    }
}
