<?php

namespace App\Http\Controllers;

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

		$appkey = "ZL2HawAIwQJUSbKzAsprLkxcHQoqh44KEDwb7L3wHi4KyKT8R2RS6ZZt8nnU52TxC9kEraTJfOsl8TizTrwzxW";

		$crl = curl_init();
		$URL = "https://r.applovin.com/report?api_key=" . $appkey . "&start=2020-12-30&end=2020-12-30&columns=day,application,package_name,country,revenue&format=json&report_type=publisher";
		curl_setopt($crl, CURLOPT_URL, $URL);
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($crl);
		curl_close($crl);

		echo "<pre>";
		print_r($response);
		echo "</pre>";

		$response = json_decode($response, true);

		$data = $response['results'];
		foreach ($data as $item) {
			$appname = $item['application'];
			$packagename = $item['package_name'];
			$countrycode = strtoupper($item['country']);
			$revenue = is_numeric($item['revenue']) ? $item['revenue'] : 0;
			$revenue = $revenue * config('tygia.revenue');

			echo "<pre>";
			print_r([$appname, $packagename, $countrycode, $revenue]);
			echo "</pre>";
		}
	}

	public function insertApplovin($arr_date)
	{
		$appkey = "ZL2HawAIwQJUSbKzAsprLkxcHQoqh44KEDwb7L3wHi4KyKT8R2RS6ZZt8nnU52TxC9kEraTJfOsl8TizTrwzxW";

		$adsnetwork_id = 5;

		$reportdata_obj = new reportdata();
		$game_obj = new game();
		$game_arr = $game_obj->getListGameArrayPakage();
		$timeupdatedata_obj = new timeupdatedata();

		$settinggetrevenue_obj = new settinggetrevenue();
		$kenh = 'applovin';

		foreach ($arr_date as $date) {
			$timeupdatedata_obj->insertTimeUpdate($date, 'applovin', 'ok vao');

			$crl = curl_init();
			$URL = "https://r.applovin.com/report?api_key=" . $appkey . "&start=" . $date . "&end=" . $date . "&columns=day,application,package_name,country,revenue&format=json&report_type=publisher";
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
					$revenue = is_numeric($item['revenue']) ? $item['revenue'] : 0;

					if (isset($game_arr[$packagename])) {
						$gameid = $game_arr[$packagename];

						$settinggetrevenue = $settinggetrevenue_obj->getSettingWhereGameKenhToArr($gameid, $kenh);
						if (isset($settinggetrevenue[$adsnetwork_id])) {
							$reportdata_obj->insertReportdata_revenue($date, $gameid, $adsnetwork_id, $countrycode, $revenue);
						}

						/*if ($gameid == 1002) {
							// Gang Master Android
							$reportdata_obj->insertReportdata_revenue($date, $gameid, $adsnetwork_id, $countrycode, $revenue);
						}*/
					}
				}

				$timeupdatedata_obj->insertTimeUpdate($date, 'applovin', 'ok xong');
			}
		}
	}
}
