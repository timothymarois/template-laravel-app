<?php

use App\Http\Controllers\ComponentShowcaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComponentShowcaseController::class, 'index'])->name('index');

// Forms
Route::get('/forms', [ComponentShowcaseController::class, 'formsIndex'])->name('forms');
Route::get('/forms/input', [ComponentShowcaseController::class, 'formsInput'])->name('forms.input');
Route::get('/forms/select', [ComponentShowcaseController::class, 'formsSelect'])->name('forms.select');
Route::get('/forms/checkbox', [ComponentShowcaseController::class, 'formsCheckbox'])->name('forms.checkbox');
Route::get('/forms/switch', [ComponentShowcaseController::class, 'formsSwitch'])->name('forms.switch');
Route::get('/forms/slider', [ComponentShowcaseController::class, 'formsSlider'])->name('forms.slider');
Route::get('/forms/fields', [ComponentShowcaseController::class, 'formsFields'])->name('forms.fields');
Route::get('/forms/calendar', [ComponentShowcaseController::class, 'formsCalendar'])->name('forms.calendar');

// Actions
Route::get('/actions', [ComponentShowcaseController::class, 'actionsIndex'])->name('actions');
Route::get('/actions/button', [ComponentShowcaseController::class, 'actionsButton'])->name('actions.button');
Route::get('/actions/menu', [ComponentShowcaseController::class, 'actionsMenu'])->name('actions.menu');
Route::get('/actions/dialog', [ComponentShowcaseController::class, 'actionsDialog'])->name('actions.dialog');
Route::get('/actions/sheet', [ComponentShowcaseController::class, 'actionsSheet'])->name('actions.sheet');

// Display
Route::get('/display', [ComponentShowcaseController::class, 'displayIndex'])->name('display');
Route::get('/display/alert', [ComponentShowcaseController::class, 'displayAlert'])->name('display.alert');
Route::get('/display/card', [ComponentShowcaseController::class, 'displayCard'])->name('display.card');
Route::get('/display/badge', [ComponentShowcaseController::class, 'displayBadge'])->name('display.badge');
Route::get('/display/avatar', [ComponentShowcaseController::class, 'displayAvatar'])->name('display.avatar');
Route::get('/display/tooltip', [ComponentShowcaseController::class, 'displayTooltip'])->name('display.tooltip');
Route::get('/display/popover', [ComponentShowcaseController::class, 'displayPopover'])->name('display.popover');
Route::get('/display/loading', [ComponentShowcaseController::class, 'displayLoading'])->name('display.loading');

// Data
Route::get('/data', [ComponentShowcaseController::class, 'dataIndex'])->name('data');
Route::get('/data/table', [ComponentShowcaseController::class, 'dataTable'])->name('data.table');
Route::get('/data/actions', [ComponentShowcaseController::class, 'dataActions'])->name('data.actions');
Route::get('/data/pagination', [ComponentShowcaseController::class, 'dataPagination'])->name('data.pagination');
