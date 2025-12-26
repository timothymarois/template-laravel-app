import { ChildProcess } from 'child_process';
import type { ConfigService } from './config-service';
import type { ProcessService } from './process-service';

interface SchedulerServiceOptions {
    configService: ConfigService;
    processService: ProcessService;
}

export class SchedulerService {
    private configService: ConfigService;
    private processService: ProcessService;
    private process: ChildProcess | null = null;

    constructor(options: SchedulerServiceOptions) {
        this.configService = options.configService;
        this.processService = options.processService;
    }

    get pid(): number | undefined {
        return this.process?.pid;
    }

    /**
     * Start Laravel scheduler.
     */
    async start(env: Record<string, string>): Promise<void> {
        const phpCommand = this.configService.isDev ? 'herd' : this.configService.phpPath;
        const phpArgs = this.configService.isDev
            ? ['php', 'artisan', 'schedule:work']
            : ['artisan', 'schedule:work'];

        this.process = this.processService.spawnProcess('Scheduler', phpCommand, phpArgs, {
            cwd: this.configService.laravelRoot,
            env,
        });
    }

    /**
     * Stop scheduler.
     */
    async stop(): Promise<void> {
        await this.processService.stopProcess('Scheduler');
        this.process = null;
    }
}
