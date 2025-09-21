<?php


namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use DB;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       // Nonaktifkan pemeriksaan foreign key
       DB::statement('SET FOREIGN_KEY_CHECKS=0;');

       // Hapus data lama
       Permission::truncate(); // Menghapus semua data permissions
       Role::truncate();       // Menghapus semua data roles
       User::truncate();       // Menghapus semua data users

       // Aktifkan kembali pemeriksaan foreign key
       DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Permissions
        Permission::firstOrCreate(['name' => 'view-role']);
        Permission::firstOrCreate(['name' => 'create-role']);
        Permission::firstOrCreate(['name' => 'update-role']);
        Permission::firstOrCreate(['name' => 'delete-role']);

        Permission::firstOrCreate(['name' => 'view-permission']);
        Permission::firstOrCreate(['name' => 'create-permission']);
        Permission::firstOrCreate(['name' => 'update-permission']);
        Permission::firstOrCreate(['name' => 'delete-permission']);

        Permission::firstOrCreate(['name' => 'view-user']);
        Permission::firstOrCreate(['name' => 'create-user']);
        Permission::firstOrCreate(['name' => 'update-user']);
        Permission::firstOrCreate(['name' => 'delete-user']);

        Permission::firstOrCreate(['name' => 'view-member']);
        Permission::firstOrCreate(['name' => 'create-member']);
        Permission::firstOrCreate(['name' => 'update-member']);
        Permission::firstOrCreate(['name' => 'delete-member']);

        Permission::firstOrCreate(['name' => 'view-trainer']);
        Permission::firstOrCreate(['name' => 'create-trainer']);
        Permission::firstOrCreate(['name' => 'update-trainer']);
        Permission::firstOrCreate(['name' => 'delete-trainer']);

        Permission::firstOrCreate(['name' => 'view-laporan']);

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']); //as super-admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $trainerRole = Role::firstOrCreate(['name' => 'trainer']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        // Give all permissions to super-admin role.
        $allPermissionNames = Permission::pluck('name')->toArray();
        $superAdminRole->givePermissionTo($allPermissionNames);

        // Give few permissions to admin role.
        $adminRole->givePermissionTo(['create-role', 'view-role', 'update-role']);
        $adminRole->givePermissionTo(['create-permission', 'view-permission']);
        $adminRole->givePermissionTo(['create-user', 'view-user', 'update-user']);

        // Give few permissions to trainer role.
        $trainerRole->givePermissionTo(['create-trainer', 'view-trainer', 'update-trainer', 'delete-trainer']);

        // Give few permissions to memebr role.
        $memberRole->givePermissionTo(['create-member', 'view-member', 'update-member', 'delete-member']);

        // Create User and assign Role to it.
        $superAdminUser = User::firstOrCreate([
                    'email' => 'super@gmail.com',
                ], [
                    'name' => 'Super Admin',
                    'email' => 'super@gmail.com',
                    'password' => Hash::make ('password'),
                ]);

        $superAdminUser->assignRole($superAdminRole);

        $adminUser = User::firstOrCreate([
                            'email' => 'admin@gmail.com'
                        ], [
                            'name' => 'Admin',
                            'email' => 'admin@gmail.com',
                            'password' => Hash::make ('password'),
                        ]);

        $adminUser->assignRole($adminRole);

        $trainerUser = User::firstOrCreate([
                            'email' => 'trainer@gmail.com',
                        ], [
                            'name' => 'Trainer',
                            'email' => 'trainer@gmail.com',
                            'password' => Hash::make('password'),
                        ]);

        $trainerUser->assignRole($trainerRole);
        
        $memberUser = User::firstOrCreate([
                            'email' => 'member@gmail.com',
                        ], [
                            'name' => 'Member',
                            'email' => 'member@gmail.com',
                            'password' => Hash::make('password'),
                        ]);

        $memberUser->assignRole($memberRole);
    }
}
