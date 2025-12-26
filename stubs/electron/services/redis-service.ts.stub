import { ChildProcess } from 'child_process';
import * as net from 'net';
import type { ConfigService } from './config-service';
import type { ProcessService } from './process-service';

interface RedisServiceOptions {
    configService: ConfigService;
    processService: ProcessService;
}

export class RedisService {
    private configService: ConfigService;
    private processService: ProcessService;
    private process: ChildProcess | null = null;
    public isAvailableInDev: boolean = false;

    constructor(options: RedisServiceOptions) {
        this.configService = options.configService;
        this.processService = options.processService;
    }

    get pid(): number | undefined {
        return this.process?.pid;
    }

    /**
     * Check if Redis is available on a port.
     */
    async checkConnection(port: number): Promise<boolean> {
        return new Promise((resolve) => {
            const client = new net.Socket();
            const timeout = setTimeout(() => {
                client.destroy();
                resolve(false);
            }, 1000);

            client.connect(port, '127.0.0.1', () => {
                client.write('PING\r\n');
            });

            client.on('data', (data) => {
                clearTimeout(timeout);
                client.destroy();
                resolve(data.toString().includes('+PONG'));
            });

            client.on('error', () => {
                clearTimeout(timeout);
                client.destroy();
                resolve(false);
            });
        });
    }

    /**
     * Start Redis server.
     */
    async start(port: number): Promise<void> {
        // In dev mode, check if Redis is already running
        if (this.configService.isDev) {
            const available = await this.checkConnection(port);
            if (available) {
                console.log(`[Redis] Using existing Redis on port ${port}`);
                this.isAvailableInDev = true;
                return;
            }
        }

        // Kill any existing process on the port
        await this.processService.killProcessOnPort(port);

        const redisPath = this.configService.redisPath;
        const args = [
            '--port', String(port),
            '--bind', '127.0.0.1',
            '--save', '',
            '--appendonly', 'no',
        ];

        this.process = this.processService.spawnProcess('Redis', redisPath, args);

        // Wait for Redis to be ready
        await this.waitForReady(port);
    }

    /**
     * Wait for Redis to be ready.
     */
    private async waitForReady(port: number, maxAttempts: number = 20): Promise<void> {
        for (let i = 0; i < maxAttempts; i++) {
            if (await this.checkConnection(port)) {
                console.log('[Redis] Server is ready');
                return;
            }
            await new Promise((resolve) => setTimeout(resolve, 300));
        }
        throw new Error('Redis server failed to start');
    }

    /**
     * Stop Redis server.
     */
    async stop(): Promise<void> {
        if (this.isAvailableInDev) {
            // Don't stop external Redis in dev mode
            return;
        }
        await this.processService.stopProcess('Redis');
        this.process = null;
    }
}
