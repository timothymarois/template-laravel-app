# Installation

Get up and running in 5 minutes.

## Prerequisites

- PHP 8.4+
- Node.js 20+
- pnpm
- Composer
- Redis (for queues and caching)

We recommend [Laravel Herd](https://herd.laravel.com/) for local development—it provides PHP, nginx, and database services with zero configuration.

## Quick Start

```bash
# Clone and install
git clone <repo-url> my-app && cd my-app
pnpm install
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations and start
php artisan migrate
pnpm dev
```

## Development Server

The `pnpm dev` command starts Vite in development mode with hot module replacement.

For a complete development environment with all services, use Solo:

```bash
php artisan solo
```

This runs all development processes in a single terminal UI:
- Vite dev server
- SSR server
- Queue worker
- Reverb WebSocket server
- Scheduler

See `config/solo.php` for configuration options.

## Verifying Installation

After installation, you should be able to:

1. Visit your application URL and see the home page
2. Register a new user account
3. Log in and access the admin dashboard at `/admin`

## Troubleshooting

### Missing dependencies

```bash
composer install
pnpm install
```

### Database connection errors

Ensure your `.env` file has correct database credentials and the database exists.

### Redis connection errors

Make sure Redis is running. With Laravel Herd, Redis is included. Otherwise, start Redis manually:

```bash
redis-server
```

### Permission errors

Ensure storage and cache directories are writable:

```bash
chmod -R 775 storage bootstrap/cache
```
