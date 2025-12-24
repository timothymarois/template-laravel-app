<?php

use App\Http\Controllers\ComponentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComponentController::class, 'index'])->name('index');

// Forms
Route::get('/forms', [ComponentController::class, 'formsIndex'])->name('forms');
Route::get('/forms/input', [ComponentController::class, 'formsInput'])->name('forms.input');
Route::get('/forms/textarea', [ComponentController::class, 'formsTextarea'])->name('forms.textarea');
Route::get('/forms/select', [ComponentController::class, 'formsSelect'])->name('forms.select');
Route::get('/forms/checkbox', [ComponentController::class, 'formsCheckbox'])->name('forms.checkbox');
Route::get('/forms/combobox', [ComponentController::class, 'formsCombobox'])->name('forms.combobox');
Route::get('/forms/switch', [ComponentController::class, 'formsSwitch'])->name('forms.switch');
Route::get('/forms/slider', [ComponentController::class, 'formsSlider'])->name('forms.slider');
Route::get('/forms/fields', [ComponentController::class, 'formsFields'])->name('forms.fields');
Route::get('/forms/calendar', [ComponentController::class, 'formsCalendar'])->name('forms.calendar');
Route::get('/forms/editor', [ComponentController::class, 'formsEditor'])->name('forms.editor');
Route::get('/forms/upload', [ComponentController::class, 'formsUpload'])->name('forms.upload');

// Actions
Route::get('/actions', [ComponentController::class, 'actionsIndex'])->name('actions');
Route::get('/actions/button', [ComponentController::class, 'actionsButton'])->name('actions.button');
Route::get('/actions/command', [ComponentController::class, 'actionsCommand'])->name('actions.command');
Route::get('/actions/dialog', [ComponentController::class, 'actionsDialog'])->name('actions.dialog');
Route::get('/actions/menu', [ComponentController::class, 'actionsMenu'])->name('actions.menu');
Route::get('/actions/sheet', [ComponentController::class, 'actionsSheet'])->name('actions.sheet');

// Display
Route::get('/display', [ComponentController::class, 'displayIndex'])->name('display');
Route::get('/display/alert', [ComponentController::class, 'displayAlert'])->name('display.alert');
Route::get('/display/card', [ComponentController::class, 'displayCard'])->name('display.card');
Route::get('/display/badge', [ComponentController::class, 'displayBadge'])->name('display.badge');
Route::get('/display/avatar', [ComponentController::class, 'displayAvatar'])->name('display.avatar');
Route::get('/display/tooltip', [ComponentController::class, 'displayTooltip'])->name('display.tooltip');
Route::get('/display/popover', [ComponentController::class, 'displayPopover'])->name('display.popover');
Route::get('/display/loading', [ComponentController::class, 'displayLoading'])->name('display.loading');
Route::get('/display/tabs', [ComponentController::class, 'displayTabs'])->name('display.tabs');
Route::get('/display/accordion', [ComponentController::class, 'displayAccordion'])->name('display.accordion');
Route::get('/display/toast', [ComponentController::class, 'displayToast'])->name('display.toast');
Route::get('/display/carousel', [ComponentController::class, 'displayCarousel'])->name('display.carousel');
Route::get('/display/resizable', [ComponentController::class, 'displayResizable'])->name('display.resizable');

// Data
Route::get('/data', [ComponentController::class, 'dataIndex'])->name('data');
Route::get('/data/table', [ComponentController::class, 'dataTable'])->name('data.table');
Route::get('/data/actions', [ComponentController::class, 'dataActions'])->name('data.actions');
Route::get('/data/pagination', [ComponentController::class, 'dataPagination'])->name('data.pagination');

// Charts
Route::get('/charts', [ComponentController::class, 'chartsIndex'])->name('charts');
Route::get('/charts/bar', [ComponentController::class, 'chartsBar'])->name('charts.bar');
Route::get('/charts/line', [ComponentController::class, 'chartsLine'])->name('charts.line');
Route::get('/charts/area', [ComponentController::class, 'chartsArea'])->name('charts.area');
Route::get('/charts/pie', [ComponentController::class, 'chartsPie'])->name('charts.pie');
