<?php

namespace App\Http\Controllers\Admin;

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

    public function __construct(protected UserService $userService) {}

    public function index(Request $request)
    {
        $options = $this->resolveIndexOptions($request);

        $query = $this->userService->listPaginated($options['perPage'], $options);

        return Inertia::render('Admin/Users', [
            'users' => $query,
            'options' => $options,
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/User', [
            'item' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        ]);

        $this->userService->create($validated);

        return back();
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
