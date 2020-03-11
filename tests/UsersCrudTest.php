<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class UsersCrudTest extends TestCase
{
    /** @test **/
    public function a_user_can_see_all_records()
    {
        factory('App\User')->create(['api_token' => $this->token]);

        $this->json('get', "{$this->api}/users", [], $this->headers())
            ->assertResponseStatus(200);
    }

    /** @test */
    public function admin_can_create_manager()
    {
        factory('App\User')->create(['role' => 'admin', 'api_token' => $this->token]);

        $roles = $this->permissions['admin'];
        foreach ($roles as $role => $code)
        {
            $data = factory('App\User')->make(['role' => $role])->attributesToArray();
            $this->json('post', "{$this->api}/users", $data, $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function manager_can_create_scrum_master()
    {
        factory('App\User')->create(['role' => 'manager', 'api_token' => $this->token]);

        $roles = $this->permissions['manager'];
        foreach ($roles as $role => $code)
        {
            $data = factory('App\User')->make(['role' => $role])->attributesToArray();
            $this->json('post', "{$this->api}/users", $data, $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function scrum_master_can_create_developer()
    {
        factory('App\User')->create(['role' => 'scrum_master', 'api_token' => $this->token]);

        $roles = $this->permissions['scrum_master'];
        foreach ($roles as $role => $code)
        {
            $data = factory('App\User')->make(['role' => $role])->attributesToArray();
            $this->json('post', "{$this->api}/users", $data, $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function developer_cant_create_user()
    {
        factory('App\User')->create(['role' => 'developer', 'api_token' => $this->token]);

        $roles = $this->permissions['developer'];
        foreach ($roles as $role => $code)
        {
            $data = factory('App\User')->make(['role' => $role])->attributesToArray();
            $this->json('post', "{$this->api}/users", $data, $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function admin_can_update_manager()
    {
        $projects = [];
        for($i = 1; $i < rand(0,10); $i++)
        {
            $projects []= factory('App\Project')->create()->id;
        }

        factory('App\User')->create(['role' => 'admin', 'api_token' => $this->token]);

        $roles = $this->permissions['admin'];
        foreach ($roles as $role => $code)
        {
            $edited_user = factory('App\User')->create(['role' => $role]);
            $this->json('put', "{$this->api}/users/{$edited_user->id}", ['projects' => $projects], $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function manager_can_update_scrum_master()
    {
        $projects = [];
        for($i = 1; $i < rand(0,10); $i++)
        {
            $projects []= factory('App\Project')->create()->id;
        }

        factory('App\User')->create(['role' => 'manager', 'api_token' => $this->token]);

        $roles = $this->permissions['manager'];
        foreach ($roles as $role => $code)
        {
            $edited_user = factory('App\User')->create(['role' => $role]);
            $this->json('put', "{$this->api}/users/{$edited_user->id}", ['projects' => $projects], $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function scrum_master_can_update_developer()
    {
        $projects = [];
        for($i = 1; $i < rand(0,10); $i++)
        {
            $projects []= factory('App\Project')->create()->id;
        }

        factory('App\User')->create(['role' => 'scrum_master', 'api_token' => $this->token]);

        $roles = $this->permissions['scrum_master'];
        foreach ($roles as $role => $code)
        {
            $edited_user = factory('App\User')->create(['role' => $role]);
            $this->json('put', "{$this->api}/users/{$edited_user->id}", ['projects' => $projects], $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function developer_cant_update_user()
    {
        $projects = [];
        for($i = 1; $i < rand(0,10); $i++)
        {
            $projects []= factory('App\Project')->create()->id;
        }

        factory('App\User')->create(['role' => 'developer', 'api_token' => $this->token]);

        $roles = $this->permissions['developer'];
        foreach ($roles as $role => $code)
        {
            $edited_user = factory('App\User')->create(['role' => $role]);
            $this->json('put', "{$this->api}/users/{$edited_user->id}", ['projects' => $projects], $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function a_user_can_read_a_record()
    {
        $user = factory('App\User')->create(['api_token' => $this->token]);

        $this->json('get',
            "{$this->api}/users/{$user->id}", [], $this->headers())
            ->assertResponseStatus(200);
    }

    /** @test */
    public function admin_can_delete_manager()
    {
        factory('App\User')->create(['role' => 'admin', 'api_token' => $this->token]);
        $roles = $this->permissions['admin'];
        foreach ($roles as $role => $code)
        {
            $deleted_user = factory('App\User')->create(['role' => $role]);
            $this->json('delete',"{$this->api}/users/{$deleted_user->id}", [], $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function manager_can_delete_scrum_master()
    {
        factory('App\User')->create(['role' => 'manager', 'api_token' => $this->token]);
        $roles = $this->permissions['manager'];
        foreach ($roles as $role => $code)
        {
            $deleted_user = factory('App\User')->create(['role' => $role]);
            $this->json('delete',"{$this->api}/users/{$deleted_user->id}", [], $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function scrum_master_can_delete_developer()
    {
        factory('App\User')->create(['role' => 'scrum_master', 'api_token' => $this->token]);
        $roles = $this->permissions['scrum_master'];
        foreach ($roles as $role => $code)
        {
            $deleted_user = factory('App\User')->create(['role' => $role]);
            $this->json('delete',"{$this->api}/users/{$deleted_user->id}", [], $this->headers())->assertResponseStatus($code);
        }
    }

    /** @test */
    public function developer_cant_delete_user()
    {
        factory('App\User')->create(['role' => 'developer', 'api_token' => $this->token]);
        $roles = $this->permissions['developer'];
        foreach ($roles as $role => $code)
        {
            $deleted_user = factory('App\User')->create(['role' => $role]);
            $this->json('delete',"{$this->api}/users/{$deleted_user->id}", [], $this->headers())->assertResponseStatus($code);
        }
    }
}
