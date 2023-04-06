<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

// Appointment routes
$router->group(['prefix' => 'appointments'], function () use ($router) {
	$router->get('/', 'AppointmentController@index');
	$router->get('/{id}', 'AppointmentController@get');
	$router->post('/', 'AppointmentController@create');
	$router->delete('/{id}', 'AppointmentController@delete');
});

// AppointmentDate routes
$router->group(['prefix' => 'appointment-dates'], function () use ($router) {
	$router->get('/', 'AppointmentDateController@index');
	$router->get('/{id}', 'AppointmentDateController@get');
});

// Vote routes
$router->group(['prefix' => 'votes'], function () use ($router) {
	$router->get('/', 'VoteController@index');
	$router->get('/{id}', 'VoteController@get');
	$router->post('/', 'VoteController@create');
});

// Comment routes
$router->group(['prefix' => 'comments'], function () use ($router) {
	$router->get('/', 'CommentController@index');
	$router->delete('/{id}', 'CommentController@delete');
});




