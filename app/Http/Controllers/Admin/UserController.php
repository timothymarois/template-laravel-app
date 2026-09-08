<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Concerns\InertiaDataTableOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\Models\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class UserController extends Controller
{
    use InertiaDataTableOptions;

    protected array $filterCasts = [
        'user_id' => 'int',
    ];

    protected array $indexDefaults = [
        'perPage' => 50,
        'sortField' => 'name',
        'sortOrder' => 1,
    ];

    public function __construct(
        protected UserService $userService,
        protected ResponseFactory $inertia,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $options = $this->resolveIndexOptions($request, true, 'admin.users.index');

        return $this->inertia->render('admin/users/Index', [
            'users' => $this->userService->listPaginated($options['perPage'], $options),
            'options' => $options,
        ]);
    }

    public function prepareIndexFilters(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $this->resolveIndexOptions($request, true, 'admin.users.index');

        return redirect()->route('admin.users.index');
    }

    public function simpleTable(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $options = $this->resolveIndexOptions($request);

        if (! isset($options['filters']['user_id'])) {
            $options['filters']['user_id'] = null;
        }

        $query = $this->userService->listPaginated($options['perPage'], $options);

        return $this->inertia->render('admin/users/SimpleTable', [
            'users' => $query,
            'options' => $options,
        ]);
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);

        return $this->inertia->render('admin/users/Show', [
            'item' => $user,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->userService->create($request->validated());

        return redirect()->route('admin.users.show', $user);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user, $request->validated());

        return back();
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        return back();
    }
}
