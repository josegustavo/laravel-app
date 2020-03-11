<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1.0'], function () use ($router){
    $router->group(['middleware' => 'auth'], function () use ($router) {

        $router->get('users', [
            'as' => 'users.index',
            'uses' => 'UsersController@index'
        ]);

        $router->post('users', [
            'as' => 'users.store',
            'uses' => 'UsersController@store'
        ]);

        $router->get('users/{id}', [
            'as' => 'users.show',
            'uses' => 'UsersController@show'
        ]);

        $router->put('users/{id}', [
            'as' => 'users.update',
            'uses' => 'UsersController@update'
        ]);

        $router->delete('users/{id}', [
            'as' => 'users.delete',
            'uses' => 'UsersController@destroy'
        ]);

        $router->post('logout', [
            'as' => 'logout',
            'uses' => 'LoginController@logout'
        ]);

        $router->get('authorization', [
            'as' => 'authorization',
            'uses' => 'AuthorizationController@index'
        ]);

        $router->get('authorization/{action}', [
            'as' => 'authorizationAction',
            'uses' => 'AuthorizationController@verify'
        ]);
    });

    $router->post('login', [
        'as' => 'login',
        'uses' => 'LoginController@login'
    ]);

});
