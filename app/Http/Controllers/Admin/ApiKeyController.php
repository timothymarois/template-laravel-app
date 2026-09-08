<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ApiAbility;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiKey\StoreApiKeyRequest;
use App\Models\User;
use App\Services\ApiKeyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;
use Laravel\Sanctum\PersonalAccessToken;

class ApiKeyController extends Controller
{
    public function __construct(
        protected ApiKeyService $apiKeys,
        protected ResponseFactory $inertia,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', PersonalAccessToken::class);

        return $this->page($request->user());
    }

    /**
     * Renders instead of redirecting, deliberately.
     *
     * The obvious shape — flash the plaintext, redirect, read it back — writes the
     * key into the session store, which in production is a database row. The one
     * value that must exist only in transit would then be at rest. Rendering from
     * the request that created it means the plaintext appears in exactly one
     * response body and nowhere else.
     *
     * History is encrypted around that render because Inertia keeps a page's props
     * in history.state, where the back button would otherwise find the key. The
     * flag is reset immediately: the factory is resolved once, so leaving it on
     * would spread to every later render in a long-lived worker.
     */
    public function store(StoreApiKeyRequest $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $issued = $this->apiKeys->issue(
            owner: $user,
            name: $request->string('name')->toString(),
            abilities: $request->array('abilities'),
            // Not `?: null` — 0 is NEVER_EXPIRES and would be coerced back to the
            // default lifetime, silently issuing an expiring key to somebody who
            // asked for a permanent one.
            lifetimeDays: $request->input('lifetime_days') === null
                ? null
                : $request->integer('lifetime_days'),
        );

        $this->inertia->encryptHistory();

        $response = $this->page($user, [
            'name' => $issued->key->name,
            'plainText' => $issued->plainText,
        ]);

        $this->inertia->encryptHistory(false);

        return $response;
    }

    public function destroy(Request $request, PersonalAccessToken $apiKey): RedirectResponse
    {
        $this->authorize('delete', $apiKey);

        /** @var User $user */
        $user = $request->user();

        $this->apiKeys->revoke($user, (int) $apiKey->getKey());

        return back()->with('status', 'API key revoked.');
    }

    /**
     * @param  array{name: string, plainText: string}|null  $issuedKey
     */
    private function page(User $user, ?array $issuedKey = null): Response
    {
        return $this->inertia->render('admin/api-keys/Index', [
            'keys' => $this->apiKeys->listFor($user)->map(fn (PersonalAccessToken $key): array => [
                'id' => $key->getKey(),
                'name' => $key->name,
                'abilities' => $key->abilities,
                'last_used_at' => $key->last_used_at,
                'expires_at' => $key->expires_at,
                'created_at' => $key->created_at,
            ])->all(),
            'abilities' => array_map(
                fn (ApiAbility $ability): array => ['value' => $ability->value, 'label' => $ability->label()],
                ApiAbility::cases(),
            ),
            'issuedKey' => $issuedKey,
        ]);
    }
}
