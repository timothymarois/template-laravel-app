<?php

use SoloTerm\Solo\Commands\Command;
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
        'SSR' => Command::from('herd php artisan inertia:start-ssr')->lazy(),
        'Queue' => Command::from('php artisan horizon')->lazy(),
        'Reverb' => Command::from('php artisan reverb:start')->lazy(),
        'Schedule' => Command::from('php artisan schedule:work')->lazy(),
        'Check:JS' => Command::from('pnpm check:js')->lazy(),
        'Check:PHP' => Command::from('pnpm check:php')->withEnv(['APP_ENV' => 'testing'])->lazy(),
        'Clear' => Command::from('php artisan optimize:clear')->lazy(),
        'Migrations' => Command::from('php artisan migrate')->lazy(),
        'Fresh' => Command::from('php artisan start:fresh')->lazy(),
        'Make' => new MakeCommand,
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
