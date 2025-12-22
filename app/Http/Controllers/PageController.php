<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Index');
    }

    public function index(): Response
    {
        return Inertia::render('admin/Index');
    }
}
