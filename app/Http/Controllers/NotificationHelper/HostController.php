<?php

namespace App\Http\Controllers\NotificationHelper;

#use Illuminate\Http\Request;

use App\Enums\NagiosPluginErrno;
use App\Http\Controllers\Controller;
use App\Model\Eloquent\centreon\Host;
use App\Services\CentreonModel\HostService;
use App\Services\CentreonRestApi\CentreonInternalRestApiService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

class HostController extends Controller {

    /**
     * @param string $hostName
     * @return mixed
     */
    public function getPollerIdFromHostName ($hostName) {
        $host = Host::whereHostName($hostName)->first();

        $poller = $host->pollers()->first();

        return $poller->id;
    }

    /**
     * @param integer $hostId
     * @return integer
     */
    public function getPollerIdFromHostId ($hostId) {
        $host = Host::find($hostId);

        $poller = $host->pollers()->first();

        return $poller->id;
    }


    public function getStatus ($hostName) {
        $hostSvc = new HostService();

        $host = Host::whereHostName($hostName)->first();
        $storageHost = $hostSvc->getInstanceSyncedInCentreonStorageDb($host);

        $state = $storageHost->state;
		if (isset($state))
			$state = intval($state);

        $status = 'UNKNOWN';
        if (is_int($state)) {
            switch ($state) {
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


    public function isAcked ($hostName) {
        $hostSvc = new HostService();

        $host = Host::whereHostName($hostName)->first();
        $storageHost = $hostSvc->getInstanceSyncedInCentreonStorageDb($host);

        if ($storageHost->acknowledged)
            return response()->json(['acked' => true]);
        else
            return response()->json(['acked' => false]);
    }


    public function ackHost ($hostName) {
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

        $resp = $restApiSvc->acknowledgeHost($hostName, $poller->getKey(), $author, $comment, $isSticky, $doNotify, $isPersistent, $timestamp);
        return response()->json($resp);
    }


    public function unackHost ($hostName) {
        $host = Host::whereHostName($hostName)->first();

        $timestamp = Input::get('timestamp', null);

        /* @var \App\Model\Eloquent\centreon\Poller $poller */
        $poller = $host->pollers()->first();

        $restApiSvc = new CentreonInternalRestApiService(config('app.centreon_internal_rest_api_url'),
            config('app.centreon_rest_api_username'), config('app.centreon_rest_api_password'));

        $resp = $restApiSvc->unacknowledgeHost($hostName, $poller->getKey(), $timestamp);
        return response()->json($resp);
    }
}