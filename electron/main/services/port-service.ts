import * as net from 'net';

export class PortService {
    /**
     * Check if a port is available.
     */
    async isPortAvailable(port: number): Promise<boolean> {
        return new Promise((resolve) => {
            const server = net.createServer();

            server.once('error', () => {
                resolve(false);
            });

            server.once('listening', () => {
                server.close();
                resolve(true);
            });

            server.listen(port, '127.0.0.1');
        });
    }

    /**
     * Find an available port starting from the given port.
     */
    async findAvailablePort(startPort: number, maxAttempts: number = 100): Promise<number> {
        for (let i = 0; i < maxAttempts; i++) {
            const port = startPort + i;
            if (await this.isPortAvailable(port)) {
                return port;
            }
        }
        throw new Error(`Could not find available port starting from ${startPort}`);
    }
}
