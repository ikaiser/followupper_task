<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Ruoli
        $roles = array(
            array('1', 'SuperAdmin', 'superadmin', 'all privilage'),
            array('2', 'Admin', 'admin', 'all privilage'),
            array('3', 'Moderatore/Ricercatore', 'moderatore/ricercatore', 'all privilage'),
            array('4', 'Utente', 'utente', 'user'),
            array('5', 'Osservatore', 'osservatore', 'all privilage'),
        );

        foreach($roles as $role)
        {
            DB::table('roles')->insert([
                'id'            => $role[0],
                'name'          => $role[1],
                'display_name'  => $role[2],
                'description'   => $role[3],
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at'    => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }

        DB::table('users')->insert([
            'id'            => 1,
            'name'          => 'SuperAdmin',
            'email'         => 'superadmin@followupper.com',
            'password'      => Hash::make('followupper'),
            'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'    => Carbon::now()->format('Y-m-d H:i:s'),
            'enabled'       => 1,
        ]);

        DB::table('role_user')->insert([
            'user_id'   => 1,
            'role_id'   => 1
        ]);
    }
}
