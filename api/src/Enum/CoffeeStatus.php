<?php

namespace App\Enum;

enum CoffeeStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
