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

/*
| =========================================================================
| GENERAL ROUTES
| =========================================================================
*/
$router->get('/', function () use ($router) {
    return sha1(\Illuminate\Support\Str::random(32));
});

/*
| =========================================================================
| AUTH ROUTES
| =========================================================================
*/
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', ['uses' => 'AuthController@register']);
    $router->post('/login', ['uses' => 'AuthController@login']);
    $router->post('/logout', ['uses' => 'AuthController@logout']);
});

/*
| =========================================================================
| USER PROFILES ROUTES
| =========================================================================
*/
$router->group(['prefix' => 'profiles'], function () use ($router) {
    $router->get('/{id_user}', ['uses' => 'ProfileController@profile']);
    $router->post('/{id_user}', ['uses' => 'ProfileController@updateProfile']);
});
