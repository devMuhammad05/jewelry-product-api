<?php

declare(strict_types=1);

namespace App\Filament\Resources\Collections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class CollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Collection Details')
                    ->description('Primary identifying information for this collection')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Collection Name')
                            ->placeholder('e.g. Summer Brilliance')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        Select::make('parent_id')
                            ->label('Parent Collection')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select parent if this is a sub-collection'),

                        TextInput::make('position')
                            ->label('Display Order')
                            ->helperText('Lower numbers appear first in the navigation')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_featured')
                            ->label('Featured Collection')
                            ->helperText('Featured collections are highlighted on the home page')
                            ->required()
                            ->default(false),

                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Provide a brief overview of this collection...')
                            ->columnSpanFull()
                            ->rows(4),
                    ]),

                Section::make('Visual Assets')
                    ->description('Hero images and banners for the collection page')
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Hero Image')
                            ->image()
                            ->directory('collections')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
