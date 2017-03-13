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

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;

Event::listen(
    'Illuminate\Database\Events\QueryExecuted',
    function ($query) {
        $data = [
            'sql'    => $query->sql,
            'params' => $query->bindings,
            'time'   => $query->time,
        ];

        print_r(json_encode($data) . PHP_EOL);
    }
);

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
        $app->get('/users/', 'AccountController@index');
        $app->post('/users/', 'AccountController@store');
        $app->get('/users/{user_id}', 'AccountController@show');
        $app->put('/users/{user_id}', 'AccountController@update');
        $app->delete('/users/{user_id}', 'AccountController@destroy');

        // Files.
        //        $app->get('/files', $callback);
        //        $app->put('/save', $callback);
        //        $app->get('/files/{id}', $callback);
        //        $app->delete('/files/{id}/delete', $callback);
        //        $app->delete('/delete', $callback);
    }
);
