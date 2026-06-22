<?php

use App\Support\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['permission_id', 'role_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        $now = now();
        $roleIds = [];

        foreach (UserRole::LABELS as $slug => $name) {
            $roleIds[$slug] = DB::table('roles')->insertGetId([
                'name' => $name,
                'slug' => $slug,
                'description' => 'Rol migrado desde la configuración inicial del sistema.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permissionIds = [];
        foreach (array_keys(UserRole::PERMISSIONS) as $slug) {
            $permissionIds[$slug] = DB::table('permissions')->insertGetId([
                'name' => Str::headline(str_replace('-', ' ', $slug)),
                'slug' => $slug,
                'description' => 'Permiso del panel administrativo.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach (UserRole::PERMISSIONS as $permission => $roles) {
            foreach ($roles as $role) {
                DB::table('permission_role')->insert([
                    'permission_id' => $permissionIds[$permission],
                    'role_id' => $roleIds[$role],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $adminRoleId = $roleIds[UserRole::ADMIN];
        foreach (array_keys(UserRole::PERMISSIONS) as $permission) {
            DB::table('permission_role')->updateOrInsert(
                ['permission_id' => $permissionIds[$permission], 'role_id' => $adminRoleId],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        foreach ($roleIds as $slug => $id) {
            DB::table('users')->where('role', $slug)->update(['role_id' => $id]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};