<?php

use App\Support\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $privilegedRoles = [
            UserRole::ADMIN,
            UserRole::EDITOR,
            UserRole::TESORERO,
            UserRole::COORDINADOR,
        ];

        $hasPrivilegedUser = DB::table('users')
            ->whereIn('role', $privilegedRoles)
            ->exists();

        if ($hasPrivilegedUser) {
            return;
        }

        $legacyUser = DB::table('users')
            ->orderBy('id')
            ->first(['id']);

        if (! $legacyUser) {
            return;
        }

        DB::table('users')
            ->where('id', $legacyUser->id)
            ->update(['role' => UserRole::ADMIN]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank: demoting an administrator on rollback could lock
        // the site owner out of the administration panel.
    }
};