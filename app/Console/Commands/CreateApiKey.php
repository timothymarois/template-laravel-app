<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ApiAbility;
use App\Models\User;
use App\Services\ApiKeyService;
use Illuminate\Console\Command;
use InvalidArgumentException;

/**
 * Issue an API key from the console.
 *
 * The admin screen is the usual route, but a key is often wanted where there is
 * no browser: seeding an environment, provisioning CI, or scripting a deploy.
 * The plaintext is printed once here for the same reason it is shown once in the
 * UI — only its hash is stored, so there is no second chance to read it.
 */
class CreateApiKey extends Command
{
    protected $signature = 'api-key:create
                            {--user= : Email of the user the key belongs to}
                            {--name= : A name identifying where the key is used}
                            {--ability=* : Abilities to grant; repeatable. Defaults to api:read}
                            {--days= : Days until expiry. Defaults to the service default}
                            {--never-expires : Issue a key with no expiry}';

    protected $description = 'Issue an API key for a user';

    public function __construct(
        private readonly ApiKeyService $apiKeys,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $email = $this->option('user') ?: $this->ask('User email');

        $user = User::firstWhere('email', $email);

        if (! $user instanceof User) {
            $this->error("No user with the email [{$email}].");

            return Command::FAILURE;
        }

        if (! $user->is_active) {
            // EnsureApiKey refuses a key whose owner is inactive, so issuing one
            // here would hand over a credential that cannot authenticate.
            $this->error("[{$email}] is deactivated; a key issued to them would be refused.");

            return Command::FAILURE;
        }

        $name = $this->option('name') ?: $this->ask('Name', 'CLI');

        /** @var array<int, string> $abilities */
        $abilities = $this->option('ability') ?: [ApiAbility::Read->value];

        $lifetime = match (true) {
            (bool) $this->option('never-expires') => ApiKeyService::NEVER_EXPIRES,
            $this->option('days') !== null => (int) $this->option('days'),
            default => null,
        };

        try {
            $issued = $this->apiKeys->issue($user, $name, $abilities, $lifetime);
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());
            $this->line('Valid abilities: '.implode(', ', ApiAbility::values()));

            return Command::FAILURE;
        }

        $this->info(sprintf(
            'Issued “%s” for %s — %s, expires %s.',
            $issued->key->name,
            $user->email,
            implode(', ', $abilities),
            $issued->key->expires_at?->toDayDateTimeString() ?? 'never',
        ));

        $this->newLine();
        $this->warn('Key (shown once, only its hash is stored):');
        $this->line($issued->plainText);

        return Command::SUCCESS;
    }
}
