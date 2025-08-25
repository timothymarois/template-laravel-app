# Documentation Sync

This command pulls documentation from other Atlas repositories into the local project.

## Usage

Configure the repositories in `config/atlas_docs.php`:

```php
return [
    'delete' => true,
    'repos' => [
        [
            'repo' => 'atlasphp/atlas-laravel',
            'paths' => [
                ['path' => 'docs/components', 'output' => 'docs/ui'],
                ['path' => 'docs/README.md', 'output' => 'docs/ui'],
            ],
            'ignore' => ['ignore.md'],
        ],
    ],
];
```

Run the sync:

```bash
php artisan atlas:sync-docs
```

Each defined `path` is copied from the source repository into its `output` directory. Directories are nested under the output path by their basename, while individual files are placed directly under the output. Files matching `ignore` patterns are skipped.

Output directories are deleted before syncing to remove outdated files. Set `delete` to `false` globally or per repository to preserve existing files.
