<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Response;
use Inertia\ResponseFactory;

class DashboardController extends Controller
{
    public function __construct(
        protected ResponseFactory $inertia
    ) {}

    public function index(): Response
    {
        return $this->inertia->render('admin/Index');
    }
}
