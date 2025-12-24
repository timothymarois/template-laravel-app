<?php

namespace App\Http\Controllers;

use App\Http\Concerns\InertiaDataTableOptions;
use App\Models\User;
use App\Services\Models\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $options = $this->resolveIndexOptions($request, true, 'admin.users.index');

        return $this->inertia->render('admin/users/Index', [
            'users' => $this->userService->listPaginated($options['perPage'], $options),
            'options' => $options,
        ]);
    }

    public function prepareIndexFilters(Request $request): RedirectResponse
    {
        $this->resolveIndexOptions($request, true, 'admin.users.index');

        return redirect()->route('admin.users.index');
    }

    public function simpleTable(Request $request): Response
    {
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
        return $this->inertia->render('admin/users/Show', [
            'item' => $user,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = $this->userService->create($validated);

        return redirect()->route('admin.users.show', $user);
        // return back();
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $this->userService->update($user, $validated);

        return back();
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user);

        return back();
    }
}
