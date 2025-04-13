<?php

use SoloTerm\Solo\Commands\Command;
use SoloTerm\Solo\Commands\EnhancedTailCommand;
use SoloTerm\Solo\Commands\MakeCommand;
use SoloTerm\Solo\Hotkeys;
use SoloTerm\Solo\Themes;

// Solo may not (should not!) exist in prod, so we have to
// check here first to see if it's installed.
if (! class_exists('\SoloTerm\Solo\Manager')) {
    return [
        //
    ];
}

return [
    /*
    |--------------------------------------------------------------------------
    | Themes
    |--------------------------------------------------------------------------
    */
    'theme' => env('SOLO_THEME', 'dark'),

    'themes' => [
        'light' => Themes\LightTheme::class,
        'dark' => Themes\DarkTheme::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Keybindings
    |--------------------------------------------------------------------------
    */
    'keybinding' => env('SOLO_KEYBINDING', 'default'),

    'keybindings' => [
        'default' => Hotkeys\DefaultHotkeys::class,
        'vim' => Hotkeys\VimHotkeys::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    |
    */
    'commands' => [
        // 'Dev' => Command::from('npm run dev')->lazy(),
        'Horizon' => Command::from('php artisan horizon')->lazy(),
        // 'Schedule' => Command::from('php artisan schedule:run')->lazy(),
        'SSR' => Command::from('herd php artisan inertia:start-ssr')->lazy(),
        // 'Stripe' => Command::from('stripe listen --forward-to https://LOCATION')->lazy(),
        // 'Logs' => EnhancedTailCommand::file(storage_path('logs/laravel.log'))->lazy(),
        // 'HTTP' => 'php artisan serve',
        // Lazy commands do no automatically start when Solo starts.
        // 'Dumps' => Command::from('php artisan solo:dumps')->lazy(),
        // 'Reverb' => Command::from('php artisan reverb')->lazy(),
        'Pint' => Command::from('php ./vendor/bin/pint')->lazy(),
        'ESlint' => Command::from('npm run eslint')->lazy(),
        'Test' => Command::from('php artisan test')->lazy(),
        'Build-SSR' => Command::from('npm run build-ssr')->lazy(),
        'Migrations' => Command::from('php artisan migrate')->lazy(),
        'Fresh' => Command::from('php artisan start:fresh')->lazy(),
        // 'Larastan' => Command::from('php artisan solo:larastan')->lazy(),
        // 'Tests' => Command::from('php artisan test --colors=always')->lazy(),
        // 'Commands' => Command::from('php artisan command-helper')->interactive(),
        'Make' => new MakeCommand,
        // 'About' => 'php artisan solo:about',

        // 'About' => 'php artisan solo:about',
        // 'Logs' => EnhancedTailCommand::file(storage_path('logs/laravel.log')),
        // 'Vite' => 'npm run dev',
        // 'Make' => new MakeCommand,
        // // 'HTTP' => 'php artisan serve',

        // // Lazy commands do no automatically start when Solo starts.
        // 'Dumps' => Command::from('php artisan solo:dumps')->lazy(),
        // 'Reverb' => Command::from('php artisan reverb:start --debug')->lazy(),
        // 'Pint' => Command::from('./vendor/bin/pint --ansi')->lazy(),
        // 'Queue' => Command::from('php artisan queue:work')->lazy(),
        // 'Tests' => Command::from('php artisan test --colors=always')->withEnv(['APP_ENV' => 'testing'])->lazy(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous
    |--------------------------------------------------------------------------
    */

    /*
     * If you run the solo:dumps command, Solo will start a server to receive
     * the dumps. This is the address. You probably don't need to change
     * this unless the default is already taken for some reason.
     */
    'dump_server_host' => env('SOLO_DUMP_SERVER_HOST', 'tcp://127.0.0.1:9984'),
];
