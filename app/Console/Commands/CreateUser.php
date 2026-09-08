<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\Models\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Create a user from the console, optionally as an operator.
 *
 * Exists because the admin surface is gated on the operator role: without a way
 * to make one, a fresh install or a fork that just adopted the gate can reach
 * `/admin` with nobody at all. Promotion by hand through tinker works but is
 * easy to get wrong, and is not something to talk somebody through in a
 * migration guide.
 */
class CreateUser extends Command
{
    protected $signature = 'user:create
                            {--name= : The user\'s name}
                            {--email= : The user\'s email address}
                            {--password= : The password; generated and printed when omitted}
                            {--admin : Create the user as an operator with admin access}';

    protected $description = 'Create a user, optionally an admin';

    public function __construct(
        private readonly UserService $userService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email address');

        // Generated when omitted so a non-interactive caller never has to invent
        // one, and never has to pass a real password on a shell command line
        // where it lands in the history file.
        $generated = $this->option('password') === null;
        $password = $this->option('password') ?: str()->password(16);

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return Command::FAILURE;
        }

        $user = $this->userService->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $this->option('admin') ? UserRole::SuperAdmin : UserRole::default(),
        ]);

        $this->info(sprintf('Created %s <%s> as %s.', $user->name, $user->email, $user->role->label()));

        if ($generated) {
            $this->line('');
            $this->warn('Generated password (shown once): '.$password);
        }

        if (! $user->isAdmin()) {
            $this->line('');
            $this->comment('This user cannot reach /admin. Pass --admin to create an operator.');
        }

        return Command::SUCCESS;
    }
}
