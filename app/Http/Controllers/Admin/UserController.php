<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filters = $request->input('filters', []);
        $perPage = (int) $request->input('perPage', 15);
        $sortField = $request->input('sortField', 'name');
        $sortOrder = (int) $request->input('sortOrder', 1);

        $query = $this->userService->listPaginated($perPage, [
            'search' => $search,
            'filters' => $filters,
            'sortField' => $sortField,
            'sortOrder' => ($sortOrder == -1 ? 'desc' : 'asc'),
        ]);

        return Inertia::render('Admin/Users', [
            'users' => $query,
            'options' => [
                'search' => $search,
                'perPage' => $perPage,
                'sortField' => $sortField,
                'sortOrder' => $sortOrder,
                'filters' => $filters,
            ],
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
