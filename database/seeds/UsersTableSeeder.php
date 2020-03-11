<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(App\User::class, 1)->create(['email' => 'admin@prueba-tecnica.test', 'role' => 'admin', 'api_token' => env('ADMIN_API_TOKEN', 'dummy')]);
        $managers = factory(App\User::class, 5)->create(['role' => 'manager']);
        $scrum_masters = factory(App\User::class, 25)->create(['role' => 'scrum_master']);
        $developers = factory(App\User::class, 125)->create(['role' => 'developer']);
    }
}
