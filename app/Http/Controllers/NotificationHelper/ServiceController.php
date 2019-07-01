<?php

namespace App\Http\Controllers\NotificationHelper;

#use Illuminate\Http\Request;

use App\Enums\NagiosPluginErrno;
use App\Http\Controllers\Controller;
use App\Model\Eloquent\centreon\Host;
use App\Model\Eloquent\centreon\Service;
use App\Services\CentreonModel\HostServiceService;
use App\Services\CentreonModel\ServiceService;
use App\Services\CentreonRestApi\CentreonInternalRestApiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

class ServiceController extends Controller {

    public function getStatus ($hostName, $serviceDescription) {
        $serviceSvc = new ServiceService();
        $hostServiceSvc = new HostServiceService();

        $host = Host::whereHostName($hostName)->first();
        $service = $serviceSvc->getFromName($hostName, $serviceDescription);

        if (empty($service))
            return response()->make("Unable to find service $hostName - $serviceDescription", 404);

        $storageService = $hostServiceSvc->getInstanceSyncedInCentreonStorageDb($host, $service);
        if (empty($storageService))
            return response()->make("Unable to retrieve associated service in storage DB", 404);

        $state = $storageService->state;

        $state = $storageService->state;
		if (isset($state))
			$state = intval($state);

        $status = 'UNKNOWN';
        if (is_int($state)) {
            switch ($storageService->state) {
                case NagiosPluginErrno::OK:
                    $status = 'OK';
                    break;
                case NagiosPluginErrno::WARNING:
                    $status = 'WARNING';
                    break;
                case NagiosPluginErrno::CRITICAL:
                    $status = 'CRITICAL';
                    break;
                case NagiosPluginErrno::PENDING:
                    $status = 'PENDING';
                    break;
                case NagiosPluginErrno::UNKNOWN:
                default:
                    $status = 'UNKNOWN';
            }
        }

        return response()->json(['status' => $status]);
    }

    public function isAcked ($hostName, $serviceDescription) {
        $serviceSvc = new ServiceService();
        $hostServiceSvc = new HostServiceService();

        $host = Host::whereHostName($hostName)->first();
        $service = $serviceSvc->getFromName($hostName, $serviceDescription);

        if (empty($service))
            return response()->make("Unable to find service $hostName - $serviceDescription", 404);

        $storageService = $hostServiceSvc->getInstanceSyncedInCentreonStorageDb($host, $service);
        if (empty($storageService))
            return response()->make("Unable to retrieve associated service in storage DB", 404);

        if ($storageService->acknowledged)
            return response()->json(['acked' => true]);
        else
            return response()->json(['acked' => false]);
    }

    public function ackService ($hostName, $serviceDescription) {
        $host = Host::whereHostName($hostName)->first();

        $author = Input::get('author', 'centreon_manager');
        $comment = Input::get('comment', '');
        $isSticky = Input::get('sticky', '2');
        $doNotify = Input::get('notify', '0');
        $isPersistent = Input::get('persistent', '0');
        $timestamp = Input::get('timestamp', null);

        /* @var \App\Model\Eloquent\centreon\Poller $poller */
        $poller = $host->pollers()->first();

        $restApiSvc = new CentreonInternalRestApiService(config('app.centreon_internal_rest_api_url'),
            config('app.centreon_rest_api_username'), config('app.centreon_rest_api_password'));

        $resp = $restApiSvc->acknowledgeService($hostName, $serviceDescription, $poller->getKey(), $author, $comment, $isSticky, $doNotify, $isPersistent, $timestamp);
        return response()->json($resp);
    }

    public function unackService ($hostName, $serviceDescription) {
        $host = Host::whereHostName($hostName)->first();

        $timestamp = Input::get('timestamp', null);

        /* @var \App\Model\Eloquent\centreon\Poller $poller */
        $poller = $host->pollers()->first();

        $restApiSvc = new CentreonInternalRestApiService(config('app.centreon_internal_rest_api_url'),
            config('app.centreon_rest_api_username'), config('app.centreon_rest_api_password'));

        $resp = $restApiSvc->unacknowledgeService($hostName, $serviceDescription, $poller->getKey(), $timestamp);
        return response()->json($resp);
    }
}