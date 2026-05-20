# Configuration

Configure your environment and application settings.

## Environment Setup

Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

## Essential Settings

### Application

```bash
APP_NAME="My Application"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://my-app.test
```

### Database

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_app
DB_USERNAME=root
DB_PASSWORD=
```

### Redis

Required for queues and caching:

```bash
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Queue

```bash
QUEUE_CONNECTION=redis
```

## Server-Side Rendering

SSR is enabled by default for better SEO and faster initial loads.

```bash
INERTIA_SSR_ENABLED=true
INERTIA_SSR_URL=http://127.0.0.1:13714
```

To disable SSR:

```bash
INERTIA_SSR_ENABLED=false
```

## WebSockets (Reverb)

Configure real-time communication:

```bash
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=localhost
REVERB_PORT=8080
```

## Security Settings

### CORS

Set allowed origins for production:

```bash
CORS_ALLOWED_ORIGINS=https://example.com,https://api.example.com
```

### Session

```bash
SESSION_DRIVER=redis
SESSION_LIFETIME=120
```

## Production Configuration

For production deployments, ensure:

```bash
APP_ENV=production
APP_DEBUG=false
```

Run cache commands after deployment:

```bash
php artisan optimize           # Cache config, routes, events
php artisan view:cache         # Compile Blade views
```

## Configuration Files

Key configuration files to review:

| File | Purpose |
|------|---------|
| `config/app.php` | Application settings |
| `config/inertia.php` | Inertia and SSR settings |
| `config/sanctum.php` | Authentication settings |
| `config/horizon.php` | Queue dashboard settings |
| `config/solo.php` | Dev runner settings |
