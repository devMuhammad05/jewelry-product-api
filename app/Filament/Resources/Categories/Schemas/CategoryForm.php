<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('General Information')
                    ->description('Basic details and nesting for this category')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Category Name')
                            ->placeholder('e.g. Diamond Rings')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        Select::make('parent_id')
                            ->label('Parent Category')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a parent category if this is a sub-category'),

                        TextInput::make('position')
                            ->label('Display Order')
                            ->helperText('Lower numbers appear first in the navigation')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Provide a brief overview of the products in this category...')
                            ->columnSpanFull()
                            ->rows(4),
                    ]),

                Section::make('Media & Visibility')
                    ->description('Visual assets and storefront display settings')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('image_url')
                            ->label('Category Banner/Thumbnail')
                            ->image()
                            ->directory('categories')
                            ->imageEditor()
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Visible in Storefront')
                            ->helperText('If disabled, this category and its products will be hidden from customers')
                            ->required()
                            ->default(true),
                    ]),
            ]);
    }
}
