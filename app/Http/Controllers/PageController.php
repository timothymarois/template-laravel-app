<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Response;
use Inertia\ResponseFactory;

class PageController extends Controller
{
    public function __construct(
        protected ResponseFactory $inertia
    ) {}

    public function home(): Response
    {
        return $this->inertia->render('Index');
    }

    public function index(): Response
    {
        return $this->inertia->render('admin/Index');
    }
}
