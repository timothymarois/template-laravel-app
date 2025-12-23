<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ComponentShowcaseController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/components/Index');
    }

    // Forms
    public function formsIndex(): Response
    {
        return Inertia::render('admin/components/forms/Index');
    }

    public function formsInput(): Response
    {
        return Inertia::render('admin/components/forms/Input');
    }

    public function formsSelect(): Response
    {
        return Inertia::render('admin/components/forms/Select');
    }

    public function formsCheckbox(): Response
    {
        return Inertia::render('admin/components/forms/Checkbox');
    }

    public function formsFields(): Response
    {
        return Inertia::render('admin/components/forms/Fields');
    }

    public function formsCalendar(): Response
    {
        return Inertia::render('admin/components/forms/Calendar');
    }

    // Actions
    public function actionsIndex(): Response
    {
        return Inertia::render('admin/components/actions/Index');
    }

    public function actionsButton(): Response
    {
        return Inertia::render('admin/components/actions/Button');
    }

    public function actionsMenu(): Response
    {
        return Inertia::render('admin/components/actions/Menu');
    }

    public function actionsDialog(): Response
    {
        return Inertia::render('admin/components/actions/Dialog');
    }

    // Display
    public function displayIndex(): Response
    {
        return Inertia::render('admin/components/display/Index');
    }

    public function displayAlert(): Response
    {
        return Inertia::render('admin/components/display/Alert');
    }

    public function displayCard(): Response
    {
        return Inertia::render('admin/components/display/Card');
    }

    public function displayBadge(): Response
    {
        return Inertia::render('admin/components/display/Badge');
    }

    public function displayTooltip(): Response
    {
        return Inertia::render('admin/components/display/Tooltip');
    }

    // Data
    public function dataIndex(): Response
    {
        return Inertia::render('admin/components/data/Index');
    }

    public function dataTable(): Response
    {
        return Inertia::render('admin/components/data/Table');
    }

    public function dataActions(): Response
    {
        return Inertia::render('admin/components/data/Actions');
    }

    public function dataPagination(): Response
    {
        return Inertia::render('admin/components/data/Pagination');
    }
}
