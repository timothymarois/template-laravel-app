<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Index');
    }

    public function exampleStore(): Response
    {
        return Inertia::render('Example/Store');
    }

    public function exampleStoreActive(): Response
    {
        return Inertia::render('Example/Store');
    }

    public function pTheme(): Response
    {
        return Inertia::render('Example/Theme');
    }

    public function pButtons(): Response
    {
        return Inertia::render('Example/Components/Buttons');
    }

    public function pForms(): Response
    {
        return Inertia::render('Example/Components/Forms');
    }
}
