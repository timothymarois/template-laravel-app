<?php

declare(strict_types=1);

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\TenantInvite;
use App\Notifications\Tenancy\TenantInvitationNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;

/*
|--------------------------------------------------------------------------
| TenantInvitationNotification — rendered email content
|--------------------------------------------------------------------------
|
| Constructs the notification with fixture invite + tenant models and
| inspects the MailMessage that toMail() returns. We don't actually send
| email — just verify subject, body lines, and action URL.
|
| The action URL uses url('/invites/{token}') so this test is independent of
| route registration and TENANCY_ENABLED state.
|
*/

function makeNotification(array $inviteAttrs = [], array $tenantAttrs = []): TenantInvitationNotification
{
    $tenant = new Tenant;
    $tenant->id = $tenantAttrs['id'] ?? 'tenant-uuid-1';
    $tenant->data = array_merge(['name' => 'Acme Corp'], $tenantAttrs);

    $invite = new TenantInvite;
    $invite->setRawAttributes(array_merge([
        'id' => 1,
        'tenant_id' => $tenant->id,
        'email' => 'invitee@example.com',
        'role' => TenantRole::Member->value,
        'token' => 'abc123token',
        'invited_by_user_id' => null,
        'expires_at' => Carbon::parse('2026-12-01 10:00:00')->toDateTimeString(),
        'accepted_at' => null,
        'created_at' => Carbon::now()->toDateTimeString(),
        'updated_at' => Carbon::now()->toDateTimeString(),
    ], $inviteAttrs));

    return new TenantInvitationNotification($invite, $tenant);
}

it('via() returns the mail channel', function () {
    $notification = makeNotification();
    expect($notification->via(new AnonymousNotifiable))->toBe(['mail']);
});

it('toMail() returns a MailMessage', function () {
    $message = makeNotification()->toMail(new AnonymousNotifiable);
    expect($message)->toBeInstanceOf(MailMessage::class);
});

it('toMail() subject includes the tenant name', function () {
    $message = makeNotification()->toMail(new AnonymousNotifiable);
    expect($message->subject)->toContain('Acme Corp');
});

it('toMail() falls back to tenant id when data.name is missing', function () {
    $tenant = new Tenant;
    $tenant->id = 'fallback-tenant-uuid';
    // No data['name']
    $tenant->data = [];

    $invite = new TenantInvite;
    $invite->setRawAttributes([
        'id' => 1, 'tenant_id' => $tenant->id, 'email' => 'x@y.z',
        'role' => 'member', 'token' => 'tok', 'invited_by_user_id' => null,
        'expires_at' => Carbon::now()->addDays(7)->toDateTimeString(),
        'accepted_at' => null,
        'created_at' => Carbon::now()->toDateTimeString(),
        'updated_at' => Carbon::now()->toDateTimeString(),
    ]);

    $message = (new TenantInvitationNotification($invite, $tenant))->toMail(new AnonymousNotifiable);

    expect($message->subject)->toContain('fallback-tenant-uuid');
});

it('toMail() body mentions the role being granted', function () {
    $message = makeNotification(['role' => TenantRole::Admin->value])->toMail(new AnonymousNotifiable);
    $body = implode("\n", $message->introLines);
    expect($body)->toContain('admin');
});

it('toMail() action button points at the invites.show route with the token', function () {
    $message = makeNotification(['token' => 'unique-test-token-xyz'])->toMail(new AnonymousNotifiable);

    // MailMessage exposes the action via $message->actionText and $message->actionUrl
    expect($message->actionText)->toBe('Accept invitation');
    expect($message->actionUrl)->toContain('/invites/unique-test-token-xyz');
});

it('toMail() body mentions the expiry date', function () {
    $message = makeNotification()->toMail(new AnonymousNotifiable);
    $body = implode("\n", array_merge($message->introLines, $message->outroLines));
    // The fixture sets expires_at to 2026-12-01 10:00:00 — the rendered
    // text uses toDayDateTimeString format (e.g. "Tue, Dec 1, 2026 ...").
    expect($body)->toContain('Dec 1, 2026');
});
