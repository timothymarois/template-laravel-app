<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Http\Traits\HandlesIndexOptions;

class UserController extends Controller
{
    use HandlesIndexOptions;

    protected array $filterCasts = [
        'user_id' => 'int',
    ];

    protected array $indexDefaults = [
        'perPage' => 50,
        'sortField' => 'name',
        'sortOrder' => 1,
    ];

    public function __construct(protected UserService $userService) {}

    public function index(Request $request)
    {
        $options = $this->resolveIndexOptions($request);

        $options['perPage'] = 50;

        $query = $this->userService->listPaginated($options['perPage'], $options);

        return Inertia::render('Users/Index', [
            'users' => $query,
            'options' => $options,
        ]);
    }

    public function simpleTable(Request $request)
    {
        $options = $this->resolveIndexOptions($request);

        $options['perPage'] = 50;

        $query = $this->userService->listPaginated($options['perPage'], $options);

        return Inertia::render('Users/SimpleTable', [
            'users' => $query,
            'options' => $options,
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Users/Show', [
            'item' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = $this->userService->create($validated);

        return redirect()->route('users.show', $user);
        // return back();
    }

    public function update(Request $request, User $user)
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

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return back();
    }
}
