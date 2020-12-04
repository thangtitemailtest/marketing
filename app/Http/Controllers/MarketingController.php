<?php

namespace App\Http\Controllers;

use App\Model\adsnetworks;
use App\Model\game;
use App\Model\reportdata;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
	public function getDataMarketing()
	{
		set_time_limit(3600);

		$datetoday = date('Y-m-d');
		//$ngay_hom_truoc_kia = date('Y-m-d', strtotime($datetoday . " -3 day"));
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
		//$ironsource_obj->insertIronsource($ngay_hom_truoc_kia, $games, $arr_adsnetworks);
		$ironsource_obj->insertIronsource($ngay_hom_truoc, $games, $arr_adsnetworks);
		$ironsource_obj->insertIronsource($ngay_hom_qua, $games, $arr_adsnetworks);
		/*END IronSource*/

		return 1;
	}

	public function getCapnhatdulieu(Request $request)
	{
		$input = $request->all();
		if (isset($input['capnhatdulieu'])){
			$this->getDataMarketing();
		}

		return view('marketing.capnhatdulieu');
	}

	public function getTest()
	{
		$adsnetworks_obj = new adsnetworks();
		$adsnetworks = $adsnetworks_obj->getListAdsnetworks();
		$arr_adsnetworks = [];
		foreach ($adsnetworks as $item) {
			$arr_adsnetworks[$item->adsnetworkname] = $item->adsnetworkid;
		}

		$adsnetwork = 'test';
		for ($i = 1; $i <= 10; $i++) {
			if (!isset($arr_adsnetworks[$adsnetwork])) {
				$adsnetwork_id = $adsnetworks_obj->insertAdsnetworks($adsnetwork);
				$arr_adsnetworks[$adsnetwork] = $adsnetwork_id;
			}

			$adsnetwork_id = $arr_adsnetworks[$adsnetwork];
		}
	}

}
