<?php

namespace App\Http\Controllers;

use App\Model\adsnetworks;
use App\Model\countries;
use App\Model\game;
use App\Model\reportadsnetwork;
use App\Model\reportcountry;
use App\Model\reportdata;
use App\Model\reportgame;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
	public function getDataMarketing()
	{
		set_time_limit(3600);

		$datetoday = date('Y-m-d');
		$ngay_hom_truoc_kia = date('Y-m-d', strtotime($datetoday . " -3 day"));
		$ngay_hom_truoc = date('Y-m-d', strtotime($datetoday . " -2 day"));
		$ngay_hom_qua = date('Y-m-d', strtotime($datetoday . " -1 day"));

		$game_obj = new game();
		$games = $game_obj->getListGame();

		$adsnetworks_obj = new adsnetworks();
		$adsnetworks = $adsnetworks_obj->getListAdsnetworks();
		$arr_adsnetworks = [];
		foreach ($adsnetworks as $item) {
			$arr_adsnetworks[$item->adsnetworkname] = $item->adsnetworkid;
		}

		/*IronSource*/
		$ironsource_obj = new IronsourceController();
		$ironsource_obj->insertIronsource($ngay_hom_truoc_kia, $games, $arr_adsnetworks);
		$ironsource_obj->insertIronsource($ngay_hom_truoc, $games, $arr_adsnetworks);
		$ironsource_obj->insertIronsource($ngay_hom_qua, $games, $arr_adsnetworks);
		/*END IronSource*/

		return 1;
	}

	public function getCapnhatdulieu(Request $request)
	{
		$input = $request->all();
		if (isset($input['capnhatdulieu'])) {
			$this->getDataMarketing();
		}

		return view('marketing.capnhatdulieu');
	}

	public function getCaidatnuoc(Request $request)
	{
		$mess = '';
		$country_obj = new countries();

		$input = $request->all();
		if (isset($input['json'])) {
			$json_input = $input['json'];
			$json_input = json_decode($json_input, true);

			$country_obj->updateCountryShow($json_input);
			$mess = 'Cài đặt thành công';
		}

		$country = $country_obj->getListCountry();
		$country_show = $country_obj->getListCountryShow();

		$arr_countryshow = [];
		foreach ($country_show as $item) {
			$arr = [
				'code' => $item->code,
				'name' => $item->name . ' (' . $item->code . ')',
			];

			$arr_countryshow[] = $arr;
		}

		$json = json_encode($arr_countryshow);

		return view('marketing.caidatnuoc', compact('country', 'json', 'mess'));
	}

	public function getThemdulieu()
	{
		$game_obj = new game();
		$games = $game_obj->getListGame();
		$adsnetwork_obj = new adsnetworks();
		$adsnetwork = $adsnetwork_obj->getListAdsnetworks();
		$country_obj = new countries();
		$country = $country_obj->getListCountryShow();
		return view('marketing.themdulieu', compact('games', 'adsnetwork', 'country'));
	}

	public function postThemdulieu(Request $request)
	{
		$input = $request->all();

		$date = $input['date'];
		$gameid = $input['game'];
		$adsnetworkid = $input['adsnetwork'];
		$country_obj = new countries();
		$country = $country_obj->getListCountryShow();
		$reportdata_obj = new reportdata();
		$reportgame_obj = new reportgame();
		$reportadsnetwork_obj = new reportadsnetwork();
		$reportcountry_obj = new reportcountry();
		foreach ($country as $item) {
			$countrycode = $item->code;
			if (isset($input['countrycode' . $countrycode])) {
				$revenue = $this->rmcomma($input['revenuevnd' . $countrycode]);
				$cost = $this->rmcomma($input['costvnd' . $countrycode]);
				$budget = $input['budget' . $countrycode];
				$cpitarget = $input['cpitarget' . $countrycode];
				$ctr = $input['ctr' . $countrycode];
				$cr = $input['cr' . $countrycode];
				$install = $input['install' . $countrycode];
				$reportdata_obj->insertReportdata_all_form($date, $gameid, $adsnetworkid, $countrycode, $revenue, $cost, $budget, $cpitarget, $ctr, $cr, $install);
			}

			$sumRevenue_country = $reportdata_obj->sumReportdata('revenue', $date, '', '', $countrycode);
			$sumCost_country = $reportdata_obj->sumReportdata('cost', $date, '', '', $countrycode);
			$suminstall_country = $reportdata_obj->sumReportdata('install', $date, '', '', $countrycode);
			$reportcountry_obj->insertReportcountry_all_form($date, $countrycode, $sumRevenue_country, $sumCost_country, '', '', '', '', $suminstall_country);
		}

		$sumRevenue_game = $reportdata_obj->sumReportdata('revenue', $date, $gameid);
		$sumCost_game = $reportdata_obj->sumReportdata('cost', $date, $gameid);
		$suminstall_game = $reportdata_obj->sumReportdata('install', $date, $gameid);
		$reportgame_obj->insertReportgame_all_form($date, $gameid, $sumRevenue_game, $sumCost_game, '', '', '', '', $suminstall_game);

		$sumRevenue_adsnetwork = $reportdata_obj->sumReportdata('revenue', $date, '', $adsnetworkid);
		$sumCost_adsnetwork = $reportdata_obj->sumReportdata('cost', $date, '', $adsnetworkid);
		$suminstall_adsnetwork = $reportdata_obj->sumReportdata('install', $date, '', $adsnetworkid);
		$reportadsnetwork_obj->insertReportadsnetwork_all_form($date, $adsnetworkid, $sumRevenue_adsnetwork, $sumCost_adsnetwork, '', '', '', '', $suminstall_adsnetwork);

		return redirect()->back()->with('mess', 'Thêm dữ liệu thành công!');

		/*$game_obj = new game();
		$games = $game_obj->getListGame();
		$adsnetwork_obj = new adsnetworks();
		$adsnetwork = $adsnetwork_obj->getListAdsnetworks();
		$country_obj = new countries();
		$country = $country_obj->getListCountryShow();
		return view('marketing.themdulieu', compact('games', 'adsnetwork', 'country'));*/
	}

	public function rmcomma($str)
	{
		return str_replace(',', '', $str);
	}
}
