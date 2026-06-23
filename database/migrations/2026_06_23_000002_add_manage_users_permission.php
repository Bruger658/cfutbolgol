<?php

use App\Support\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $permissionId = DB::table('permissions')->updateOrInsert(
            ['slug' => 'manage-users'],
            [
                'name' => UserRole::PERMISSION_LABELS['manage-users'],
                'description' => 'Permiso del panel administrativo.',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $permission = DB::table('permissions')->where('slug', 'manage-users')->first();
        $adminRole = DB::table('roles')->where('slug', UserRole::ADMIN)->first();

        if ($permission && $adminRole) {
            DB::table('permission_role')->updateOrInsert(
                ['permission_id' => $permission->id, 'role_id' => $adminRole->id],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    public function down(): void
    {
        $permission = DB::table('permissions')->where('slug', 'manage-users')->first();

        if ($permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }
    }
};