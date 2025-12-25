<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Response;
use Inertia\ResponseFactory;

class ComponentController extends Controller
{
    public function __construct(
        protected ResponseFactory $inertia
    ) {}

    public function index(): Response
    {
        return $this->inertia->render('admin/components/Index');
    }

    // Forms
    public function formsIndex(): Response
    {
        return $this->inertia->render('admin/components/forms/Index');
    }

    public function formsInput(): Response
    {
        return $this->inertia->render('admin/components/forms/Input');
    }

    public function formsInputMasks(): Response
    {
        return $this->inertia->render('admin/components/forms/InputMasks');
    }

    public function formsTextarea(): Response
    {
        return $this->inertia->render('admin/components/forms/Textarea');
    }

    public function formsSelect(): Response
    {
        return $this->inertia->render('admin/components/forms/Select');
    }

    public function formsCheckbox(): Response
    {
        return $this->inertia->render('admin/components/forms/Checkbox');
    }

    public function formsCombobox(): Response
    {
        return $this->inertia->render('admin/components/forms/Combobox');
    }

    public function formsSwitch(): Response
    {
        return $this->inertia->render('admin/components/forms/Switch');
    }

    public function formsSlider(): Response
    {
        return $this->inertia->render('admin/components/forms/Slider');
    }

    public function formsFields(): Response
    {
        return $this->inertia->render('admin/components/forms/Fields');
    }

    public function formsCalendar(): Response
    {
        return $this->inertia->render('admin/components/forms/Calendar');
    }

    public function formsEditor(): Response
    {
        return $this->inertia->render('admin/components/forms/Editor');
    }

    public function formsUpload(): Response
    {
        return $this->inertia->render('admin/components/forms/Upload');
    }

    public function formsTagsInput(): Response
    {
        return $this->inertia->render('admin/components/forms/TagsInput');
    }

    // Actions
    public function actionsIndex(): Response
    {
        return $this->inertia->render('admin/components/actions/Index');
    }

    public function actionsButton(): Response
    {
        return $this->inertia->render('admin/components/actions/Button');
    }

    public function actionsCommand(): Response
    {
        return $this->inertia->render('admin/components/actions/Command');
    }

    public function actionsMenu(): Response
    {
        return $this->inertia->render('admin/components/actions/Menu');
    }

    public function actionsDialog(): Response
    {
        return $this->inertia->render('admin/components/actions/Dialog');
    }

    public function actionsSheet(): Response
    {
        return $this->inertia->render('admin/components/actions/Sheet');
    }

    // Display
    public function displayIndex(): Response
    {
        return $this->inertia->render('admin/components/display/Index');
    }

    public function displayAlert(): Response
    {
        return $this->inertia->render('admin/components/display/Alert');
    }

    public function displayCard(): Response
    {
        return $this->inertia->render('admin/components/display/Card');
    }

    public function displayBadge(): Response
    {
        return $this->inertia->render('admin/components/display/Badge');
    }

    public function displayAvatar(): Response
    {
        return $this->inertia->render('admin/components/display/Avatar');
    }

    public function displayTooltip(): Response
    {
        return $this->inertia->render('admin/components/display/Tooltip');
    }

    public function displayPopover(): Response
    {
        return $this->inertia->render('admin/components/display/Popover');
    }

    public function displayLoading(): Response
    {
        return $this->inertia->render('admin/components/display/Loading');
    }

    public function displayTabs(): Response
    {
        return $this->inertia->render('admin/components/display/Tabs');
    }

    public function displayAccordion(): Response
    {
        return $this->inertia->render('admin/components/display/Accordion');
    }

    public function displayToast(): Response
    {
        return $this->inertia->render('admin/components/display/Toast');
    }

    public function displayCarousel(): Response
    {
        return $this->inertia->render('admin/components/display/Carousel');
    }

    public function displayResizable(): Response
    {
        return $this->inertia->render('admin/components/display/Resizable');
    }

    // Data
    public function dataIndex(): Response
    {
        return $this->inertia->render('admin/components/data/Index');
    }

    public function dataTable(): Response
    {
        return $this->inertia->render('admin/components/data/Table');
    }

    public function dataActions(): Response
    {
        return $this->inertia->render('admin/components/data/Actions');
    }

    public function dataPagination(): Response
    {
        return $this->inertia->render('admin/components/data/Pagination');
    }

    // Charts
    public function chartsIndex(): Response
    {
        return $this->inertia->render('admin/components/charts/Index');
    }

    public function chartsBar(): Response
    {
        return $this->inertia->render('admin/components/charts/Bar');
    }

    public function chartsLine(): Response
    {
        return $this->inertia->render('admin/components/charts/Line');
    }

    public function chartsArea(): Response
    {
        return $this->inertia->render('admin/components/charts/Area');
    }

    public function chartsPie(): Response
    {
        return $this->inertia->render('admin/components/charts/Pie');
    }
}
