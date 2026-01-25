<?php

declare(strict_types=1);

namespace App\Filament\Resources\Collections\Pages;

use App\Filament\Resources\Collections\CollectionResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateCollection extends CreateRecord
{
    protected static string $resource = CollectionResource::class;
}
