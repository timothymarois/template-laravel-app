<?php

declare(strict_types=1);

it('reports the deployed release version without caching it', function (): void {
    config()->set('release.version', '1.2.3');

    $response = $this->getJson(route('release.version'));

    $response
        ->assertOk()
        ->assertExactJson(['version' => '1.2.3']);

    expect($response->headers->get('Cache-Control'))->toContain('no-store');
});
