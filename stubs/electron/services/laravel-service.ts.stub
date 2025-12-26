import { ChildProcess } from 'child_process';
import type { ConfigService } from './config-service';
import type { ProcessService } from './process-service';

interface LaravelServiceOptions {
    configService: ConfigService;
    processService: ProcessService;
}

export class LaravelService {
    private configService: ConfigService;
    private processService: ProcessService;
    private process: ChildProcess | null = null;

    constructor(options: LaravelServiceOptions) {
        this.configService = options.configService;
        this.processService = options.processService;
    }

    get pid(): number | undefined {
        return this.process?.pid;
    }

    /**
     * Start the Laravel development server.
     */
    async start(port: number, env: Record<string, string>): Promise<void> {
        const phpCommand = this.configService.isDev ? 'herd' : this.configService.phpPath;
        const phpArgs = this.configService.isDev
            ? ['php', 'artisan', 'serve', '--host=127.0.0.1', `--port=${port}`]
            : ['artisan', 'serve', '--host=127.0.0.1', `--port=${port}`];

        this.process = this.processService.spawnProcess('Laravel', phpCommand, phpArgs, {
            cwd: this.configService.laravelRoot,
            env,
        });
    }

    /**
     * Wait for Laravel to be ready.
     */
    async waitForReady(port: number, maxAttempts: number = 30): Promise<void> {
        const url = `http://127.0.0.1:${port}/up`;

        for (let i = 0; i < maxAttempts; i++) {
            try {
                const response = await fetch(url, { method: 'GET' });
                if (response.ok) {
                    console.log('[Laravel] Server is ready');
                    return;
                }
            } catch {
                // Not ready yet
            }
            await new Promise((resolve) => setTimeout(resolve, 500));
        }

        throw new Error('Laravel server failed to start');
    }

    /**
     * Stop the Laravel server.
     */
    async stop(): Promise<void> {
        await this.processService.stopProcess('Laravel');
        this.process = null;
    }
}
