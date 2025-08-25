# AGENTS Instructions

This repository uses Laravel for the PHP backend and Vue 3 with Inertia for the frontend. Follow the standards outlined in the project documentation.

## Guidelines
- For PHP and Laravel code, follow the [Laravel Guide](./docs/laravel-standards/docs/laravel-guide.md).
- For Vue components and frontend work, follow the [Vue Guide](./docs/laravel-standards/docs/vue-guide.md).
- Review the [Agents Guide](./docs/laravel-standards/docs/agents-guide.md) for more details.

## Required Checks
Run these commands before committing changes:
- Run `npm run eslint`

Run these if you've modified any php code, you will need to run `composer install` first.

- Run `./vendor/bin/pint`
- Run `php artisan test`
