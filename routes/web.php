<?php

/**
 *--------------------------------------------------------------------------
 * Application Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register all of the routes for an application.
 * It is a breeze. Simply tell Lumen the URIs it should respond to
 * and give it the Closure to call when that URI is requested.
 *
 * PHP version 7.0
 */

$app->get('/', 'AccountController@index');
$app->post('/registration', 'AccountController@register');
$app->post('/login', 'AccountController@login');
$app->group(
    [
        'prefix'     => '/{account_id}',
        'middleware' => 'auth',
    ],
    function () use ($app) {
        // Users
        $app->get('/users/', 'AccountController@index');
        $app->post('/users/', 'AccountController@store');
        $app->get('/users/{user_id}', 'AccountController@show');
        $app->put('/users/{user_id}', 'AccountController@update');
        $app->delete('/users/{user_id}', 'AccountController@destroy');

        //        $app->get('/files', $callback);
        //        $app->put('/save', $callback);
        //        $app->get('/files/{id}', $callback);
        //        $app->delete('/files/{id}/delete', $callback);
        //        $app->delete('/delete', $callback);
    }
);
