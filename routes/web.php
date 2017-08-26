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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function($api) {
    $api->get('/hello', 'App\Http\Controllers\TestController@hello');

    $api->get('/students', 'App\Http\Controllers\StudentController@getAllStudents');
    $api->get('/students/{student_number}', 'App\Http\Controllers\StudentController@getStudent');

    $api->get('/cys', 'App\Http\Controllers\StudentController@getAllCy');
    $api->get('/cys/{student_number}', 'App\Http\Controllers\StudentController@getCy');

    $api->get('/preferences', 'App\Http\Controllers\StudentController@getAllPreferences');
    $api->get('/preferences/{student_number}', 'App\Http\Controllers\StudentController@getPreference');

    $api->get('/schedules/{student_number}/{preference_id}', 'App\Http\Controllers\StudentController@getSchedules');
    $api->get('/grades/{student_number}/{preference_id}', 'App\Http\Controllers\StudentController@getGrades');
});

// $app->get('/', function () use ($app) {
//     return $app->version();
// });
