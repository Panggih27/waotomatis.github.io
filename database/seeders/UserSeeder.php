<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::create([
            'id' => Str::orderedUuid(),
            'name' => 'admin',
            'email' => 'admin@waotomatis.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'api_key' => 'WAOTO-'. Str::random(54),
            'chunk_blast' => 0
        ]);

        $user->point()->create([
            'point' => 1000000,
            'expired_at' => now()->addYear()
        ]);

        $role = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        $roleUser = Role::create([
            'name' => 'customer',
            'guard_name' => 'web'
        ]);

        DB::table('permissions')->insert([
            [
                'name' => 'product-crud',
                'guard_name' => 'web',
            ],[
                'name' => 'update-transaction',
                'guard_name' => 'web',
            ],[
                'name' => 'tnc-preview',
                'guard_name' => 'web',
            ],[
                'name' => 'user-tickets',
                'guard_name' => 'web',
            ]
        ]);

        $roleUser->givePermissionTo(['tnc-preview', 'user-tickets']);
        $user->assignRole($role);

    }
}
