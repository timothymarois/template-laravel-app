<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ListUsersRequest;
use App\Models\User;
use App\Services\Models\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * Lists users for a machine caller (R-APIKEY-1's read scope).
     *
     * Authorized in ListUsersRequest through UserPolicy, so a valid key belonging to a
     * non-admin is refused: `abilities:api:read` says what the KEY may do, never who
     * its owner is.
     *
     * Fields are named explicitly rather than returning the model. A serialized User
     * would publish every column added later — including whatever a fork adds next —
     * to every integration, silently.
     */
    public function index(ListUsersRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $users = $this->userService->listPaginated(
            (int) ($validated['perPage'] ?? 15),
            $validated,
        );

        return response()->json([
            'data' => array_map(
                static fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at?->toIso8601String(),
                ],
                $users->items(),
            ),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }
}
