<?php

namespace App\Enums\Action\Worker;

enum ActionStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
