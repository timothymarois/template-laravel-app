<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| sitemap:generate
|--------------------------------------------------------------------------
|
| Every branch of shouldIncludeRoute(). A sitemap is published to crawlers, so
| the cost of a wrong answer is asymmetric: a missing page is invisible, and a
| wrongly included one advertises an auth screen, a 5xx endpoint, or a route
| that only exists locally.
|
*/

function generatedSitemap(): string
{
    $path = public_path('sitemap.xml');
    $existing = file_exists($path) ? file_get_contents($path) : null;

    test()->artisan('sitemap:generate')->assertSuccessful();

    $xml = file_get_contents($path);

    $existing === null ? @unlink($path) : file_put_contents($path, $existing);

    return $xml;
}

function hasLoc(string $xml, string $path): bool
{
    return str_contains($xml, '<loc>'.rtrim(config('app.url'), '/').$path.'</loc>');
}

it('includes the home page', function () {
    expect(hasLoc(generatedSitemap(), ''))->toBeTrue();
});

it('includes a plain public GET route', function () {
    Route::get('about-us', fn () => 'ok')->name('about');

    expect(hasLoc(generatedSitemap(), '/about-us'))->toBeTrue();
});

it('excludes non-GET routes', function () {
    Route::post('webhooks/stripe', fn () => 'ok')->name('webhooks.stripe');

    expect(hasLoc(generatedSitemap(), '/webhooks/stripe'))->toBeFalse();
});

it('excludes authenticated routes', function () {
    Route::get('members', fn () => 'ok')->middleware('auth')->name('members');
    Route::get('dashboard', fn () => 'ok')->middleware('auth:sanctum')->name('dash');

    $xml = generatedSitemap();

    expect(hasLoc($xml, '/members'))->toBeFalse()
        ->and(hasLoc($xml, '/dashboard'))->toBeFalse();
});

it('excludes guest-only routes', function () {
    // Login and register are guest-gated: indexable, but thin and useless in
    // results, and they are the pages a crawler most often mistakes for content.
    Route::get('sign-in', fn () => 'ok')->middleware('guest')->name('signin');

    expect(hasLoc(generatedSitemap(), '/sign-in'))->toBeFalse();
});

it('excludes routes carrying parameters', function () {
    // A URI template is not a URL. Real ones belong in addDynamicPages().
    Route::get('posts/{post}', fn () => 'ok')->name('posts.show');

    $xml = generatedSitemap();

    expect(str_contains($xml, '{post}'))->toBeFalse()
        ->and(str_contains($xml, '/posts/'))->toBeFalse();
});

it('excludes every configured utility prefix', function () {
    $xml = generatedSitemap();

    // /health answers 503 whenever a check fails, so listing it would put a 5xx
    // URL in the sitemap; /release is JSON; _inertia is local-only dev tooling.
    foreach (['/health', '/release', '/up'] as $path) {
        expect(hasLoc($xml, $path))->toBeFalse();
    }

    expect(str_contains($xml, '_inertia'))->toBeFalse()
        ->and(str_contains($xml, '_debugbar'))->toBeFalse();
});

it('excludes the shipped auth routes by name', function () {
    $xml = generatedSitemap();

    foreach (['/login', '/register', '/forgot-password'] as $path) {
        expect(hasLoc($xml, $path))->toBeFalse();
    }

    expect(str_contains($xml, 'reset-password'))->toBeFalse();
});

it('excludes a route matching a wildcard name pattern', function () {
    // `password.*` is a wildcard entry; this proves the pattern branch, not just
    // the exact-name one the shipped routes happen to hit.
    Route::get('password/anything', fn () => 'ok')->name('password.anything');

    expect(hasLoc(generatedSitemap(), '/password/anything'))->toBeFalse();
});

it('excludes the admin surface', function () {
    expect(str_contains(generatedSitemap(), '/admin'))->toBeFalse();
});

it('writes a well-formed urlset', function () {
    $xml = generatedSitemap();

    expect($xml)->toStartWith('<?xml')
        ->and($xml)->toContain('http://www.sitemaps.org/schemas/sitemap/0.9');

    // Every <loc> must be an absolute URL — a relative one is silently ignored.
    preg_match_all('/<loc>(.*?)<\/loc>/', $xml, $matches);

    expect($matches[1])->not->toBeEmpty();

    foreach ($matches[1] as $loc) {
        expect($loc)->toStartWith('http');
    }
});
