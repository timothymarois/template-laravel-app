# Architecture Overview

This document describes the high-level architecture of the Laravel Vue Starter.

## System Design

The application follows a modern monolithic architecture with clear separation of concerns:

```
┌─────────────────────────────────────────────────────────────┐
│                        Browser                               │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Vue 3 + Inertia                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │   Pages     │  │  Components │  │    Composables      │  │
│  └─────────────┘  └─────────────┘  └─────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Backend                           │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │ Controllers │  │   Services  │  │      Models         │  │
│  └─────────────┘  └─────────────┘  └─────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     Data Layer                               │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │   MySQL     │  │    Redis    │  │      Storage        │  │
│  └─────────────┘  └─────────────┘  └─────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## Key Principles

### 1. Thin Controllers

Controllers handle HTTP concerns only. All business logic lives in Services.

```php
// Good: Controller delegates to Service
public function store(CreateUserRequest $request): Response
{
    $user = $this->userService->create($request->validated());
    return $this->inertia->render('admin/users/Show', ['user' => $user]);
}

// Bad: Business logic in controller
public function store(Request $request): Response
{
    // Don't do validation, transformation, or business logic here
}
```

### 2. Service Layer

Services encapsulate business logic and can be injected anywhere:

```php
class UserService extends ModelService
{
    public function create(array $data): User
    {
        // Business logic, validation, side effects
    }
}
```

### 3. Single Purpose Files

Every file has one clear responsibility:
- Controllers handle HTTP
- Services handle business logic
- Models handle data structure and relationships
- Composables handle reusable Vue logic

### 4. Stateless UI Components

Base UI components (`components/ui/`) are stateless and props-driven. Application logic lives in `components/app/` or `components/site/`.

## Request Flow

1. **HTTP Request** → Nginx/PHP-FPM
2. **Middleware** → Authentication, rate limiting, security headers
3. **Controller** → Route handling, request validation
4. **Service** → Business logic execution
5. **Model** → Database operations
6. **Inertia Response** → Page component with props
7. **Vue Render** → Client-side or SSR

## Real-Time Flow

1. **Event Triggered** → Laravel event dispatched
2. **Broadcasting** → Event sent to Reverb
3. **WebSocket** → Reverb pushes to connected clients
4. **Echo** → Vue composable receives event
5. **UI Update** → Reactive state updates

## Background Jobs

1. **Job Dispatched** → Added to Redis queue
2. **Horizon** → Monitors and processes jobs
3. **Worker** → Executes job logic
4. **Retry/Fail** → Automatic retry or failure handling

## Directory Structure

See [Tech Stack](/architecture/tech-stack) for the complete directory layout.
