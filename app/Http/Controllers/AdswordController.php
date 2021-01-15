<?php

namespace App\Http\Controllers;

use App\Model\game;
use App\Model\reportdata;
use App\Model\timeupdatedata;
use Edujugon\GoogleAds\GoogleAds;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Illuminate\Http\Request;

class AdswordController extends Controller
{
	function getAdsword()
	{
		set_time_limit(3600);

		//$arr_tk_adwords = ['256-247-7293', '184-236-4088', '761-776-7486', '501-116-6276', '877-912-8370', '484-840-6491', '766-066-7300', '741-406-3586'];
		$arr_tk_adwords = ['510-000-4823'];

		$games = new game();
		$arr_game = $games->getListGameArrayPakage();

		$date = '2021-01-14';
		//$reportdata->insertReportdata_adwords($date, 1004, 2, 'AR', '10482', '500,000 đ', '9.53', '84.44', '38');


		foreach ($arr_tk_adwords as $adwords) {
			$ads = new GoogleAds($adwords);
			$ads->env($adwords)
				->session([
					'developerToken' => 'twRYeMqpeDSJQXJYFuBNAQ',
					'clientCustomerId' => $adwords
				]);
			$camp = $ads->service(CampaignService::class)->select(['Id', 'Name', 'Status', 'Settings'])
				->where('Status = "ENABLED"')
				->get()
				->items();
			$dem = 0;
			foreach ($camp as $item) {
				$dem++;
				$camp_id = $item->getId();
				$camp_name = $item->getName();
				$gamename = '';
				$countrycode = '';
				$platform = '';
				$camp_name_exp = explode('_', $camp_name);
				if (isset($camp_name_exp[0]) && isset($camp_name_exp[1]) && isset($camp_name_exp[2])) {
					$gamename = $camp_name_exp[0];
					$countrycode = strtoupper($camp_name_exp[1]);
					$platform = $camp_name_exp[2];
				}

				$settings = $item->getSettings()[1];
				$packagename = $settings->getAppId();

				/*if (!isset($arr_game[$packagename])) {
					$gameid_moi = $games->insertGame($packagename, $gamename, $platform);
					$arr_game[$packagename] = $gameid_moi;
				}

				$gameid = $arr_game[$packagename];*/

				$obj = $ads->report()
					->from('CAMPAIGN_PERFORMANCE_REPORT')
					->during('20210114', '20210114')
					->where('CampaignId = ' . $camp_id)
					->select('CampaignId', 'CampaignName', 'Cost', 'Amount', 'CampaignStatus', 'AccountCurrencyCode', 'Ctr', 'ConversionRate', 'Conversions')
					->getAsObj()->result;

				if ($obj) {
					foreach ($obj as $item_c) {
						$donvitien = $item_c->currency;
						$cost = is_numeric($item_c->cost);
						$cost = round($cost / 1000000, 2);
						$budget = is_numeric($item_c->budget);
						$budget = round($budget / 1000000, 2);
						if ($donvitien == 'USD') {
							$cost = $cost * config('tygia.cost');
							$budget = $budget . " $";
						}
						if ($donvitien == 'VND') {
							$budget = $budget . " đ";
						}
						$ctr = $item_c->ctr;
						$cr = $item_c->convRate;
						$install = $item_c->conversions;

						//$reportdata->insertReportdata_adwords($date, $gameid, $adsnetworkid, $countrycode, $cost, $budget, $ctr, $cr, $install);

						echo "<pre>";
						print_r([$dem, $camp_name, $packagename, $gamename, $countrycode, $donvitien, $cost, $budget, $ctr, $cr, $install]);
						echo "</pre>";
					}
				}
			}
		}
	}

	function insertAdwords($arr_date)
	{
		//set_time_limit(3600);

		$arr_tk_adwords = ['256-247-7293', '184-236-4088', '761-776-7486', '501-116-6276', '877-912-8370', '484-840-6491', '766-066-7300', '741-406-3586', '510-000-4823'];

		$games = new game();
		$arr_game = $games->getListGameArrayPakage();

		$timeupdatedata_obj = new timeupdatedata();

		$reportdata = new reportdata();

		$adsnetworkid = 2;

		foreach ($arr_date as $date) {
			$timeupdatedata_obj->insertTimeUpdate($date, 'adwords', 'ok vao');

			$date_query = date('Ymd', strtotime($date));

			foreach ($arr_tk_adwords as $adwords) {
				$ads = new GoogleAds($adwords);
				$ads->env($adwords)
					->session([
						'developerToken' => 'twRYeMqpeDSJQXJYFuBNAQ',
						'clientCustomerId' => $adwords
					]);
				$camp = $ads->service(CampaignService::class)->select(['Id', 'Name', 'Status', 'Settings'])
					->where('Status = "ENABLED"')
					->get()
					->items();

				foreach ($camp as $item) {
					//$dem++;
					$camp_id = $item->getId();
					$camp_name = $item->getName();
					$camp_name_exp = explode('_', $camp_name);
					$vitricuoi = count($camp_name_exp) - 1;
					$gamename = $camp_name_exp[0];
					$countrycode = strtoupper($camp_name_exp[$vitricuoi]);
					if ($countrycode == "UK") {
						$countrycode = "GB";
					}

					if (strlen($countrycode) <= 6) {
						$settings = $item->getSettings()[1];
						$packagename = $settings->getAppId();

						if (!isset($arr_game[$packagename])) {
							$gameid_moi = $games->insertGame($packagename, $gamename);
							$arr_game[$packagename] = $gameid_moi;
						}

						$gameid = $arr_game[$packagename];

						$obj = $ads->report()
							->from('CAMPAIGN_PERFORMANCE_REPORT')
							->during($date_query, $date_query)
							->where('CampaignId = ' . $camp_id)
							->select('CampaignId', 'CampaignName', 'Cost', 'Amount', 'CampaignStatus', 'AccountCurrencyCode', 'Ctr', 'ConversionRate', 'Conversions')
							->getAsObj()->result;
						foreach ($obj as $item_c) {
							$donvitien = $item_c->currency;
							$cost = is_numeric($item_c->cost) ? $item_c->cost : 0;
							$cost = round($cost / 1000000, 2);
							$budget = is_numeric($item_c->budget) ? $item_c->budget : 0;
							$budget = round($budget / 1000000, 2);
							if ($donvitien == 'USD') {
								$cost = $cost * config('tygia.cost');
								$budget = $budget . " $";
							}
							if ($donvitien == 'VND') {
								$budget = number_format($budget) . " đ";
							}
							$ctr = $item_c->ctr;
							$cr = $item_c->convRate;
							$install = $item_c->conversions;

							$reportdata->insertReportdata_adwords($date, $gameid, $adsnetworkid, $countrycode, $cost, $budget, $ctr, $cr, $install);
						}
					}
				}
			}

			$timeupdatedata_obj->insertTimeUpdate($date, 'adwords', 'ok xong');
		}

		return 1;
	}

	function insertAdwords_theotk($arr_date, $taikhoan, $stt)
	{
		//set_time_limit(3600);

		$arr_tk_adwords = $taikhoan;

		$games = new game();
		$arr_game = $games->getListGameArrayPakage();

		$timeupdatedata_obj = new timeupdatedata();

		$reportdata = new reportdata();

		$adsnetworkid = 2;

		foreach ($arr_date as $date) {
			$timeupdatedata_obj->insertTimeUpdate($date, 'adwords' . $stt, 'ok vao');

			$date_query = date('Ymd', strtotime($date));

			foreach ($arr_tk_adwords as $adwords) {
				$ads = new GoogleAds($adwords);
				$ads->env($adwords)
					->session([
						'developerToken' => 'twRYeMqpeDSJQXJYFuBNAQ',
						'clientCustomerId' => $adwords
					]);
				$camp = $ads->service(CampaignService::class)->select(['Id', 'Name', 'Status', 'Settings'])
					->where('Status = "ENABLED"')
					->get()
					->items();

				foreach ($camp as $item) {
					//$dem++;
					$camp_id = $item->getId();
					$camp_name = $item->getName();
					$camp_name_exp = explode('_', $camp_name);
					$vitricuoi = count($camp_name_exp) - 1;
					$gamename = $camp_name_exp[0];
					$countrycode = strtoupper($camp_name_exp[$vitricuoi]);
					if ($countrycode == "UK") {
						$countrycode = "GB";
					}

					if (strlen($countrycode) <= 6) {
						$settings = $item->getSettings()[1];
						$packagename = $settings->getAppId();

						if (!isset($arr_game[$packagename])) {
							$gameid_moi = $games->insertGame($packagename, $gamename);
							$arr_game[$packagename] = $gameid_moi;
						}

						$gameid = $arr_game[$packagename];

						$obj = $ads->report()
							->from('CAMPAIGN_PERFORMANCE_REPORT')
							->during($date_query, $date_query)
							->where('CampaignId = ' . $camp_id)
							->select('CampaignId', 'CampaignName', 'Cost', 'Amount', 'CampaignStatus', 'AccountCurrencyCode', 'Ctr', 'ConversionRate', 'Conversions')
							->getAsObj()->result;
						foreach ($obj as $item_c) {
							$donvitien = $item_c->currency;
							$cost = is_numeric($item_c->cost) ? $item_c->cost : 0;
							$cost = round($cost / 1000000, 2);
							$budget = is_numeric($item_c->budget) ? $item_c->budget : 0;
							$budget = round($budget / 1000000, 2);
							if ($donvitien == 'USD') {
								$cost = $cost * config('tygia.cost');
								$budget = $budget . " $";
							}
							if ($donvitien == 'VND') {
								$budget = number_format($budget) . " đ";
							}
							$ctr = $item_c->ctr;
							$cr = $item_c->convRate;
							$install = $item_c->conversions;

							$reportdata->insertReportdata_adwords($date, $gameid, $adsnetworkid, $countrycode, $cost, $budget, $ctr, $cr, $install);
						}
					}
				}
			}

			$timeupdatedata_obj->insertTimeUpdate($date, 'adwords' . $stt, 'ok xong');
		}

		return 1;
	}

	function insertAdwords_theotk_tach($arr_date, $taikhoan, $stt, $limit, $offset)
	{
		//set_time_limit(3600);

		$arr_tk_adwords = $taikhoan;

		$games = new game();
		$arr_game = $games->getListGameArrayPakage();

		$timeupdatedata_obj = new timeupdatedata();

		$reportdata = new reportdata();

		$adsnetworkid = 2;

		foreach ($arr_date as $date) {
			$timeupdatedata_obj->insertTimeUpdate($date, 'adwords' . $stt, 'ok vao');

			$date_query = date('Ymd', strtotime($date));

			foreach ($arr_tk_adwords as $adwords) {
				$ads = new GoogleAds($adwords);
				$ads->env($adwords)
					->session([
						'developerToken' => 'twRYeMqpeDSJQXJYFuBNAQ',
						'clientCustomerId' => $adwords
					]);
				$camp = $ads->service(CampaignService::class)->select(['Id', 'Name', 'Status', 'Settings'])
					->where('Status = "ENABLED"')
					->limit($limit, $offset)
					->get()
					->items();

				foreach ($camp as $item) {
					$camp_id = $item->getId();
					$camp_name = $item->getName();
					$camp_name_exp = explode('_', $camp_name);
					$vitricuoi = count($camp_name_exp) - 1;
					$gamename = $camp_name_exp[0];
					$countrycode = strtoupper($camp_name_exp[$vitricuoi]);
					if ($countrycode == "UK") {
						$countrycode = "GB";
					}

					if (strlen($countrycode) <= 6) {
						$settings = $item->getSettings()[1];
						$packagename = $settings->getAppId();

						if (!isset($arr_game[$packagename])) {
							$gameid_moi = $games->insertGame($packagename, $gamename);
							$arr_game[$packagename] = $gameid_moi;
						}

						$gameid = $arr_game[$packagename];

						$obj = $ads->report()
							->from('CAMPAIGN_PERFORMANCE_REPORT')
							->during($date_query, $date_query)
							->where('CampaignId = ' . $camp_id)
							->select('CampaignId', 'CampaignName', 'Cost', 'Amount', 'CampaignStatus', 'AccountCurrencyCode', 'Ctr', 'ConversionRate', 'Conversions')
							->getAsObj()->result;
						foreach ($obj as $item_c) {
							$donvitien = $item_c->currency;
							$cost = is_numeric($item_c->cost) ? $item_c->cost : 0;
							$cost = round($cost / 1000000, 2);
							$budget = is_numeric($item_c->budget) ? $item_c->budget : 0;
							$budget = round($budget / 1000000, 2);
							if ($donvitien == 'USD') {
								$cost = $cost * config('tygia.cost');
								$budget = $budget . " $";
							}
							if ($donvitien == 'VND') {
								$budget = number_format($budget) . " đ";
							}
							$ctr = $item_c->ctr;
							$cr = $item_c->convRate;
							$install = $item_c->conversions;

							$reportdata->insertReportdata_adwords($date, $gameid, $adsnetworkid, $countrycode, $cost, $budget, $ctr, $cr, $install);
						}
					}
				}
			}

			$timeupdatedata_obj->insertTimeUpdate($date, 'adwords' . $stt, 'ok xong');
		}

		return 1;
	}
}
