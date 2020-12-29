<?php

namespace App\Http\Controllers;

use App\Model\adsnetworks;
use App\Model\game;
use App\Model\reportadsnetwork;
use App\Model\reportcountry;
use App\Model\reportdata;
use App\Model\reportgame;
use App\Model\timeupdatedata;
use Illuminate\Http\Request;

class IronsourceController extends Controller
{
	public function getTokenHeader()
	{
		$authURL = 'https://platform.ironsrc.com/partners/publisher/auth';
		$authHeaders = array(
			'secretkey: ' . config('ironsource.SecretKey') . '',
			'refreshToken: ' . config('ironsource.RefreshToken') . '',
		);

		$curlClient = curl_init($authURL);
		curl_setopt($curlClient, CURLOPT_HTTPHEADER, $authHeaders);
		curl_setopt($curlClient, CURLOPT_RETURNTRANSFER, true);
		$bearerTokenResponse = curl_exec($curlClient);
		$bearerToken = str_replace('"', '', $bearerTokenResponse);
		curl_close($curlClient);

		return $bearerToken;
	}

	public function getRevenueIronsource($appKey, $date)
	{
		if ($appKey == '' || $date == '') return json_encode(array('status' => 0, 'message' => 'appKey/date not found'));

		$tokenHeader = $this->getTokenHeader();
		$crl = curl_init();
		$header = array();
		$header[] = "Authorization: Bearer " . $tokenHeader;
		$URL = "https://platform.ironsrc.com/partners/adRevenueMeasurements/v1?appKey=" . $appKey . "&date=" . $date;
		curl_setopt($crl, CURLOPT_URL, $URL);
		curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($crl);
		curl_close($crl);

		$response = json_decode($response, true);

		$timeupdatedata_obj = new timeupdatedata();

		if (isset($response['urls'])) {
			$url = $response['urls'][0];

			$file_url = basename($url);
			$file_name = explode("?", $file_url)[0];

			//$file_name = "report.csv";
			$dir = getcwd() . "/adsironsource/" . $file_name;
			$out = fopen($dir, "wp");
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_FILE, $out);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);

			curl_exec($ch);
			//echo "<br>Error is : " . curl_error($ch);
			curl_close($ch);
			fclose($out);

			$dest = gzopen($dir, 'r');
			if (!$dest) {
				echo 'Could not open destination file';
			}

			$arr_report = [];
			$row = 0;
			while (($data = fgetcsv($dest, 1000, ",")) !== FALSE) {
				if ($row == 0) {
					if ($data[7] != 'country') {
						$file_name_error = str_replace("report", "report" . date('YmdHis'), $file_name);
						$dir_error = getcwd() . "/adsironsource/errorfile/" . $file_name_error;
						copy($dir, $dir_error);
						$timeupdatedata_obj->insertTimeUpdate($date, 'ironsource', 'country sai 0- ' . $data[7]);
						return json_encode(array('status' => 0, 'message' => 'country sai'));
					}
				}
				if ($row > 0) {
					if (isset($data[5]) && isset($data[7]) && isset($data[11])) {
						$adsnetwork = $data[5];
						$country = $data[7];
						$revenue = is_numeric($data[11]) ? $data[11] : 0;

						if (strlen($country) > 2) {
							$timeupdatedata_obj->insertTimeUpdate($date, 'ironsource', 'country sai ' . $country);
							return json_encode(array('status' => 0, 'message' => 'country sai'));
						}

						if (empty($arr_report[$adsnetwork][$country]['revenue'])) $arr_report[$adsnetwork][$country]['revenue'] = 0;

						$arr_report[$adsnetwork][$country]['revenue'] += $revenue;
					}
				}

				$row++;
			}

			fclose($dest);

			$result['status'] = 1;
			$result['message'] = 'ok';
			$result['arr_report'] = $arr_report;

			return json_encode($result);

		} else {
			return json_encode(array('status' => 0, 'message' => 'urls not found'));
		}

	}

	public function insertIronsource($arr_date, $games, $arr_adsnetworks)
	{
		$reportdata_obj = new reportdata();
		$timeupdatedata_obj = new timeupdatedata();

		foreach ($arr_date as $date) {
			$timeupdatedata_obj->insertTimeUpdate($date, 'ironsource', 'ok vao');
			foreach ($games as $item) {
				if (isset($item->ironscource_appkey) && !empty($item->ironscource_appkey)) {
					$gameid = $item->gameid;
					$appKey = $item->ironscource_appkey;
					$ironsource = $this->getRevenueIronsource($appKey, $date);
					$ironsource = json_decode($ironsource, true);
					if ($ironsource['status'] == 1) {

						$arr_ironsource = $ironsource['arr_report'];

						// report data
						foreach ($arr_ironsource as $adsnetwork => $item_country) {
							$adsnetwork_id = empty($arr_adsnetworks[$adsnetwork]) ? '' : $arr_adsnetworks[$adsnetwork];
							foreach ($item_country as $country => $item_data) {
								if ($gameid == 1004) {
									// Gun Clash 3D: Epic battle Android
									if ($adsnetwork_id == 3 || $adsnetwork_id == 4) {
										// ironsource , unity
										$reportdata_obj->insertReportdata_revenue($date, $gameid, $adsnetwork_id, $country, $item_data['revenue']);
									}
								} else {
									$reportdata_obj->insertReportdata_revenue($date, $gameid, $adsnetwork_id, $country, $item_data['revenue']);
								}
							}
						}
					}
				}
			}
			$timeupdatedata_obj->insertTimeUpdate($date, 'ironsource', 'ok xong');
		}

		return 1;
	}

	public function getIronsource()
	{
		set_time_limit(1800);
		$datetoday = date('Y-m-d');
		$ngay_hom_truoc_kia = date('Y-m-d', strtotime($datetoday . " -3 day"));
		$ngay_hom_truoc = date('Y-m-d', strtotime($datetoday . " -2 day"));
		$date = '2020-12-26';

		$adsnetworks_obj = new adsnetworks();
		$adsnetworks = $adsnetworks_obj->getListAdsnetworks();
		$arr_adsnetworks = [];
		foreach ($adsnetworks as $item) {
			$arr_adsnetworks[$item->adsnetworkname] = $item->adsnetworkid;
		}

		$game_obj = new game();
		$games = $game_obj->getListGame();

		$reportdata_obj = new reportdata();

		$dem = 0;
		foreach ($games as $item) {
			$dem++;
			if ($item->gameid == 1004) {
			if (isset($item->ironscource_appkey) && !empty($item->ironscource_appkey)) {
				$gameid = $item->gameid;

				echo "<pre>";
				print_r('----------- ' . $gameid . ' --------------');
				echo "</pre>";

				$appKey = $item->ironscource_appkey;
				$ironsource = $this->getRevenueIronsource($appKey, $date);
				$ironsource = json_decode($ironsource, true);

				echo "<pre>";
				print_r($ironsource['message']);
				echo "</pre>";

				if ($ironsource['status'] == 1) {
					$arr_ironsource = $ironsource['arr_report'];


					/*echo "<pre>";
					print_r('sum: ' . $sum_revenue_game);
					echo "</pre>";

					echo "<pre>";
					print_r($arr_ironsource);
					echo "</pre>";

					echo "<pre>";
					print_r($arr_report_adsnetwork);
					echo "</pre>";

					echo "<pre>";
					print_r($arr_report_country);
					echo "</pre>";*/
					/*echo "<pre>";
					print_r($arr_report_adsnetwork);
					echo "</pre>";*/


					/*foreach ($arr_report_adsnetwork as $adsnetwork => $item_adsnetwork) {
						if (isset($arr_adsnetworks[$adsnetwork])) {
							$adsnetwork_id = $arr_adsnetworks[$adsnetwork];
							echo "<pre>";
							print_r([$adsnetwork_id, $adsnetwork, $item_adsnetwork['revenue']]);
							echo "</pre>";

						}
					}*/


					/*foreach ($arr_ironsource as $adsnetwork => $item_country) {
						foreach ($item_country as $country => $item) {
							$adsnetwork_id = $arr_adsnetworks[$adsnetwork];
							$reportdata_obj->insertReportdata_revenue($ngay_hom_truoc, $gameid, $adsnetwork_id, $country, $item['revenue']);
						}
					}*/
				}

				echo "<pre>";
				print_r('-------------------------------------------');
				echo "</pre>";
			}
			}
		}
	}
}
