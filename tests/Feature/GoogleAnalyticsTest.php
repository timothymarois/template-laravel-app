<?php

declare(strict_types=1);

it('emits the gtag.js snippet when GOOGLE_ANALYTICS_ID is configured', function () {
    config(['services.google_analytics.measurement_id' => 'G-TESTABC123']);

    $this->withoutVite();
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TESTABC123', false);
    $response->assertSee("gtag('config', \"G-TESTABC123\")", false);
});

it('omits the gtag.js snippet when GOOGLE_ANALYTICS_ID is empty', function () {
    config(['services.google_analytics.measurement_id' => null]);

    $this->withoutVite();
    $response = $this->get('/');

    $response->assertOk();
    $response->assertDontSee('googletagmanager.com', false);
});
