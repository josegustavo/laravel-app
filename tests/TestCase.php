<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

//    use DatabaseMigrations;

    protected $permissions = [

        'scrum_master' => [
            'admin' => 403,
            'manager' => 403,
            'scrum_master' => 403,
            'developer' => 200,
        ],
        'developer' => [
            'admin' => 403,
            'manager' => 403,
            'scrum_master' => 403,
            'developer' => 403,
        ],
        'admin' => [
            'admin' => 403,
            'manager' => 200,
            'scrum_master' => 403,
            'developer' => 403,
        ],
        'manager' => [
            'admin' => 403,
            'manager' => 403,
            'scrum_master' => 200,
            'developer' => 403,
        ],
    ];

    protected $api = '/api/v1.0';

    protected $token;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->token = \Illuminate\Support\Str::random(30);
    }

    protected  function headers($token=null)
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . ($token??$this->token)
        ];
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
