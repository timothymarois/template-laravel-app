import { app } from 'electron';
import { execSync, spawn } from 'child_process';
import * as fs from 'fs';
import * as path from 'path';
import * as os from 'os';

interface ConfigServiceOptions {
    isDev: boolean;
}

export class ConfigService {
    public isDev: boolean;

    constructor(options: ConfigServiceOptions) {
        this.isDev = options.isDev;
    }

    /**
     * Get the Laravel project root directory.
     */
    get laravelRoot(): string {
        if (this.isDev) {
            return process.cwd();
        }
        return path.join(process.resourcesPath, 'laravel');
    }

    /**
     * Get the binary directory for the current platform.
     */
    get binPath(): string {
        const platform = process.platform;
        const arch = process.arch;
        const platformArch = `${platform === 'darwin' ? 'darwin' : 'win32'}-${arch === 'arm64' ? 'arm64' : 'x64'}`;

        if (this.isDev) {
            return path.join(process.cwd(), 'electron', 'resources', 'bin', platformArch);
        }
        return path.join(process.resourcesPath, 'bin');
    }

    /**
     * Get the PHP binary path.
     */
    get phpPath(): string {
        if (this.isDev) {
            // Use Herd PHP in development
            return 'herd php';
        }
        const ext = process.platform === 'win32' ? '.exe' : '';
        return path.join(this.binPath, `php${ext}`);
    }

    /**
     * Get the Redis binary path.
     */
    get redisPath(): string {
        const ext = process.platform === 'win32' ? '.exe' : '';
        return path.join(this.binPath, `redis-server${ext}`);
    }

    /**
     * Get the user data directory for storing app data.
     */
    get userDataPath(): string {
        return app.getPath('userData');
    }

    /**
     * Get the storage path for Laravel.
     */
    get storagePath(): string {
        if (this.isDev) {
            return path.join(this.laravelRoot, 'storage');
        }
        return path.join(this.userDataPath, 'storage');
    }

    /**
     * Get the database path for SQLite.
     */
    get databasePath(): string {
        if (this.isDev) {
            return path.join(this.laravelRoot, 'database', 'database.sqlite');
        }
        return path.join(this.userDataPath, 'database', 'database.sqlite');
    }

    /**
     * Get the PID file path.
     */
    get pidFilePath(): string {
        return path.join(this.userDataPath, 'pids.json');
    }

    /**
     * Initialize required directories.
     */
    initializeDirectories(): void {
        const directories = [
            this.userDataPath,
            path.dirname(this.databasePath),
            path.join(this.storagePath, 'app'),
            path.join(this.storagePath, 'framework', 'cache'),
            path.join(this.storagePath, 'framework', 'sessions'),
            path.join(this.storagePath, 'framework', 'views'),
            path.join(this.storagePath, 'logs'),
        ];

        for (const dir of directories) {
            if (!fs.existsSync(dir)) {
                fs.mkdirSync(dir, { recursive: true });
            }
        }
    }

    /**
     * Get environment variables for running artisan commands.
     */
    getArtisanEnv(): Record<string, string> {
        return {
            ...process.env,
            APP_ENV: this.isDev ? 'local' : 'production',
            DB_DATABASE: this.databasePath,
            QUEUE_CONNECTION: 'sync',
            CACHE_STORE: 'file',
        } as Record<string, string>;
    }

    /**
     * Get environment variables for Laravel server.
     */
    getLaravelEnv(redisPort: number, reverbPort: number): Record<string, string> {
        return {
            ...process.env,
            APP_ENV: this.isDev ? 'local' : 'production',
            APP_DEBUG: this.isDev ? 'true' : 'false',
            DB_DATABASE: this.databasePath,
            APP_STORAGE_PATH: this.storagePath,
            REDIS_HOST: '127.0.0.1',
            REDIS_PORT: String(redisPort),
            QUEUE_CONNECTION: 'redis',
            CACHE_STORE: 'redis',
            REVERB_HOST: '127.0.0.1',
            REVERB_PORT: String(reverbPort),
        } as Record<string, string>;
    }

    /**
     * Get environment variables for Horizon.
     */
    getHorizonEnv(redisPort: number, reverbPort: number): Record<string, string> {
        return {
            ...this.getLaravelEnv(redisPort, reverbPort),
            BROADCAST_CONNECTION: 'reverb',
        };
    }

    /**
     * Get environment variables for Reverb.
     */
    getReverbEnv(reverbPort: number): Record<string, string> {
        return {
            ...process.env,
            REVERB_HOST: '0.0.0.0',
            REVERB_PORT: String(reverbPort),
        } as Record<string, string>;
    }

    /**
     * Run an artisan command.
     */
    async runArtisan(args: string[]): Promise<string> {
        return new Promise((resolve, reject) => {
            const phpCommand = this.isDev ? 'herd' : this.phpPath;
            const phpArgs = this.isDev ? ['php', 'artisan', ...args] : ['artisan', ...args];

            const child = spawn(phpCommand, phpArgs, {
                cwd: this.laravelRoot,
                env: this.getArtisanEnv(),
            });

            let output = '';
            let error = '';

            child.stdout.on('data', (data) => {
                output += data.toString();
            });

            child.stderr.on('data', (data) => {
                error += data.toString();
            });

            child.on('close', (code) => {
                if (code === 0) {
                    resolve(output);
                } else {
                    reject(new Error(`Artisan command failed: ${error}`));
                }
            });
        });
    }

    /**
     * Write runtime .env file with dynamic values (production only).
     */
    writeRuntimeEnv(redisPort: number, reverbPort: number): void {
        if (this.isDev) return;

        const envPath = path.join(this.laravelRoot, '.env');
        let envContent = fs.existsSync(envPath) ? fs.readFileSync(envPath, 'utf-8') : '';

        // Update or append runtime values
        const runtimeValues: Record<string, string> = {
            DB_DATABASE: this.databasePath,
            APP_STORAGE_PATH: this.storagePath,
            REDIS_PORT: String(redisPort),
            REVERB_PORT: String(reverbPort),
        };

        for (const [key, value] of Object.entries(runtimeValues)) {
            const regex = new RegExp(`^${key}=.*$`, 'm');
            if (regex.test(envContent)) {
                envContent = envContent.replace(regex, `${key}=${value}`);
            } else {
                envContent += `\n${key}=${value}`;
            }
        }

        fs.writeFileSync(envPath, envContent);
    }
}
