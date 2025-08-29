# Agents Guide

This project uses Laravel for the backend and Vue 3 with Inertia for the frontend. Agents contributing to this repository must adhere to the project's core conventions:

1. **Backend (Laravel)** – Follow the [Laravel Guide](./laravel-guide.md) for architecture, naming, and coding standards.
2. **Frontend (Vue)** – Follow the [Vue Guide](./vue-guide.md) for structure, component design, and best practices.

After completing a task, review every change to ensure it follows the appropriate guide. If a change does not meet the relevant standards, revise the code before finalizing.

Additional notes:

- Any new controller functionality should be implemented within dedicated service classes.
- For new enums, create them in Laravel and run the enum command to generate the corresponding Vue enums. Use those generated enums in the frontend code.

Always review the appropriate guide before committing changes to ensure the standards are met. Also, check for any `AGENTS.md` files within the repository and follow their instructions.
