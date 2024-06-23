<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ExampleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Example/Index',[
            'example' => 'Example Prop 1'
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('Example/About', [
            'example' => 'Example Prop 2'
        ]);
    }

    public function store(): Response
    {
        return Inertia::render('Example/Store');
    }
}
