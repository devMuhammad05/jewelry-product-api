<?php

declare(strict_types=1);

namespace App\Enums;

enum CartStatus: string
{
    case Active = 'active';
    case Abandoned = 'abandoned';
    case Converted = 'converted';
    case Merged = 'merged';
}
