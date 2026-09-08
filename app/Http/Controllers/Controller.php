<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    /**
     * AGENTS.md requires a state change with no request input to authorize with
     * `$this->authorize()` in the controller. Laravel no longer applies this trait
     * to the base controller by default, so without it that convention has nothing
     * to call.
     */
    use AuthorizesRequests;
}
