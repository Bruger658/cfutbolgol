<?php

use App\Support\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (UserRole::PERMISSION_LABELS as $slug => $name) {
            DB::table('permissions')
                ->where('slug', $slug)
                ->update([
                    'name' => $name,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $previousNames = [
            'access-dashboard' => 'Access Dashboard',
            'manage-content' => 'Manage Content',
            'manage-store' => 'Manage Store',
            'manage-members' => 'Manage Members',
            'manage-fees' => 'Manage Fees',
            'manage-enrollments' => 'Manage Enrollments',
            'manage-roles' => 'Manage Roles',
        ];

        foreach ($previousNames as $slug => $name) {
            DB::table('permissions')
                ->where('slug', $slug)
                ->update([
                    'name' => $name,
                    'updated_at' => now(),
                ]);
        }
    }
};