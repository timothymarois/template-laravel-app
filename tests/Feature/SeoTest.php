<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Document head and crawlability
|--------------------------------------------------------------------------
|
| Nothing covered the head before, which is how three defects survived: two
| <title> tags (parsers take the first, so every page was titled APP_NAME), an
| og:url that was always empty because it was built from window.location in a
| render that has no window, and utility endpoints in the sitemap.
|
*/

it('never emits more than one title tag', function () {
    // Two were emitted before: one hardcoded in the Blade root, one from
    // @inertiaHead. Parsers take the first, so every page was titled APP_NAME.
    // Under SSR the count is 1; with SSR off the head is set on the client and
    // the count is 0. Two is the failure, and it is what this guards.
    expect(substr_count($this->get('/')->getContent(), '<title'))->toBeLessThanOrEqual(1);
});

it('does not hardcode a title in the root template', function () {
    expect(file_get_contents(resource_path('views/app.blade.php')))
        ->not->toContain('<title>')
        ->not->toContain('<title ');
});

it('shares an absolute app url for the head to build canonical urls from', function () {
    config()->set('app.url', 'https://example.test/');

    $this->get('/')->assertInertia(fn ($page) => $page
        // Trailing slash trimmed, or every canonical would carry a double slash.
        ->where('appUrl', 'https://example.test'));
});

it('shares the configured seo defaults', function () {
    config()->set('seo.site_name', 'Acme');
    config()->set('seo.description', 'We make things.');

    $this->get('/')->assertInertia(fn ($page) => $page
        ->where('seo.siteName', 'Acme')
        ->where('seo.description', 'We make things.'));
});

it('sends X-Robots-Tag when the deployment is not indexable', function () {
    config()->set('seo.indexable', false);

    $this->get('/')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

it('does not send X-Robots-Tag when the deployment is indexable', function () {
    config()->set('seo.indexable', true);

    expect($this->get('/')->headers->has('X-Robots-Tag'))->toBeFalse();
});

it('keeps utility and auth endpoints out of the sitemap', function () {
    $path = public_path('sitemap.xml');
    $existing = file_exists($path) ? file_get_contents($path) : null;

    $this->artisan('sitemap:generate')->assertSuccessful();

    $xml = file_get_contents($path);

    // /health answers 503 whenever a check fails, so listing it would put a 5xx
    // URL in the sitemap. /release is JSON. _inertia is the dev-tools route.
    foreach (['/health', '/release', '/_inertia', '/login', '/register', '/admin'] as $path_) {
        expect($xml)->not->toContain('<loc>'.config('app.url')."{$path_}</loc>");
    }

    expect($xml)->toContain('<loc>'.config('app.url').'</loc>');

    $existing === null ? @unlink($path) : file_put_contents($path, $existing);
});
