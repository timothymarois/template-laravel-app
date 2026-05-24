<?php

declare(strict_types=1);

use App\Enums\TenantRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pivot table linking central App\Models\User to App\Models\Tenant — the
 * foundation of multi-tenant access where one user can belong to many tenants
 * (and one tenant has many member users).
 *
 * Naming follows Laravel's belongsToMany default: singular alphabetical
 * `tenant_user` (vs plural `tenant_users`). The User::tenants() and
 * Tenant::users() relationships rely on this convention.
 *
 * Forks that only need single-tenant-per-user can still use this table — it
 * just always holds one row per user with role=owner.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_user', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default(TenantRole::Member->value); // see App\Enums\TenantRole
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id']);
            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_user');
    }
};
