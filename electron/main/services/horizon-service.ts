import { ChildProcess } from 'child_process';
import type { ConfigService } from './config-service';
import type { ProcessService } from './process-service';
import type { RedisService } from './redis-service';

interface HorizonServiceOptions {
    configService: ConfigService;
    processService: ProcessService;
    redisService: RedisService;
}

export class HorizonService {
    private configService: ConfigService;
    private processService: ProcessService;
    private redisService: RedisService;
    private process: ChildProcess | null = null;

    constructor(options: HorizonServiceOptions) {
        this.configService = options.configService;
        this.processService = options.processService;
        this.redisService = options.redisService;
    }

    get pid(): number | undefined {
        return this.process?.pid;
    }

    /**
     * Start Horizon queue worker.
     */
    async start(redisPort: number, env: Record<string, string>): Promise<void> {
        // Skip if Redis is not available in dev mode
        if (this.configService.isDev && !this.redisService.isAvailableInDev) {
            console.log('[Horizon] Skipping - Redis not available in dev mode');
            return;
        }

        const phpCommand = this.configService.isDev ? 'herd' : this.configService.phpPath;
        const phpArgs = this.configService.isDev
            ? ['php', 'artisan', 'horizon']
            : ['artisan', 'horizon'];

        this.process = this.processService.spawnProcess('Horizon', phpCommand, phpArgs, {
            cwd: this.configService.laravelRoot,
            env,
        });
    }

    /**
     * Stop Horizon.
     */
    async stop(): Promise<void> {
        await this.processService.stopProcess('Horizon');
        this.process = null;
    }
}
