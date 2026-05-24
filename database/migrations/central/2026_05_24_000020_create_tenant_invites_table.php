<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pending tenant invitations sent by email. One row per active invite.
 *
 * Single-use: when accepted, accepted_at is set and the same token can no
 * longer accept. Time-bound: expires_at defaults to +14 days. Unique on
 * (tenant_id, email) so the same person can't be invited to the same tenant
 * twice in parallel — resend behavior should update the existing row.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_invites', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant_id');
            $table->string('email');
            $table->string('role')->default(\App\Enums\TenantRole::Member->value);
            $table->string('token', 64)->unique();
            $table->foreignId('invited_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'email']);
            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_invites');
    }
};
