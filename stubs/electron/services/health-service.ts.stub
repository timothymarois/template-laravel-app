interface HealthCheckOptions {
    isDev: boolean;
    redisEnabled: boolean;
}

export class HealthService {
    private monitorInterval: NodeJS.Timeout | null = null;

    /**
     * Verify all services are running.
     */
    async verifyAllServices(laravelPort: number, options: HealthCheckOptions): Promise<void> {
        // Check Laravel
        const laravelHealthy = await this.checkLaravel(laravelPort);
        if (!laravelHealthy) {
            throw new Error('Laravel health check failed');
        }
        console.log('[Health] Laravel is healthy');
    }

    /**
     * Check if Laravel is responding.
     */
    private async checkLaravel(port: number): Promise<boolean> {
        try {
            const response = await fetch(`http://127.0.0.1:${port}/up`);
            return response.ok;
        } catch {
            return false;
        }
    }

    /**
     * Start health monitoring.
     */
    startMonitor(laravelPort: number, intervalMs: number = 30000): void {
        this.stopMonitor();

        this.monitorInterval = setInterval(async () => {
            const healthy = await this.checkLaravel(laravelPort);
            if (!healthy) {
                console.warn('[Health] Laravel health check failed');
            }
        }, intervalMs);
    }

    /**
     * Stop health monitoring.
     */
    stopMonitor(): void {
        if (this.monitorInterval) {
            clearInterval(this.monitorInterval);
            this.monitorInterval = null;
        }
    }
}
