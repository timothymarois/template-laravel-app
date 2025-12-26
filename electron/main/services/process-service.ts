import { ChildProcess, spawn, execSync } from 'child_process';
import * as fs from 'fs';
import type { PidFileData } from './index';

interface ProcessServiceOptions {
    pidFilePath: string;
    onError: (error: string) => void;
}

export class ProcessService {
    private pidFilePath: string;
    private onError: (error: string) => void;
    private processes: Map<string, ChildProcess> = new Map();

    constructor(options: ProcessServiceOptions) {
        this.pidFilePath = options.pidFilePath;
        this.onError = options.onError;
    }

    /**
     * Spawn a detached process.
     */
    spawnProcess(
        name: string,
        command: string,
        args: string[],
        options: {
            cwd?: string;
            env?: Record<string, string>;
        } = {}
    ): ChildProcess {
        console.log(`[Process] Starting ${name}: ${command} ${args.join(' ')}`);

        const child = spawn(command, args, {
            cwd: options.cwd,
            env: options.env,
            detached: true,
            stdio: ['ignore', 'pipe', 'pipe'],
        });

        // Handle stream errors to prevent EPIPE crashes during shutdown
        child.stdout?.on('error', () => {});
        child.stderr?.on('error', () => {});
        process.stdout?.on('error', () => {});
        process.stderr?.on('error', () => {});

        child.stdout?.on('data', (data) => {
            try {
                console.log(`[${name}] ${data.toString().trim()}`);
            } catch {}
        });

        child.stderr?.on('data', (data) => {
            try {
                console.error(`[${name}] ${data.toString().trim()}`);
            } catch {}
        });

        child.on('error', (error) => {
            try {
                console.error(`[${name}] Process error:`, error);
                this.onError(`${name} process error: ${error.message}`);
            } catch {}
        });

        child.on('exit', (code, signal) => {
            try {
                console.log(`[${name}] Process exited with code ${code}, signal ${signal}`);
            } catch {}
            this.processes.delete(name);
        });

        this.processes.set(name, child);
        return child;
    }

    /**
     * Stop a process by name.
     */
    async stopProcess(name: string): Promise<void> {
        const child = this.processes.get(name);
        if (!child || !child.pid) return;

        console.log(`[Process] Stopping ${name} (PID: ${child.pid})...`);

        return new Promise((resolve) => {
            // First try graceful shutdown
            try {
                process.kill(-child.pid!, 'SIGTERM');
            } catch {
                try {
                    child.kill('SIGTERM');
                } catch {}
            }

            // Force kill after timeout
            const timeout = setTimeout(() => {
                try {
                    process.kill(-child.pid!, 'SIGKILL');
                } catch {
                    try {
                        child.kill('SIGKILL');
                    } catch {}
                }
                resolve();
            }, 2000);

            child.on('exit', () => {
                clearTimeout(timeout);
                resolve();
            });
        });
    }

    /**
     * Clean up processes from previous sessions.
     */
    async cleanupStaleProcesses(): Promise<void> {
        if (!fs.existsSync(this.pidFilePath)) return;

        try {
            const pidData: PidFileData = JSON.parse(fs.readFileSync(this.pidFilePath, 'utf-8'));

            // Kill any stale processes
            for (const [name, pid] of Object.entries(pidData)) {
                if (name === 'ports' || name === 'timestamp') continue;
                if (typeof pid === 'number' && pid > 0) {
                    try {
                        process.kill(pid, 'SIGKILL');
                        console.log(`[Process] Killed stale ${name} process (PID: ${pid})`);
                    } catch {
                        // Process already dead
                    }
                }
            }

            // Remove PID file
            fs.unlinkSync(this.pidFilePath);
        } catch (error) {
            console.error('[Process] Error cleaning up stale processes:', error);
        }
    }

    /**
     * Kill a process by port.
     */
    async killProcessOnPort(port: number): Promise<void> {
        try {
            if (process.platform === 'win32') {
                // Windows
                const output = execSync(`netstat -ano | findstr :${port}`).toString();
                const match = output.match(/\s+(\d+)\s*$/m);
                if (match) {
                    execSync(`taskkill /F /PID ${match[1]}`);
                }
            } else {
                // macOS / Linux
                execSync(`lsof -ti:${port} | xargs kill -9 2>/dev/null || true`);
            }
        } catch {
            // No process on port
        }
    }
}
