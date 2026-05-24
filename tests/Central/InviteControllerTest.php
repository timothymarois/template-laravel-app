<?php

declare(strict_types=1);

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantInviteService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| InviteController — feature tests for the public accept/decline flow
|--------------------------------------------------------------------------
*/

uses(CentralBaseTestCase::class);

beforeEach(function () {
    Event::fake([TenantCreated::class]);
    Notification::fake();
    // The template doesn't ship invites/Show, invites/Expired, invites/Mismatch
    // Vue files — forks build those. Skip Inertia's "page file exists" check.
    config(['inertia.testing.ensure_pages_exist' => false]);
    $this->service = app(TenantInviteService::class);
});

it('GET /invites/{token} renders the show page when invite is pending', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com', TenantRole::Admin);

    $this->get('/invites/'.$invite->token)
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('invites/Show')
            ->where('token', $invite->token)
            ->where('email', 'newcomer@example.com')
            ->where('role', 'admin')
            ->where('tenant.id', $tenant->id)
        );
});

it('GET /invites/{token} renders Expired when token is unknown', function () {
    $this->get('/invites/nonexistent-token')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('invites/Expired'));
});

it('GET /invites/{token} renders Expired when invite has expired', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');
    $invite->update(['expires_at' => now()->subDay()]);

    $this->get('/invites/'.$invite->token)
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('invites/Expired'));
});

it('POST /invites/{token}/accept redirects to login when not authenticated', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    $this->post('/invites/'.$invite->token.'/accept')
        ->assertRedirect(route('login', ['invite' => $invite->token]));
});

it('POST /invites/{token}/accept attaches the user and redirects to the tenant', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'newcomer@example.com']);
    $invite = $this->service->send($tenant, 'newcomer@example.com', TenantRole::Admin);

    $this->actingAs($user)
        ->post('/invites/'.$invite->token.'/accept')
        ->assertRedirect(tenant_url('/', $tenant));

    $this->assertDatabaseHas('tenant_user', [
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'role' => 'admin',
    ]);
});

it('POST /invites/{token}/accept renders Mismatch when user email does not match invite', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'other@example.com']);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    $this->actingAs($user)
        ->post('/invites/'.$invite->token.'/accept')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('invites/Mismatch')
            ->where('invited_email', 'newcomer@example.com')
            ->where('current_email', 'other@example.com')
        );

    $this->assertDatabaseMissing('tenant_user', ['tenant_id' => $tenant->id, 'user_id' => $user->id]);
});

it('POST /invites/{token}/decline deletes the invite and redirects home', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    $this->post('/invites/'.$invite->token.'/decline')
        ->assertRedirect(route('home'));

    $this->assertDatabaseMissing('tenant_invites', ['id' => $invite->id]);
});

it('POST /invites/{token}/decline renders Expired when invite is not pending', function () {
    $this->post('/invites/nonexistent-token/decline')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('invites/Expired'));
});
