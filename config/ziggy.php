<?php

/**
 * Ziggy routes
 * https://github.com/tighten/ziggy?tab=readme-ov-file#includingexcluding-routes
 */
return [
    // 'only' => ['home', 'posts.index', 'posts.show'],
    'except' => ['_debugbar.*', 'horizon.*', 'admin.*', 'log-viewer.*', 'sanctum'],
];
