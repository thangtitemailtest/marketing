<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::table('user')->insert([
        [
            'email' => 'admin.@gmail.com',
            'name' => 'daobka' ,
            'password' => bcrypt('123456'),
            'permission' => '1',
        ],
        [
            'email' => 'phamducdao.tb@gmail.com',
            'name' => 'ducdao' ,
            'password' => bcrypt('123456'),
            'permission' => '0',
        ]
        ]

        );

    }
}
