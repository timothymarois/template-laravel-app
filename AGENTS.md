# AGENTS Instructions

This repository uses Laravel for the backend and Vue 3 with Inertia for the frontend. Follow the standards outlined in the project documentation.

## Guidelines
- For PHP and Laravel code, follow the [Laravel Guide](./docs/laravel-standards/docs/laravel-guide.md).
- For Vue components and frontend work, follow the [Vue Guide](./docs/laravel-standards/docs/vue-guide.md).
- Check for `AGENTS.md` files in subdirectories and adhere to their instructions.

## Required Checks
Run these commands before committing changes:
- `php artisan test`
- `./vendor/bin/phpstan analyse`
- `./vendor/bin/pint --test` (use `./vendor/bin/pint` to automatically fix formatting)
- `npm run eslint`

