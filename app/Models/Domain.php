<?php

declare(strict_types=1);

namespace App\Models;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

/**
 * The template's Domain model. Defined locally so the application has its
 * own class to extend; inherits all behavior from the package's base.
 */
class Domain extends BaseDomain {}
