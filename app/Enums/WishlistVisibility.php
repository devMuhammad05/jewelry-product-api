<?php

declare(strict_types=1);

namespace App\Enums;

enum WishlistVisibility: string
{
    case Private = 'private';
    case Shared = 'shared';
}
