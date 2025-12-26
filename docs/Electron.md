# Electron Desktop Build Documentation

This starter kit includes optional Electron desktop build support for creating native macOS and Windows applications from your Laravel project.

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Setup](#setup)
- [Rollback](#rollback)
- [Configuration](#configuration)
- [Services](#services)
- [Building](#building)
- [Development Workflow](#development-workflow)
- [Binary Management](#binary-management)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

## Overview

The Electron build feature allows you to package your Laravel application as a standalone desktop application. Each desktop app includes:

- **Bundled PHP**: A static PHP binary that runs your Laravel backend
- **Bundled Redis** (optional): A Redis server for caching and queues
- **Native UI**: Your Inertia.js frontend runs in an Electron window
- **Service Orchestration**: Automatic startup/shutdown of all required services

## Architecture

### Boot Sequence

When the Electron app starts, the boot manager orchestrates service startup:

```
Progress | Step              | Description
---------|-------------------|----------------------------------
5%       | Initialize        | Create runtime directories
10%      | Cleanup           | Kill stale processes from PIDs
15%      | Database          | Create/verify SQLite, run migrations
20%      | Ports             | Find available ports
25%      | Environment       | Write runtime .env values
35%      | Redis             | Start Redis (if enabled)
50%      | Laravel           | Start `php artisan serve`
65%      | Health            | Wait for Laravel health check
75%      | Reverb            | Start Reverb (if enabled)
85%      | Horizon           | Start Horizon (if enabled/Redis)
90%      | Scheduler         | Start scheduler (if enabled)
100%     | Ready             | Open main window
```

### Service Dependencies

```
Laravel (Required)
├── Database (SQLite or MySQL)
├── Redis (Optional)
│   └── Horizon (Optional, requires Redis)
├── Reverb (Optional)
└── Scheduler (Optional)
```

### Data Storage

- **macOS**: `~/Library/Application Support/{bundle-id}/`
- **Windows**: `%APPDATA%/{app-name}/`

Storage includes:
- SQLite database
- Laravel storage directory
- Log files
- Process PID file

## Setup

**Important:** Electron build should only be enabled on a **new project**. The setup process modifies `package.json` and adds Electron-specific configuration.

### Prerequisites

- Node.js 18+ installed
- npm or pnpm package manager
- For building: Xcode (macOS) or Visual Studio Build Tools (Windows)

### Enabling Electron Build

Run the setup command:

```bash
php artisan build:electron
```

This command will:
1. Prompt for service configuration (database, Redis, Horizon, Reverb, Scheduler)
2. Install Electron npm dependencies
3. Create the `electron/` directory structure
4. Publish TypeScript source files
5. Create configuration files
6. Update `package.json` with Electron scripts

### Options

```bash
# Skip interactive prompts, use defaults
php artisan build:electron --force

# Skip npm dependency installation
php artisan build:electron --skip-npm

# Remove Electron and restore original state
php artisan build:electron --rollback
```

### Post-Setup Steps

1. **Download platform binaries:**
   ```bash
   pnpm setup:binaries     # macOS
   # or
   pnpm setup:binaries:win # Windows
   ```

2. **Add application icons:**
   - `electron/resources/icon.icns` - macOS icon
   - `electron/resources/icon.ico` - Windows icon

3. **Generate application key for production:**
   ```bash
   php artisan key:generate --show
   ```
   Add this to `.env.production`

4. **Test in development mode:**
   ```bash
   pnpm electron:dev
   ```

## Rollback

To completely remove Electron configuration and restore the original state:

```bash
php artisan build:electron --rollback
```

This will:
1. Remove `config/electron.php`
2. Remove `ElectronServiceProvider`
3. Delete `electron/` directory
4. Remove build scripts
5. Restore original `package.json`
6. Restore original `bootstrap/providers.php`
7. Remove npm dependencies

Use `--force` to skip confirmation:
```bash
php artisan build:electron --rollback --force
```

## Configuration

### config/electron.php

```php
return [
    'app' => [
        'name' => env('ELECTRON_APP_NAME', env('APP_NAME', 'Laravel')),
        'bundle_id' => env('ELECTRON_BUNDLE_ID', 'com.example.app'),
    ],

    'services' => [
        'database' => [
            'driver' => env('ELECTRON_DB_DRIVER', 'sqlite'),
            'external' => env('ELECTRON_DB_EXTERNAL', false),
        ],
        'redis' => [
            'enabled' => env('ELECTRON_REDIS_ENABLED', true),
            'external' => env('ELECTRON_REDIS_EXTERNAL', false),
        ],
        'horizon' => [
            'enabled' => env('ELECTRON_HORIZON_ENABLED', true),
        ],
        'reverb' => [
            'enabled' => env('ELECTRON_REVERB_ENABLED', false),
        ],
        'scheduler' => [
            'enabled' => env('ELECTRON_SCHEDULER_ENABLED', false),
        ],
    ],

    'ports' => [
        'laravel' => env('ELECTRON_PORT_LARAVEL', 48000),
        'redis' => env('ELECTRON_PORT_REDIS', 6379),
        'reverb' => env('ELECTRON_PORT_REVERB', 48080),
    ],
];
```

### Environment Variables

Add to `.env` for development or `.env.production` for builds:

```env
# Application Identity
ELECTRON_APP_NAME=MyApp
ELECTRON_BUNDLE_ID=com.example.myapp

# Database
ELECTRON_DB_DRIVER=sqlite

# Services
ELECTRON_REDIS_ENABLED=true
ELECTRON_HORIZON_ENABLED=true
ELECTRON_REVERB_ENABLED=false
ELECTRON_SCHEDULER_ENABLED=false

# Ports
ELECTRON_PORT_LARAVEL=48000
ELECTRON_PORT_REDIS=6379
ELECTRON_PORT_REVERB=48080
```

## Services

### Laravel (Required)

The Laravel server is always started using `php artisan serve`. In development, it uses Herd's PHP. In production, it uses the bundled PHP binary.

### SQLite Database (Default)

When using SQLite (default):
- Database is created in the user's Application Support directory
- Migrations run automatically on first launch
- No external database server required

### MySQL Database (Optional)

Set `ELECTRON_DB_DRIVER=mysql` for MySQL:
- Requires external MySQL server
- User must configure connection details
- Useful for apps that need multi-user database access

### Redis (Optional)

When enabled:
- A bundled Redis server starts on the configured port
- Used for caching and queue backend
- In dev mode, uses existing Redis if available (e.g., Herd Redis)

### Horizon (Optional)

When enabled:
- Requires Redis to be enabled
- Processes queued jobs in the background
- Skipped in dev mode if Redis is not available

### Reverb (Optional)

When enabled:
- Starts WebSocket server for real-time features
- Required for Laravel Echo / broadcasting

### Scheduler (Optional)

When enabled:
- Runs `php artisan schedule:work` in production
- Executes scheduled tasks defined in `app/Console/Kernel.php`

## Building

### Development Build

```bash
# Run in development mode (hot reload, dev tools)
pnpm electron:dev
```

### Production Build

```bash
# Build for current platform
pnpm electron:build

# Build for macOS only
pnpm electron:build:mac

# Build for Windows only
pnpm electron:build:win

# Build unpacked directory (faster, for testing)
pnpm electron:build:dir
```

Build outputs are placed in `dist-electron/`.

### Code Signing (macOS)

For distribution outside the App Store:

1. Get a Developer ID certificate from Apple
2. Set environment variables:
   ```bash
   export CSC_LINK=/path/to/certificate.p12
   export CSC_KEY_PASSWORD=your-password
   ```
3. Build: `pnpm electron:build:mac`

### Code Signing (Windows)

For distribution:

1. Get a code signing certificate
2. Set environment variables:
   ```bash
   set CSC_LINK=C:\path\to\certificate.pfx
   set CSC_KEY_PASSWORD=your-password
   ```
3. Build: `pnpm electron:build:win`

## Development Workflow

### Running in Development

1. Start your Laravel development server (optional, Electron will start its own):
   ```bash
   php artisan serve
   ```

2. Run Electron in development mode:
   ```bash
   pnpm electron:dev
   ```

### Hot Reload

- Laravel changes: Restart Electron app
- Frontend changes: Automatic via Vite HMR (if dev server running)
- Electron main process changes: Restart Electron app

### Debugging

- DevTools: `View > Toggle Developer Tools` (dev mode only)
- Main process logs: Visible in terminal running `electron:dev`
- Renderer process: Use DevTools console

## Binary Management

### PHP (static-php-cli)

The setup script downloads a static PHP binary:

- **Source**: https://dl.static-php.dev/static-php-cli/
- **Version**: 8.4.x
- **Platforms**: macOS arm64, macOS x64, Windows x64

### Redis

Redis is either:
- Copied from Homebrew installation (macOS)
- Downloaded from GitHub releases (Windows)

### Updating Binaries

1. Edit `scripts/setup-binaries.sh` (or `.ps1` for Windows)
2. Update version numbers
3. Run `pnpm setup:binaries`

### Manual Binary Setup

If automatic download fails:

1. Download PHP from static-php-cli
2. Download Redis from redis.io
3. Place binaries in `electron/resources/bin/{platform}-{arch}/`

## Testing

### Running Tests

```bash
# Run all Electron tests
php artisan test --filter=Electron

# Run specific test class
php artisan test --filter=ElectronSetupTest
php artisan test --filter=ElectronRollbackTest
php artisan test --filter=ElectronIdempotencyTest

# Run including slow TypeScript compilation tests
php artisan test --filter=Electron --group=slow
```

### Test Categories

- **ElectronSetupTest**: Verifies setup creates all required files
- **ElectronRollbackTest**: Verifies rollback removes all files
- **ElectronConfigurationTest**: Verifies config is correctly written
- **ElectronIdempotencyTest**: Verifies setup/rollback cycles work
- **ElectronTypeScriptTest**: Verifies TypeScript files compile

## Troubleshooting

### Boot Failed: "PHP not found"

Ensure binaries are set up:
```bash
pnpm setup:binaries
```

### Boot Failed: "Port in use"

The boot manager automatically finds available ports. If issues persist:
- Check for zombie processes: `lsof -i :48000`
- Kill existing processes: `pkill -f "artisan serve"`

### macOS: "App can't be opened because it is from an unidentified developer"

- Enable in System Preferences > Security & Privacy
- Or build with code signing enabled

### Windows: "Windows protected your PC"

- Click "More info" then "Run anyway"
- Or build with code signing enabled

### Database Not Persisting

SQLite database location:
- macOS: `~/Library/Application Support/{bundle-id}/database/`
- Windows: `%APPDATA%/{app-name}/database/`

### Redis Connection Failed

- Check if Redis is enabled in config
- Check if port 6379 is available
- In dev mode, ensure Herd Redis is running or bundled Redis can start

### Horizon Not Processing Jobs

- Ensure Redis is enabled and running
- Check Horizon logs in Laravel logs
- Verify queue connection is set to Redis
