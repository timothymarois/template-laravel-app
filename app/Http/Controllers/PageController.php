<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Home', [
            'example' => 'Example Prop 1'
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'example' => 'Example Prop 2'
        ]);
    }
}
