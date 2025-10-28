<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Make sure to use DB facade

use App\Models\Role;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Truncate tables with foreign keys
        User::truncate();
        DB::table('role_user')->truncate();

        // 3. Define and create roles
        $adminRole = Role::where('name', 'admin')->first();
        $vetRole = Role::where('name', 'vet')->first();
        $farmerRole = Role::where('name', 'farmer')->first();

        // 4. Define and create users
        $admin = User::create([
            'name' => 'AdminUser',
            'email' => 'chan@gmail.com',
            'password' => Hash::make('admin123')
        ]);

        $vet = User::create([
            'name' => 'Vetranies',
            'email' => 'vet@gmail.com',
            'password' => Hash::make('vet123')
        ]);

        $farmer = User::create([
            'name' => 'Farmer',
            'email' => 'farmer@gmail.com',
            'password' => Hash::make('farmer123')
        ]);

            // $x = 0;
            // foreach(range(1,20) as $index)
            // {
            //     $x++;
            //     $user1 = User::create([
            //         'name' => 'User'.$x,
            //         'email' => 'user'.$x.'@mail.com',
            //         'password' => Hash::make('user')
            //     ]);

            //     $user1->roles()->attach($userRole);
            // }

        // 5. Attach roles to users
        $admin->roles()->attach($adminRole);
        $vet->roles()->attach($vetRole);
        $farmer->roles()->attach($farmerRole);

        // 6. Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}