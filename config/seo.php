<?php

declare(strict_types=1);

return [
    /*
     * Defaults for the document head, used whenever a page does not set its own.
     * Shared to every Inertia page by HandleInertiaRequests and consumed by the
     * SeoHead component.
     */
    'site_name' => env('SEO_SITE_NAME', env('APP_NAME', 'Laravel')),

    'description' => env('SEO_DESCRIPTION', ''),

    /*
     * Path or absolute URL to the default social sharing image. A relative path is
     * resolved against APP_URL. 1200x630 is the size every network crops well.
     */
    'image' => env('SEO_IMAGE', ''),

    /*
     * Whether crawlers may index this deployment. False sends
     * `X-Robots-Tag: noindex, nofollow` on every response — the correct way to keep
     * a staging environment out of the index. A robots.txt Disallow does not
     * prevent indexing, it only prevents crawling, so a linked page still appears.
     */
    'indexable' => env('SEO_INDEXABLE', env('APP_ENV') === 'production'),
];
