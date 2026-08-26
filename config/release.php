<?php

declare(strict_types=1);

$manifest = json_decode((string) file_get_contents(base_path('composer.json')), true);

return [
    'version' => is_array($manifest) && is_string($manifest['version'] ?? null)
        ? $manifest['version']
        : 'missing',
];
