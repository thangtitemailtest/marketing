<?php

namespace App\Http\Controllers;

use App\Model\adsnetworks;
use App\Model\game;
use App\Model\reportdata;
use App\Model\settinggetrevenue;
use App\Model\timeupdatedata;
use Illuminate\Http\Request;

class ApplovinController extends Controller
{
	public function getApplovin()
	{
		set_time_limit(3600);

		$adsnetworks_obj = new adsnetworks();
		$adsnetworks = $adsnetworks_obj->getListAdsnetworks();

		$appkey = "ZL2HawAIwQJUSbKzAsprLkxcHQoqh44KEDwb7L3wHi4KyKT8R2RS6ZZt8nnU52TxC9kEraTJfOsl8TizTrwzxW";

		$crl = curl_init();
		$URL = "http://r.applovin.com/maxReport?api_key=" . $appkey . "&start=2021-01-01&end=2021-01-01&format=json&columns=day,application,package_name,platform,network,country,estimated_revenue";
		curl_setopt($crl, CURLOPT_URL, $URL);
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($crl);
		curl_close($crl);
		/*echo "<pre>";
		print_r($response);
		echo "</pre>";*/

		$response = json_decode($response, true);

		$game_obj = new game();
		$game_arr = $game_obj->getListGameArrayPakage();
		$game_arr_bundleid = $game_obj->getListGameArrayBundleid();
		$settinggetrevenue_obj = new settinggetrevenue();
		$kenh = 'applovin';

		//$adsnetwork_id = 5;

		$sum_revenue = 0;

		$data = $response['results'];
		foreach ($data as $item) {
			$appname = $item['application'];
			$packagename = $item['package_name'];
			$countrycode = strtoupper($item['country']);
			$revenue = is_numeric($item['estimated_revenue']) ? $item['estimated_revenue'] : 0;
			$platform = $item['platform'];
			$adsnetwork_name = strtoupper($item['network']);

			if ($platform == 'ios') {
				if (isset($game_arr_bundleid[$packagename])) {
					$gameid = $game_arr_bundleid[$packagename];
					if ($gameid == 1001) {
						$adsnetwork_id = 0;

						foreach ($adsnetworks as $item_network) {
							$adsnetworkname = strtoupper($item_network->adsnetworkname);

							$check_adsnetwork = strpos($adsnetwork_name, $adsnetworkname);
							if (is_numeric($check_adsnetwork)) {
								$adsnetwork_id = $item_network->adsnetworkid;

								break;
							}
						}

						if ($adsnetwork_id > 0) {
							$settinggetrevenue = $settinggetrevenue_obj->getSettingWhereGameKenhToArr($gameid, $kenh);
							if (isset($settinggetrevenue[$adsnetwork_id])) {
								echo "<pre>";
								print_r([$appname, $packagename, $countrycode, $revenue, $platform, $adsnetwork_name, $adsnetwork_id]);
								echo "</pre>";

								$sum_revenue += $revenue;
							}
						}
					}
				}
			}
		}

		echo "<pre>";
		print_r($sum_revenue);
		echo "</pre>";

	}

	public function insertApplovin($arr_date)
	{
		$appkey = "ZL2HawAIwQJUSbKzAsprLkxcHQoqh44KEDwb7L3wHi4KyKT8R2RS6ZZt8nnU52TxC9kEraTJfOsl8TizTrwzxW";

		$reportdata_obj = new reportdata();
		$game_obj = new game();
		$game_arr = $game_obj->getListGameArrayPakage();
		$game_arr_bundleid = $game_obj->getListGameArrayBundleid();
		$timeupdatedata_obj = new timeupdatedata();

		$settinggetrevenue_obj = new settinggetrevenue();
		$kenh = 'applovin';

		$adsnetworks_obj = new adsnetworks();
		$adsnetworks = $adsnetworks_obj->getListAdsnetworks();

		foreach ($arr_date as $date) {
			$timeupdatedata_obj->insertTimeUpdate($date, 'applovin', 'ok vao');

			$crl = curl_init();
			$URL = "http://r.applovin.com/maxReport?api_key=" . $appkey . "&start=" . $date . "&end=" . $date . "&format=json&columns=day,application,package_name,platform,network,country,estimated_revenue";
			curl_setopt($crl, CURLOPT_URL, $URL);
			curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($crl);
			curl_close($crl);

			$response = json_decode($response, true);

			if (empty($response)) {
				$timeupdatedata_obj->insertTimeUpdate($date, 'applovin', 'erorr: empty response');

			} else {
				$data = $response['results'];
				foreach ($data as $item) {
					$appname = $item['application'];
					$packagename = $item['package_name'];
					$countrycode = strtoupper($item['country']);
					$revenue = is_numeric($item['estimated_revenue']) ? $item['estimated_revenue'] : 0;
					$platform = $item['platform'];
					$adsnetwork_name = strtoupper($item['network']);

					$adsnetwork_id = 0;

					foreach ($adsnetworks as $item_network) {
						$adsnetworkname = strtoupper($item_network->adsnetworkname);

						$check_adsnetwork = strpos($adsnetwork_name, $adsnetworkname);
						if (is_numeric($check_adsnetwork)) {
							$adsnetwork_id = $item_network->adsnetworkid;

							break;
						}
					}

					if ($adsnetwork_id > 0) {
						if ($platform == 'android') {
							if (isset($game_arr[$packagename])) {
								$gameid = $game_arr[$packagename];

								$settinggetrevenue = $settinggetrevenue_obj->getSettingWhereGameKenhToArr($gameid, $kenh);
								if (isset($settinggetrevenue[$adsnetwork_id])) {
									$reportdata_obj->insertReportdata_revenue_applovin($date, $gameid, $adsnetwork_id, $countrycode, $revenue);
								}
							}
						}

						if ($platform == 'ios') {
							if (isset($game_arr_bundleid[$packagename])) {
								$gameid = $game_arr_bundleid[$packagename];

								$settinggetrevenue = $settinggetrevenue_obj->getSettingWhereGameKenhToArr($gameid, $kenh);
								if (isset($settinggetrevenue[$adsnetwork_id])) {
									$reportdata_obj->insertReportdata_revenue_applovin($date, $gameid, $adsnetwork_id, $countrycode, $revenue);
								}
							}
						}
					}
				}

				$timeupdatedata_obj->insertTimeUpdate($date, 'applovin', 'ok xong');
			}
		}
	}
}
