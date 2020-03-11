<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Project;
use Faker\Generator as Faker;

class UsersProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $users = User::all();
        $projects = Project::all();

        foreach ($users as $user)
        {
            for($i = 0; $i < rand(0,10); $i++)
            {
                $rand_project = $faker->randomElement($projects);
                $user->projects()->save($rand_project);
            }
        }
    }
}
