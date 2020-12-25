<?php

namespace App\Http\Controllers;

use App\Model\game;
use App\Model\reportdata;
use App\Model\timeupdatedata;
use Illuminate\Http\Request;

class UnityadsController extends Controller
{
	public function getUnityads()
	{
		$api_key = "5f108ba46b7024d40dbb478ba4f062213e3e2115471bca59bb8fc65b0c56122e";
		$organization_id = "5c9b1c210d7ede00277e9950";

		$crl = curl_init();
		$URL = "https://stats.unityads.unity3d.com/organizations/" . $organization_id . "/reports/acquisitions?splitBy=target,country&start=2020-12-23T00:00:00&end=2020-12-23T23:59:59&scale=day&apikey=" . $api_key;
		curl_setopt($crl, CURLOPT_URL, $URL);
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($crl);
		curl_close($crl);

		$data = array_map('str_getcsv', explode("\n", $response));
		dd($data);
		foreach ($data as $key => $item) {
			if ($key > 0 && !empty($item[0])) {
				$packagename = $item[2];
				$countrycode = $item[4];
				$install = $item[8];
				$cost = $item[9];
				$cr = $item[10];
				$ctr = $item[11];
				$cpi = $item[13];

				echo "<pre>";
				print_r(array('$key' => $key, '$packagename' => $packagename, '$countrycode' => $countrycode, '$install' => $install, '$cost' => $cost, '$cr' => $cr, '$ctr' => $ctr, '$cpi' => $cpi));
				echo "</pre>";

			}
		}

	}

	public function insertUnityads($arr_date)
	{
		$api_key = "5f108ba46b7024d40dbb478ba4f062213e3e2115471bca59bb8fc65b0c56122e";
		$organization_id = "5c9b1c210d7ede00277e9950";

		$games = new game();
		$arr_game = $games->getListGameArrayPakage();

		$reportdata = new reportdata();
		$timeupdatedata_obj = new timeupdatedata();

		$adsnetworkid = 4;


		foreach ($arr_date as $date) {
			$timeupdatedata_obj->insertTimeUpdate($date, 'unity', 'ok vao');

			$start = $date . "T00:00:00";
			$end = $date . "T23:59:59";

			$crl = curl_init();
			$URL = "https://stats.unityads.unity3d.com/organizations/" . $organization_id . "/reports/acquisitions?splitBy=target,country&start=" . $start . "&end=" . $end . "&scale=day&apikey=" . $api_key;
			curl_setopt($crl, CURLOPT_URL, $URL);
			curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($crl);
			curl_close($crl);

			$data = array_map('str_getcsv', explode("\n", $response));

			foreach ($data as $key => $item) {
				if ($key > 0 && !empty($item[0])) {
					$packagename = $item[2];
					$gamename = $item[3];
					$countrycode = $item[4];
					$install = $item[8];
					$cost = $item[9];
					$cr = $item[10];
					$ctr = $item[11];
					$cpi = $item[13];
					$budget = '';

					if (!empty($cost)){
						$cost = $cost * config('tygia.cost');
					}
					if (!empty($cr)){
						$cr = round($cr * 100,2);
					}
					if (!empty($ctr)){
						$ctr = round($ctr * 100,2);
					}

					if (!isset($arr_game[$packagename])) {
						$gameid_moi = $games->insertGame($packagename, $gamename);
						$arr_game[$packagename] = $gameid_moi;
					}

					$gameid = $arr_game[$packagename];

					$reportdata->insertReportdata_adwords($date, $gameid, $adsnetworkid, $countrycode, $cost, $budget, $ctr, $cr, $install, $cpi);
				}
			}

			$timeupdatedata_obj->insertTimeUpdate($date, 'unity', 'ok xong');

			sleep(4);
		}
	}
}
