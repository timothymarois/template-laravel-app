import { ChildProcess } from 'child_process';
import type { ConfigService } from './config-service';
import type { ProcessService } from './process-service';

interface ReverbServiceOptions {
    configService: ConfigService;
    processService: ProcessService;
}

export class ReverbService {
    private configService: ConfigService;
    private processService: ProcessService;
    private process: ChildProcess | null = null;

    constructor(options: ReverbServiceOptions) {
        this.configService = options.configService;
        this.processService = options.processService;
    }

    get pid(): number | undefined {
        return this.process?.pid;
    }

    /**
     * Start Reverb WebSocket server.
     */
    async start(port: number, env: Record<string, string>): Promise<void> {
        const phpCommand = this.configService.isDev ? 'herd' : this.configService.phpPath;
        const phpArgs = this.configService.isDev
            ? ['php', 'artisan', 'reverb:start', `--port=${port}`]
            : ['artisan', 'reverb:start', `--port=${port}`];

        this.process = this.processService.spawnProcess('Reverb', phpCommand, phpArgs, {
            cwd: this.configService.laravelRoot,
            env,
        });

        // Give Reverb time to start
        await new Promise((resolve) => setTimeout(resolve, 1000));
    }

    /**
     * Stop Reverb.
     */
    async stop(): Promise<void> {
        await this.processService.stopProcess('Reverb');
        this.process = null;
    }
}
