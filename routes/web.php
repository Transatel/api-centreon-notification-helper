<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

$hotnameRegex = '[A-Za-z0-9_/.-]+';


// ------------------------------------------------------------------------
// NOTIFICATION

// used for conditionally including graph in notifications
Route::get('notification-helper/has-metrics/{host_service_id}','NotificationHelper\MetricsController@hasMetrics');

Route::get('notification-helper/host-associated-poller/{host_name}','NotificationHelper\HostController@getPollerIdFromHostName')->where('host_name', $hotnameRegex);

Route::get('notification-helper/host-status/{host_name}','NotificationHelper\HostController@getStatus')->where('host_name', $hotnameRegex);
Route::get('notification-helper/service-status/{host_name}/{service_description}','NotificationHelper\ServiceController@getStatus')->where('host_name', $hotnameRegex);

Route::get('notification-helper/is-acked-host/{host_name}','NotificationHelper\HostController@isAcked')->where('host_name', $hotnameRegex);
Route::get('notification-helper/ack-host/{host_name}','NotificationHelper\HostController@ackHost')->where('host_name', $hotnameRegex);
Route::get('notification-helper/unack-host/{host_name}','NotificationHelper\HostController@unackHost')->where('host_name', $hotnameRegex);

Route::get('notification-helper/is-acked-service/{host_name}/{service_description}','NotificationHelper\ServiceController@isAcked')->where('host_name', $hotnameRegex);
Route::get('notification-helper/ack-service/{host_name}/{service_description}','NotificationHelper\ServiceController@ackService')->where('host_name', $hotnameRegex);
Route::get('notification-helper/unack-service/{host_name}/{service_description}','NotificationHelper\ServiceController@unackService')->where('host_name', $hotnameRegex);
