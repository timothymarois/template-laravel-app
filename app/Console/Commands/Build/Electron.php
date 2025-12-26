<?php

declare(strict_types=1);

namespace App\Console\Commands\Build;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class Electron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:electron
                            {--force : Overwrite existing electron configuration}
                            {--skip-npm : Skip installing npm dependencies}
                            {--rollback : Remove electron and restore original state}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up Electron desktop app build for this application';

    /**
     * The filesystem instance.
     */
    protected Filesystem $files;

    /**
     * The stub directory path.
     */
    protected string $stubPath;

    /**
     * The backup directory path.
     */
    protected string $backupPath;

    /**
     * Service selections made during setup.
     *
     * @var array<string, mixed>
     */
    protected array $services = [];

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->stubPath = base_path('stubs/electron');
        $this->backupPath = storage_path('electron-backups');
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Prevent running in production
        if (app()->isProduction()) {
            error('This command cannot be run in production!');

            return self::FAILURE;
        }

        // Handle rollback
        if ($this->option('rollback')) {
            return $this->handleRollback();
        }

        $this->newLine();
        info('Setting up Electron desktop app build...');
        $this->newLine();

        // Check if stubs directory exists
        if (! $this->files->isDirectory($this->stubPath)) {
            error('Electron stubs not found. Please ensure stubs/electron directory exists.');

            return self::FAILURE;
        }

        // Check prerequisites
        if (! $this->checkPrerequisites()) {
            return self::FAILURE;
        }

        // Check if already configured
        if ($this->isAlreadyConfigured() && ! $this->option('force')) {
            warning('Electron appears to be already configured.');

            if (! confirm('Do you want to overwrite the existing configuration?', false)) {
                info('Setup cancelled.');

                return self::SUCCESS;
            }
        }

        // Warn about new projects
        if (! $this->option('force')) {
            warning('Electron desktop build should ideally be set up on a new project.');
            warning('This will modify package.json and add electron dependencies.');

            if (! confirm('Continue with setup?', true)) {
                info('Setup cancelled.');

                return self::SUCCESS;
            }
        }

        // Run setup steps
        // Note: electron/ directory already exists with TypeScript source code
        // We only need to publish config files and update package.json
        $steps = [
            'promptServiceSelection' => 'Configuring services',
            'installNpmDependencies' => 'Installing npm dependencies',
            'createDirectories' => 'Creating directories',
            'backupOriginalFiles' => 'Backing up original files',
            'publishElectronConfig' => 'Publishing configuration',
            'publishServiceProvider' => 'Publishing service provider',
            'publishBuildScripts' => 'Publishing build scripts',
            'publishBuilderConfig' => 'Publishing electron-builder config',
            'updatePackageJson' => 'Updating package.json',
            'updateBootstrapProviders' => 'Updating bootstrap providers',
            'updateEnvExample' => 'Updating .env.example',
            'createEnvProduction' => 'Creating .env.production',
        ];

        $currentStep = 0;
        $totalSteps = count($steps);

        foreach ($steps as $method => $description) {
            $currentStep++;
            $prefix = "[{$currentStep}/{$totalSteps}]";

            if ($method === 'installNpmDependencies' && $this->option('skip-npm')) {
                note("{$prefix} Skipping: {$description}");

                continue;
            }

            $result = spin(
                fn () => $this->{$method}(),
                "{$prefix} {$description}..."
            );

            if ($result === false) {
                error("Failed: {$description}");

                return self::FAILURE;
            }
        }

        $this->newLine();
        info('Electron desktop build setup complete!');
        $this->newLine();

        $this->displayNextSteps();

        return self::SUCCESS;
    }

    /**
     * Check if required prerequisites are met.
     */
    protected function checkPrerequisites(): bool
    {
        // Check for Node.js
        $nodeVersion = shell_exec('node --version 2>/dev/null');
        if (empty($nodeVersion)) {
            error('Node.js is required but not found. Please install Node.js first.');

            return false;
        }

        // Check for npm/pnpm
        $pnpmVersion = shell_exec('pnpm --version 2>/dev/null');
        $npmVersion = shell_exec('npm --version 2>/dev/null');
        if (empty($pnpmVersion) && empty($npmVersion)) {
            error('npm or pnpm is required but not found.');

            return false;
        }

        return true;
    }

    /**
     * Check if electron is already configured.
     */
    protected function isAlreadyConfigured(): bool
    {
        // Only check for config file - electron/ directory is part of the codebase
        return $this->files->exists(config_path('electron.php'));
    }

    /**
     * Prompt for service selection.
     */
    protected function promptServiceSelection(): bool
    {
        if ($this->option('force')) {
            // Use defaults when force is set
            $this->services = [
                'database' => 'sqlite',
                'redis' => true,
                'horizon' => true,
                'reverb' => false,
                'scheduler' => false,
            ];

            return true;
        }

        $this->services['database'] = select(
            label: 'Which database driver should be used?',
            options: [
                'sqlite' => 'SQLite (bundled, self-contained)',
                'mysql' => 'MySQL (external, requires separate server)',
            ],
            default: 'sqlite'
        );

        $this->services['redis'] = confirm(
            label: 'Enable Redis for caching and queues?',
            default: true,
            hint: 'Redis will be bundled with the app'
        );

        if ($this->services['redis']) {
            $this->services['horizon'] = confirm(
                label: 'Enable Horizon queue worker?',
                default: true,
                hint: 'Requires Redis'
            );
        } else {
            $this->services['horizon'] = false;
        }

        $this->services['reverb'] = confirm(
            label: 'Enable Reverb WebSocket server?',
            default: false,
            hint: 'For real-time features'
        );

        $this->services['scheduler'] = confirm(
            label: 'Enable Laravel scheduler?',
            default: false,
            hint: 'For background tasks'
        );

        return true;
    }

    /**
     * Install npm dependencies.
     */
    protected function installNpmDependencies(): bool
    {
        $packageManager = file_exists(base_path('pnpm-lock.yaml')) ? 'pnpm' : 'npm';

        $dependencies = [
            'electron' => '^33.0.0',
            'electron-builder' => '^25.0.0',
        ];

        $devDeps = implode(' ', array_map(
            fn ($pkg, $ver) => "{$pkg}@{$ver}",
            array_keys($dependencies),
            array_values($dependencies)
        ));

        $command = $packageManager === 'pnpm'
            ? "pnpm add -D {$devDeps}"
            : "npm install -D {$devDeps}";

        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            base_path()
        );

        if (is_resource($process)) {
            fclose($pipes[0]);
            stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);

            return $exitCode === 0;
        }

        return false;
    }

    /**
     * Create necessary directories.
     */
    protected function createDirectories(): bool
    {
        // electron/ directory already exists in the codebase
        // Only create scripts/ and backup directories
        $directories = [
            base_path('scripts'),
            $this->backupPath,
        ];

        foreach ($directories as $directory) {
            if (! $this->files->isDirectory($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }
        }

        return true;
    }

    /**
     * Backup original files before modification.
     */
    protected function backupOriginalFiles(): bool
    {
        $filesToBackup = [
            base_path('package.json') => 'package.json.bak',
            base_path('bootstrap/providers.php') => 'providers.php.bak',
        ];

        foreach ($filesToBackup as $source => $backupName) {
            $backupPath = "{$this->backupPath}/{$backupName}";

            // Only create backup if it doesn't already exist
            // This preserves the original files across multiple setup/rollback cycles
            if ($this->files->exists($source) && ! $this->files->exists($backupPath)) {
                $this->files->copy($source, $backupPath);
            }
        }

        return true;
    }

    /**
     * Publish the electron configuration file.
     */
    protected function publishElectronConfig(): bool
    {
        $stub = $this->files->get("{$this->stubPath}/config/electron.php.stub");

        // Replace service defaults based on selection
        $stub = str_replace(
            "'driver' => env('ELECTRON_DB_DRIVER', 'sqlite')",
            "'driver' => env('ELECTRON_DB_DRIVER', '{$this->services['database']}')",
            $stub
        );

        $redisEnabled = $this->services['redis'] ? 'true' : 'false';
        $stub = str_replace(
            "'enabled' => env('ELECTRON_REDIS_ENABLED', true)",
            "'enabled' => env('ELECTRON_REDIS_ENABLED', {$redisEnabled})",
            $stub
        );

        $horizonEnabled = $this->services['horizon'] ? 'true' : 'false';
        $stub = str_replace(
            "'enabled' => env('ELECTRON_HORIZON_ENABLED', true)",
            "'enabled' => env('ELECTRON_HORIZON_ENABLED', {$horizonEnabled})",
            $stub
        );

        $reverbEnabled = $this->services['reverb'] ? 'true' : 'false';
        $stub = str_replace(
            "'enabled' => env('ELECTRON_REVERB_ENABLED', false)",
            "'enabled' => env('ELECTRON_REVERB_ENABLED', {$reverbEnabled})",
            $stub
        );

        $schedulerEnabled = $this->services['scheduler'] ? 'true' : 'false';
        $stub = str_replace(
            "'enabled' => env('ELECTRON_SCHEDULER_ENABLED', false)",
            "'enabled' => env('ELECTRON_SCHEDULER_ENABLED', {$schedulerEnabled})",
            $stub
        );

        $this->files->put(config_path('electron.php'), $stub);

        return true;
    }

    /**
     * Publish the service provider.
     */
    protected function publishServiceProvider(): bool
    {
        return $this->publishStub(
            'providers/ElectronServiceProvider.php.stub',
            app_path('Providers/ElectronServiceProvider.php')
        );
    }

    /**
     * Publish binary setup scripts.
     */
    protected function publishBuildScripts(): bool
    {
        $this->publishStub(
            'scripts/setup-binaries.sh.stub',
            base_path('scripts/setup-binaries.sh')
        );

        // Make script executable
        chmod(base_path('scripts/setup-binaries.sh'), 0755);

        $this->publishStub(
            'scripts/setup-binaries.ps1.stub',
            base_path('scripts/setup-binaries.ps1')
        );

        return true;
    }

    /**
     * Publish electron-builder configuration.
     */
    protected function publishBuilderConfig(): bool
    {
        return $this->publishStub(
            'electron-builder.json.stub',
            base_path('electron-builder.json')
        );
    }

    /**
     * Update package.json with electron configuration.
     */
    protected function updatePackageJson(): bool
    {
        $packageJsonPath = base_path('package.json');
        $packageJson = json_decode($this->files->get($packageJsonPath), true);

        // Add main entry point
        $packageJson['main'] = 'electron/dist/main/index.js';

        // Add electron scripts
        $electronScripts = [
            'electron:compile' => "mkdir -p electron/dist && echo '{\"type\":\"commonjs\"}' > electron/dist/package.json && tsc -p electron && cp -r electron/renderer electron/dist/",
            'electron:dev' => 'pnpm electron:compile && NODE_ENV=development electron .',
            'electron:build' => 'pnpm build && pnpm electron:compile && electron-builder',
            'electron:build:mac' => 'pnpm build && pnpm electron:compile && electron-builder --mac',
            'electron:build:win' => 'pnpm build && pnpm electron:compile && electron-builder --win',
            'electron:build:dir' => 'pnpm build && pnpm electron:compile && electron-builder --dir',
            'setup:binaries' => './scripts/setup-binaries.sh',
            'setup:binaries:win' => 'powershell -ExecutionPolicy Bypass -File scripts/setup-binaries.ps1',
        ];

        $packageJson['scripts'] = array_merge($packageJson['scripts'] ?? [], $electronScripts);

        // Add build configuration reference
        $packageJson['build'] = [
            'extends' => './electron-builder.json',
        ];

        $this->files->put(
            $packageJsonPath,
            json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n"
        );

        return true;
    }

    /**
     * Update bootstrap/providers.php to conditionally load ElectronServiceProvider.
     */
    protected function updateBootstrapProviders(): bool
    {
        $path = base_path('bootstrap/providers.php');
        $content = $this->files->get($path);

        // Check if already modified
        if (Str::contains($content, 'ElectronServiceProvider')) {
            return true;
        }

        // Check if it's the conditional format (from tenancy) or simple array
        if (Str::contains($content, '$providers = [')) {
            // Already using conditional format, add electron provider
            $content = str_replace(
                'return $providers;',
                "// Conditionally load ElectronServiceProvider when electron is configured\n".
                "if (file_exists(config_path('electron.php'))) {\n".
                "    \$providers[] = App\\Providers\\ElectronServiceProvider::class;\n".
                "}\n\n".
                'return $providers;',
                $content
            );
        } else {
            // Simple return array format - convert to conditional
            $content = <<<'PHP'
<?php

$providers = [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
];

// Conditionally load ElectronServiceProvider when electron is configured
if (file_exists(config_path('electron.php'))) {
    $providers[] = App\Providers\ElectronServiceProvider::class;
}

return $providers;

PHP;
        }

        $this->files->put($path, $content);

        return true;
    }

    /**
     * Update .env.example with electron variables.
     */
    protected function updateEnvExample(): bool
    {
        $envPath = base_path('.env.example');
        $content = $this->files->get($envPath);

        // Check if already has electron variables
        if (Str::contains($content, 'ELECTRON_APP_NAME')) {
            return true;
        }

        $electronEnv = <<<'ENV'

# =============================================================================
# ELECTRON DESKTOP BUILD
# =============================================================================
# These variables are used when building and running as an Electron desktop app.
# They have no effect on standard web deployment.

# Application Identity
ELECTRON_APP_NAME="${APP_NAME}"
ELECTRON_BUNDLE_ID=com.example.app

# Database (sqlite = bundled, mysql = external)
ELECTRON_DB_DRIVER=sqlite
ELECTRON_DB_EXTERNAL=false

# Redis (for caching and queues)
ELECTRON_REDIS_ENABLED=true
ELECTRON_REDIS_EXTERNAL=false

# Queue Worker (requires Redis)
ELECTRON_HORIZON_ENABLED=true

# WebSocket Server
ELECTRON_REVERB_ENABLED=false

# Task Scheduler
ELECTRON_SCHEDULER_ENABLED=false

# Default Ports (auto-allocated at runtime if occupied)
ELECTRON_PORT_LARAVEL=48000
ELECTRON_PORT_REDIS=6379
ELECTRON_PORT_REVERB=48080
ENV;

        $this->files->put($envPath, $content.$electronEnv."\n");

        return true;
    }

    /**
     * Create .env.production template.
     */
    protected function createEnvProduction(): bool
    {
        return $this->publishStub(
            'env.production.stub',
            base_path('.env.production')
        );
    }

    /**
     * Publish a stub file to a destination.
     */
    protected function publishStub(string $stub, string $destination): bool
    {
        $stubPath = "{$this->stubPath}/{$stub}";

        if (! $this->files->exists($stubPath)) {
            return false;
        }

        $content = $this->files->get($stubPath);

        // Ensure directory exists
        $directory = dirname($destination);
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($destination, $content);

        return true;
    }

    /**
     * Display next steps after setup.
     */
    protected function displayNextSteps(): void
    {
        note('Next steps:');
        $this->newLine();

        $this->line('  1. Run in development mode:');
        $this->line('     <comment>pnpm electron:dev</comment>');
        $this->newLine();

        $this->line('  2. For production builds, download platform binaries:');
        $this->line('     <comment>pnpm setup:binaries</comment>');
        $this->newLine();

        $this->line('  3. Add your application icons:');
        $this->line('     <comment>electron/resources/icon.icns</comment> (macOS)');
        $this->line('     <comment>electron/resources/icon.ico</comment> (Windows)');
        $this->newLine();

        $this->line('  4. Build for distribution:');
        $this->line('     <comment>pnpm electron:build:mac</comment> (macOS)');
        $this->line('     <comment>pnpm electron:build:win</comment> (Windows)');
        $this->newLine();
    }

    /**
     * Handle the rollback operation.
     */
    protected function handleRollback(): int
    {
        $this->newLine();
        warning('Rolling back Electron setup...');
        $this->newLine();

        if (! $this->isAlreadyConfigured()) {
            // Even if electron isn't configured, restore from backups if they exist
            // This handles cases where a previous rollback was interrupted
            if ($this->files->isDirectory($this->backupPath)) {
                $this->restorePackageJson();
                $this->restoreBootstrapProviders();
                $this->cleanupBackups();
            }

            info('Electron is not configured. Nothing to rollback.');

            return self::SUCCESS;
        }

        // Confirm rollback (skip if --force is used)
        if (! $this->option('force')) {
            warning('This will remove all Electron files and restore original configuration.');

            if (! confirm('Continue with rollback?', false)) {
                info('Rollback cancelled.');

                return self::SUCCESS;
            }
        }

        // Note: electron/ directory is NOT removed - it's part of the codebase
        $steps = [
            'removeConfig' => 'Removing configuration',
            'removeProvider' => 'Removing service provider',
            'removeScripts' => 'Removing build scripts',
            'removeEnvProduction' => 'Removing .env.production',
            'removeBuilderConfig' => 'Removing electron-builder config',
            'restorePackageJson' => 'Restoring package.json',
            'restoreBootstrapProviders' => 'Restoring bootstrap providers',
            'restoreEnvExample' => 'Restoring .env.example',
            'removeNpmDependencies' => 'Removing npm dependencies',
            'cleanupBackups' => 'Cleaning up backup files',
        ];

        $currentStep = 0;
        $totalSteps = count($steps);

        foreach ($steps as $method => $description) {
            $currentStep++;
            $prefix = "[{$currentStep}/{$totalSteps}]";

            $result = spin(
                fn () => $this->{$method}(),
                "{$prefix} {$description}..."
            );

            if ($result === false) {
                error("Failed: {$description}");

                return self::FAILURE;
            }
        }

        $this->newLine();
        info('Electron rollback complete! The application has been reset.');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Remove the electron configuration file.
     */
    protected function removeConfig(): bool
    {
        $path = config_path('electron.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove the ElectronServiceProvider.
     */
    protected function removeProvider(): bool
    {
        $path = app_path('Providers/ElectronServiceProvider.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove build scripts.
     */
    protected function removeScripts(): bool
    {
        $files = [
            base_path('scripts/setup-binaries.sh'),
            base_path('scripts/setup-binaries.ps1'),
        ];

        foreach ($files as $path) {
            if ($this->files->exists($path)) {
                $this->files->delete($path);
            }
        }

        // Remove scripts directory if empty
        $scriptsDir = base_path('scripts');
        if ($this->files->isDirectory($scriptsDir) && count($this->files->files($scriptsDir)) === 0) {
            $this->files->deleteDirectory($scriptsDir);
        }

        return true;
    }

    /**
     * Remove .env.production.
     */
    protected function removeEnvProduction(): bool
    {
        $path = base_path('.env.production');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove electron-builder config.
     */
    protected function removeBuilderConfig(): bool
    {
        $path = base_path('electron-builder.json');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Restore package.json from backup.
     */
    protected function restorePackageJson(): bool
    {
        $path = base_path('package.json');
        $backupPath = "{$this->backupPath}/package.json.bak";

        if ($this->files->exists($backupPath)) {
            $this->files->copy($backupPath, $path);
        }

        return true;
    }

    /**
     * Restore bootstrap/providers.php from backup.
     */
    protected function restoreBootstrapProviders(): bool
    {
        $path = base_path('bootstrap/providers.php');
        $backupPath = "{$this->backupPath}/providers.php.bak";

        if ($this->files->exists($backupPath)) {
            $this->files->copy($backupPath, $path);
        }

        return true;
    }

    /**
     * Restore .env.example by removing electron variables.
     */
    protected function restoreEnvExample(): bool
    {
        $envPath = base_path('.env.example');
        $content = $this->files->get($envPath);

        // Remove electron section
        $pattern = '/\n# =+\n# ELECTRON DESKTOP BUILD\n# =+.*?ELECTRON_PORT_REVERB=\d+\n/s';
        $content = preg_replace($pattern, '', $content);

        $this->files->put($envPath, $content);

        return true;
    }

    /**
     * Remove npm electron dependencies.
     */
    protected function removeNpmDependencies(): bool
    {
        $packageManager = file_exists(base_path('pnpm-lock.yaml')) ? 'pnpm' : 'npm';

        $command = $packageManager === 'pnpm'
            ? 'pnpm remove electron electron-builder'
            : 'npm uninstall electron electron-builder';

        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            base_path()
        );

        if (is_resource($process)) {
            fclose($pipes[0]);
            stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        }

        return true;
    }

    /**
     * Clean up backup files after successful rollback.
     */
    protected function cleanupBackups(): bool
    {
        if ($this->files->isDirectory($this->backupPath)) {
            $this->files->deleteDirectory($this->backupPath);
        }

        return true;
    }
}
