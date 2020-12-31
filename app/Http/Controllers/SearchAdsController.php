<?php

namespace App\Http\Controllers;

use App\Model\game;
use App\Model\reportdata;
use App\Model\timeupdatedata;
use Illuminate\Http\Request;

class SearchAdsController extends Controller
{
	public function getSearchads()
	{
		//$dir = getcwd() . "/searchads/xgameapi.p12";
		$dir = getcwd() . "/searchads/";
		echo "<pre>";
		print_r([$dir, 1]);
		echo "</pre>";

		$crl = curl_init();
		$header = array();
		$header[] = "Authorization: orgId=1756810";
		$header[] = "Content-Type: application/json";
		$body = '{
    "startTime": "2020-12-23",
    "endTime": "2020-12-23",
    "selector": {
        "orderBy": [
            {
                "field": "countryOrRegion",
                "sortOrder": "ASCENDING"
            }
        ],
        "conditions": [
            {
                "field": "servingStatus",
                "operator": "EQUALS",
                "values": [
                    "RUNNING"
                ]
            }
        ],
        "pagination": {
            "offset": 0,
            "limit": 1000
        }
    },
    "groupBy": [
        "countryOrRegion"
    ],
    "returnRowTotals": true
}';
		$URL = "https://api.searchads.apple.com/api/v3/reports/campaigns";
		curl_setopt($crl, CURLOPT_URL, $URL);
		curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($crl, CURLOPT_POSTFIELDS, $body);
		curl_setopt($crl, CURLOPT_SSLCERT, $dir . 'xgameapi.crt.pem');
		curl_setopt($crl, CURLOPT_SSLKEY, $dir . 'xgameapi.key.pem');
		curl_setopt($crl, CURLOPT_SSLCERTPASSWD, '123456');
		curl_setopt($crl, CURLOPT_SSLKEYPASSWD, '123456');
		//curl_setopt($crl, CURLOPT_SSLCERT, $dir);
		//curl_setopt($crl, CURLOPT_SSLCERTTYPE, 'p12');
		//curl_setopt($crl, CURLOPT_SSLCERTPASSWD, '123456');
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($crl);
		/*$error = curl_error($crl);
		echo "<pre>";
		print_r($error);
		echo "</pre>";*/
		curl_close($crl);

		echo "<pre>";
		print_r($response);
		echo "</pre>";


		$response = json_decode($response, true);

		$game_obj = new game();
		$arr_game = $game_obj->getListGameArrayPakage();

		$data = $response['data']['reportingDataResponse']['row'];
		foreach ($data as $item) {
			$total = $item['total'];
			$metadata = $item['metadata'];
			$gamename = $metadata['app']['appName'];
			$packagename = $metadata['app']['adamId'];
			$camid = $metadata['campaignId'];

			if (isset($arr_game[$packagename])) {
				$gameid = $arr_game[$packagename];

				$countrycode = $metadata['countryOrRegion'];
				$budget = $metadata['totalBudget']['amount'];
				$cost = empty($total['localSpend']['amount']) ? 0 : $total['localSpend']['amount'];
				$cost = $cost * config('tygia.cost');
				$ctr = empty($total['ttr']) ? 0 : $total['ttr'];
				$ctr = round($ctr * 100, 2);
				$cr = empty($total['conversionRate']) ? 0 : $total['conversionRate'];
				$cr = round($cr * 100, 2);
				$install = $total['installs'];

				$crl2 = curl_init();
				$URL2 = "https://api.searchads.apple.com/api/v3/campaigns/" . $camid . "/adgroups";
				curl_setopt($crl2, CURLOPT_URL, $URL2);
				curl_setopt($crl2, CURLOPT_HTTPHEADER, $header);
				curl_setopt($crl2, CURLOPT_SSLCERT, $dir . 'xgameapi.crt.pem');
				curl_setopt($crl2, CURLOPT_SSLKEY, $dir . 'xgameapi.key.pem');
				curl_setopt($crl2, CURLOPT_SSLCERTPASSWD, '123456');
				curl_setopt($crl2, CURLOPT_SSLKEYPASSWD, '123456');
				curl_setopt($crl2, CURLOPT_RETURNTRANSFER, true);
				$response2 = curl_exec($crl2);
				curl_close($crl2);

				$response2 = json_decode($response2, true);

				$cpitarget = empty($response2['data'][0]['defaultCpcBid']['amount']) ? 0 : $response2['data'][0]['defaultCpcBid']['amount'];

				echo "<pre>";
				print_r([$camid, $gamename, $packagename, $gameid, $countrycode, $budget, $cost, $cpitarget, $ctr, $cr, $install, $cpitarget]);
				echo "</pre>";

			}
		}

	}

	public function insertSearchads($arr_date)
	{
		$timeupdatedata_obj = new timeupdatedata();

		$reportdata = new reportdata();

		$game_obj = new game();
		$arr_game = $game_obj->getListGameArrayPakage();

		$adsnetworkid = 7;

		foreach ($arr_date as $date) {
			$timeupdatedata_obj->insertTimeUpdate($date, 'searchads', 'ok vao');

			//$dir = getcwd() . "/searchads/xgameapi.p12";
			$dir = getcwd() . "/searchads/";

			$crl = curl_init();
			$header = array();
			$header[] = "Authorization: orgId=1756810";
			$header[] = "Content-Type: application/json";
			$body = '{
    "startTime": "' . $date . '",
    "endTime": "' . $date . '",
    "selector": {
        "orderBy": [
            {
                "field": "countryOrRegion",
                "sortOrder": "ASCENDING"
            }
        ],
        "conditions": [
            {
                "field": "servingStatus",
                "operator": "EQUALS",
                "values": [
                    "RUNNING"
                ]
            }
        ],
        "pagination": {
            "offset": 0,
            "limit": 1000
        }
    },
    "groupBy": [
        "countryOrRegion"
    ],
    "returnRowTotals": true
}';
			$URL = "https://api.searchads.apple.com/api/v3/reports/campaigns";
			curl_setopt($crl, CURLOPT_URL, $URL);
			curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($crl, CURLOPT_POSTFIELDS, $body);
			curl_setopt($crl, CURLOPT_SSLCERT, $dir . 'xgameapi.crt.pem');
			curl_setopt($crl, CURLOPT_SSLKEY, $dir . 'xgameapi.key.pem');
			curl_setopt($crl, CURLOPT_SSLCERTPASSWD, '123456');
			curl_setopt($crl, CURLOPT_SSLKEYPASSWD, '123456');
			curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($crl);
			curl_close($crl);

			$response = json_decode($response, true);

			if (empty($response)) {
				$timeupdatedata_obj->insertTimeUpdate($date, 'searchads', 'error: empty response');
			} else {
				$data = $response['data']['reportingDataResponse']['row'];
				foreach ($data as $item) {
					$total = $item['total'];
					$metadata = $item['metadata'];
					//$gamename = $metadata['app']['appName'];
					$packagename = $metadata['app']['adamId'];
					$camid = $metadata['campaignId'];

					if (isset($arr_game[$packagename])) {
						$gameid = $arr_game[$packagename];

						$countrycode = $metadata['countryOrRegion'];
						$budget = $metadata['totalBudget']['amount'];
						$cost = empty($total['localSpend']['amount']) ? 0 : $total['localSpend']['amount'];
						$cost = $cost * config('tygia.cost');
						$ctr = empty($total['ttr']) ? 0 : $total['ttr'];
						$ctr = round($ctr * 100, 2);
						$cr = empty($total['conversionRate']) ? 0 : $total['conversionRate'];
						$cr = round($cr * 100, 2);
						$install = $total['installs'];

						$crl2 = curl_init();
						$URL2 = "https://api.searchads.apple.com/api/v3/campaigns/" . $camid . "/adgroups";
						curl_setopt($crl2, CURLOPT_URL, $URL2);
						curl_setopt($crl2, CURLOPT_HTTPHEADER, $header);
						curl_setopt($crl2, CURLOPT_SSLCERT, $dir . 'xgameapi.crt.pem');
						curl_setopt($crl2, CURLOPT_SSLKEY, $dir . 'xgameapi.key.pem');
						curl_setopt($crl2, CURLOPT_SSLCERTPASSWD, '123456');
						curl_setopt($crl2, CURLOPT_SSLKEYPASSWD, '123456');
						curl_setopt($crl2, CURLOPT_RETURNTRANSFER, true);
						$response2 = curl_exec($crl2);
						curl_close($crl2);

						$response2 = json_decode($response2, true);
						if (empty($response2)){
							$cpitarget = 0;
						}else {
							$cpitarget = empty($response2['data'][0]['defaultCpcBid']['amount']) ? 0 : $response2['data'][0]['defaultCpcBid']['amount'];
						}

						$reportdata->insertReportdata_adwords($date, $gameid, $adsnetworkid, $countrycode, $cost, $budget, $ctr, $cr, $install, $cpitarget);
					}
				}

				$timeupdatedata_obj->insertTimeUpdate($date, 'searchads', 'ok xong');
			}
		}
	}
}
