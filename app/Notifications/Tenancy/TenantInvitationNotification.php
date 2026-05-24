<?php

declare(strict_types=1);

namespace App\Notifications\Tenancy;

use App\Models\Tenant;
use App\Models\TenantInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email notification sent to invitees with an acceptance link.
 *
 * Renders via Laravel's default MailMessage template — forks can swap in a
 * custom Markdown view by extending this class. The acceptance URL is on
 * the central domain at /invites/{token}.
 */
class TenantInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly TenantInvite $invite,
        private readonly Tenant $tenant,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tenantName = (string) ($this->tenant->data['name'] ?? $this->tenant->id);
        // The /invites/{token} path is registered in routes/web.php behind the
        // tenancy-enabled gate. Using url() (vs route('invites.show', ...)) keeps
        // the notification working in unit tests that don't load all routes.
        $acceptUrl = url('/invites/'.$this->invite->token);
        $role = $this->invite->role->value;

        return (new MailMessage)
            ->subject("You're invited to join {$tenantName}")
            ->line("You've been invited to join \"{$tenantName}\" as a {$role}.")
            ->action('Accept invitation', $acceptUrl)
            ->line('This invitation expires on '.$this->invite->expires_at->toDayDateTimeString().'.')
            ->line("If you weren't expecting this invitation, you can safely ignore this email.");
    }
}
