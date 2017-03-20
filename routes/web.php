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
 *
 * @category Interview
 * @package  Routes
 * @author   Georgi Staykov <g.staikov85@gmail.com>
 * @license  Just Man
 * @link     localhost
 */

use Illuminate\Support\Facades\Event;
/*
Event::listen(
    'Illuminate\Database\Events\QueryExecuted',
    function ($query) {
        $data = [
            'sql'    => $query->sql,
            'params' => $query->bindings,
            'time'   => $query->time,
        ];

        print_r(json_encode($data) . ',');
    }
);*/

$app->group(
    [
    ],
    function () use ($app) {
        $app->get('/', 'AccountController@index');
        $app->post('/registration', 'AccountController@register');
        $app->post('/login', 'AccountController@login');
    }
);

$app->group(
    [
        'middleware' => 'auth',
    ],
    function () use ($app) {
        // Users.
        //$app->get('/users/', 'AccountController@index');
        $app->post(
            '/users/create',
            'AccountController@store'
        );
        $app->get(
            '/users/{user_id}',
            'AccountController@show'
        );
        $app->put(
            '/users/{user_id}/edit',
            'AccountController@update'
        );
        $app->delete(
            '/users/{user_id}/delete',
            'AccountController@destroy'
        );
        
        // Configurations.
        $app->get(
            '/configurations/{configuration_id}',
            'ConfigurationController@show'
        );
        $app->put(
            '/configurations/{configuration_id}/edit',
            'ConfigurationController@update'
        );

        // Files.
        $app->get(
            '/files/{id}',
            'FileController@index'
        );
        $app->get(
            '/files/{id}/{fileName}',
            'FileController@show'
        );
        $app->put(
            '/files/{id}/upload',
            'FileController@upload'
        );
        $app->delete(
            '/files/{id}/{fileName}/delete',
            'FileController@delete'
        );
    }
);
