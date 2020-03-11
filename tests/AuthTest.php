<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthTest extends TestCase
{
    /** @test **/
    public function a_user_can_login()
    {
        $this->json('post', "{$this->api}/login", ['email' => 'dummy@email.net', 'password' => 'dummypassword'])
            ->assertResponseStatus(500);

        $user = factory('App\User')->create();
        $email = $user->email;
        $this->json('post', "{$this->api}/login", ['email' => $email, 'password' => 'dummypassword'])
            ->assertResponseStatus(200);
    }

    /** @test **/
    public function a_user_can_logout()
    {
        $this->json('post', "{$this->api}/logout", [], $this->headers())
            ->assertResponseStatus(401);

        factory('App\User')->create(['api_token' => $this->token]);
        $this->json('post', "{$this->api}/logout", [], $this->headers())
            ->assertResponseStatus(200);

    }
}