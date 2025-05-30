<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ComponentController extends Controller
{
    public function forms(): Response
    {
        return Inertia::render('Components/Forms');
    }

    public function editor(): Response
    {
        return Inertia::render('Components/Editor');
    }

    public function editorVariant(): Response
    {
        return Inertia::render('Components/EditorVariant');
    }
}
