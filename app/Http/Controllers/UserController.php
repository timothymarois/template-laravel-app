<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Atlas\Laravel\Http\Concerns\InertiaDataTableOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

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

    public function __construct(protected UserService $userService) {}

    public function index(Request $request): Response
    {
        $options = $this->resolveIndexOptions($request, true, 'users.index');

        return Inertia::render('Users/Index', [
            'users' => $this->userService->listPaginated($options['perPage'], $options),
            'options' => $options,
        ]);
    }

    public function prepareIndexFilters(Request $request): RedirectResponse
    {
        $this->resolveIndexOptions($request, true, 'users.index');

        return redirect()->route('users.index');
    }

    public function simpleTable(Request $request): Response
    {
        $options = $this->resolveIndexOptions($request);

        if (! isset($options['filters']['user_id'])) {
            $options['filters']['user_id'] = null;
        }

        $query = $this->userService->listPaginated($options['perPage'], $options);

        return Inertia::render('Users/SimpleTable', [
            'users' => $query,
            'options' => $options,
        ]);
    }

    public function show(User $user): Response
    {
        return Inertia::render('Users/Show', [
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

        return redirect()->route('users.show', $user);
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
