// Core services (always included)
export { ConfigService } from './config-service';
export { PortService } from './port-service';
export { ProcessService } from './process-service';
export { LaravelService } from './laravel-service';
export { HealthService } from './health-service';

// Optional services are NOT exported here to avoid compile-time dependencies.
// They are loaded dynamically by boot-manager.ts using require() with try-catch.
// See: boot-manager.ts for how optional services are conditionally loaded.
//
// Optional service files (when enabled):
// - redis-service.ts (RedisService)
// - horizon-service.ts (HorizonService)
// - reverb-service.ts (ReverbService)
// - scheduler-service.ts (SchedulerService)

// Types
export interface PidFileData {
    laravel?: number;
    redis?: number;
    horizon?: number;
    reverb?: number;
    scheduler?: number;
    ports: {
        laravel: number;
        redis: number;
        reverb: number;
    };
    timestamp: number;
}
