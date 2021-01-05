<?php

namespace App\Http\Controllers;

use App\Model\adsnetworks;
use App\Model\countries;
use App\Model\game;
use App\Model\loggetrevenue;
use App\Model\reportadsnetwork;
use App\Model\reportcountry;
use App\Model\reportdata;
use App\Model\reportgame;
use App\Model\settingcountry;
use App\Model\settinggetrevenue;
use App\Model\timeupdatedata;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\Log;
use Auth;

class MarketingController extends Controller
{
	public function getDataMarketing()
	{
		set_time_limit(3600);

		$timeupdatedata_obj = new timeupdatedata();
		$timeupdatedata_obj->insertTimeUpdate();

		$datetoday = date('Y-m-d');
		$ngay_hom_truoc_kia = date('Y-m-d', strtotime($datetoday . " -3 day"));
		$ngay_hom_truoc = date('Y-m-d', strtotime($datetoday . " -2 day"));
		$ngay_hom_qua = date('Y-m-d', strtotime($datetoday . " -1 day"));

		$arr_date = array();
		$arr_date[] = $ngay_hom_truoc_kia;
		$arr_date[] = $ngay_hom_truoc;
		$arr_date[] = $ngay_hom_qua;

		$arr_date2 = array();
		$arr_date2[] = $ngay_hom_qua;

		$game_obj = new game();
		$games = $game_obj->getListGame();

		$adsnetworks_obj = new adsnetworks();
		$adsnetworks = $adsnetworks_obj->getListAdsnetworks();
		$arr_adsnetworks = [];
		foreach ($adsnetworks as $item) {
			$arr_adsnetworks[$item->adsnetworkname] = $item->adsnetworkid;
		}

		$loggetrevenue_obj = new loggetrevenue();
		$loggetrevenue_obj->deleteDb();

		/*IronSource*/
		$ironsource_obj = new IronsourceController();
		$ironsource_obj->insertIronsource($arr_date, $games, $arr_adsnetworks);
		/*END IronSource*/

		/*Adwords*/
		$adwords_obj = new AdswordController();
		$adwords_obj->insertAdwords($arr_date2);
		/*END Adwords*/

		/*Unity*/
		$unity_obj = new UnityadsController();
		$unity_obj->insertUnityads($arr_date2);
		/*END Unity*/

		/*SearchAds*/
		$searchads_obj = new SearchAdsController();
		$searchads_obj->insertSearchads($arr_date2);
		/*END SearchAds*/

        /*AppLovin*/
		$applovin_obj = new ApplovinController();
		$applovin_obj->insertApplovin($arr_date);
		/*EDN AppLovin*/

		return 1;
	}

	public function getDataMarketingDate($date, $adsnetwork)
	{
		set_time_limit(3600);

		$timeupdatedata_obj = new timeupdatedata();
		$timeupdatedata_obj->insertTimeUpdate();

		$arr_date = array();
		$arr_date[] = $date;

		$game_obj = new game();
		$games = $game_obj->getListGame();

		$adsnetworks_obj = new adsnetworks();
		$adsnetworks = $adsnetworks_obj->getListAdsnetworks();
		$arr_adsnetworks = [];
		foreach ($adsnetworks as $item) {
			$arr_adsnetworks[$item->adsnetworkname] = $item->adsnetworkid;
		}

		if ($adsnetwork == 'ironsource') {
			$loggetrevenue_obj = new loggetrevenue();
			$loggetrevenue_obj->deleteDb();
			/*IronSource*/
			$ironsource_obj = new IronsourceController();
			$ironsource_obj->insertIronsource($arr_date, $games, $arr_adsnetworks);
			/*END IronSource*/
		}

		if ($adsnetwork == 'applovin') {
			/*AppLovin*/
			$applovin_obj = new ApplovinController();
			$applovin_obj->insertApplovin($arr_date);
			/*EDN AppLovin*/
		}

		if ($adsnetwork == 'adwords') {
			/*Adwords*/
			$adwords_obj = new AdswordController();
			$adwords_obj->insertAdwords($arr_date);
			/*END Adwords*/
		}

		if ($adsnetwork == 'unity') {
			/*Unity*/
			$unity_obj = new UnityadsController();
			$unity_obj->insertUnityads($arr_date);
			/*END Unity*/
		}

		if ($adsnetwork == 'searchads') {
			/*SearchAds*/
			$searchads_obj = new SearchAdsController();
			$searchads_obj->insertSearchads($arr_date);
			/*END SearchAds*/
		}

		return 1;
	}

	public function getCapnhatdulieu(Request $request)
	{
		$input = $request->all();
		if (isset($input['capnhatdulieu'])) {
			$date = $input['date'];
			$adsnetwork = $input['adsnetwork'];
			$this->getDataMarketingDate($date, $adsnetwork);
		}

		return view('marketing.capnhatdulieu');
	}

	public function getCaidatnuoc()
	{
		$country_obj = new countries();
		$game_obj = new game();
		$games = $game_obj->getListGame();
		$country = $country_obj->getListCountry();
		$permission = json_decode(Auth::user()->permission, true);
		if (empty($permission)) $permission[0] = '';

		return view('marketing.caidatnuoc', compact('country', 'games', 'permission'));
	}

	public function getCountrygame(Request $request)
	{
		$input = $request->all();
		$gameid = $input['gameid'];

		$settingcountry = new settingcountry();
		$country_show = $settingcountry->getListCountryGameDesc($gameid);

		$arr_countryshow = [];
		foreach ($country_show as $item) {
			$arr = [
				'code' => $item->code,
				'name' => $item->name . ' (' . $item->code . ')',
			];

			$arr_countryshow[] = $arr;
		}

		$json = json_encode($arr_countryshow);

		return $json;
	}

	public function postCaidatnuoc(Request $request)
	{
		$input = $request->all();
		$gameid = $request->gameid;
		$json = json_decode($input['json'], true);
		$settingcountry = new settingcountry();
		$settingcountry->deleteSettingCountryGame($gameid);
		foreach ($json as $item) {
			$countrycode = $item['code'];
			$name = $item['name'];
			$countryname = explode("(", $name)[0];
			$countryname = rtrim($countryname);

			$settingcountry->insertSettingCountry($gameid, $countrycode, $countryname);
		}

		return redirect()->back()->with('mess', 'Cài đặt thành công!');
	}

	public function getThemdulieu()
	{
		$game_obj = new game();
		$games = $game_obj->getListGame();
		$adsnetwork_obj = new adsnetworks();
		$adsnetwork = $adsnetwork_obj->getListAdsGroup();
		$permission = json_decode(Auth::user()->permission, true);
		if (empty($permission)) $permission[0] = '';

		return view('marketing.themdulieu', compact('games', 'adsnetwork', 'permission'));
	}

	public function getBangthemdulieu(Request $request)
	{
		$input = $request->all();
		$gameid = $input['gameid'];
		$settingcountry = new settingcountry();
		$country = $settingcountry->getListCountryGame($gameid);
		$arr_country = [];
		foreach ($country as $item) {
			$arr_country[] = $item->code;
		}
		?>
        <input type="hidden" name="arrcountry" id="arrcountry" value='<?= json_encode($arr_country) ?>'>
        <div class="col-md-12 table-responsive" style="height: 500px">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0"
                   id="dataTable">
                <thead>
                <tr>
                    <th rowspan="3" style="padding: 3px">Country</th>
                    <th rowspan="3" style="padding: 3px">Budget</th>
                    <th colspan="2" style="padding: 3px">Cost</th>
                    <th rowspan="3" style="padding: 3px">CPI target</th>
                    <th rowspan="3" style="padding: 3px">CTR</th>
                    <th rowspan="3" style="padding: 3px">CR</th>
                    <th rowspan="3" style="padding: 3px">Install</th>
                    <th colspan="2" style="padding: 3px">Revenue</th>
                </tr>
                <tr>
                    <th class="cltien" style="padding: 3px">USD</th>
                    <th class="cltien" style="padding: 3px">VND</th>
                    <th class="cltien" style="padding: 3px">USD</th>
                    <th class="cltien" style="padding: 3px">VND</th>
                </tr>
                <tr>
                    <th class="cltien2" style="padding: 3px" id="sumcostusd"></th>
                    <th class="cltien2" style="padding: 3px" id="sumcostvnd"></th>
                    <th class="cltien2" style="padding: 3px" id="sumrevenueusd"></th>
                    <th class="cltien2" style="padding: 3px" id="sumrevenuevnd"></th>
                </tr>
                </thead>
                <tbody>
				<?php
				foreach ($country as $item) {
					?>
                    <tr>
                        <input type="hidden" name="countrycode<?= $item->code ?>"
                               id="countrycode<?= $item->code ?>" value="<?= $item->code ?>">
                        <td><?= $item->name ?> (<?= $item->code ?>)</td>
                        <td><input type="text" class="form-control clearinput" name="budget<?= $item->code ?>"
                                   onkeyup="addcomma1(this)"
                                   id="budget<?= $item->code ?>">
                        </td>
                        <td><input type="text" class="form-control clearinput" name="costusd<?= $item->code ?>"
                                   id="costusd<?= $item->code ?>"
                                   onkeyup="changeCostUsd('<?= $item->code ?>')">
                        </td>
                        <td><input type="text" class="form-control clearinput" name="costvnd<?= $item->code ?>"
                                   onkeyup="changeCostVnd(this,'<?= $item->code ?>')"
                                   id="costvnd<?= $item->code ?>">
                        </td>
                        <td><input type="text" class="form-control clearinput"
                                   name="cpitarget<?= $item->code ?>" id="cpitarget<?= $item->code ?>">
                        </td>
                        <td><input type="text" class="form-control clearinput" name="ctr<?= $item->code ?>"
                                   id="ctr<?= $item->code ?>">
                        </td>
                        <td><input type="text" class="form-control clearinput" name="cr<?= $item->code ?>"
                                   id="cr<?= $item->code ?>"></td>
                        <td><input type="text" class="form-control clearinput" name="install<?= $item->code ?>"
                                   id="install<?= $item->code ?>">
                        </td>
                        <td><input type="text" class="form-control clearinput"
                                   name="revenueusd<?= $item->code ?>" id="revenueusd<?= $item->code ?>"
                                   onkeyup="changeRevenueUsd('<?= $item->code ?>')"></td>
                        <td><input type="text" class="form-control clearinput"
                                   onkeyup="changeRevenueVnd(this,'<?= $item->code ?>')"
                                   name="revenuevnd<?= $item->code ?>" id="revenuevnd<?= $item->code ?>">
                        </td>
                    </tr>
					<?php
				}
				?>
                </tbody>
            </table>
        </div>
		<?php
	}

	public function getBangthemdulieuThongsoads(Request $request)
	{
		$input = $request->all();
		$gameid = $input['gameid'];
		$adsnetworkid = $input['adsnetwork'];
		$date = $input['date'];

		$result = [];
		$reportdata_obj = new reportdata();
		$reportdata = $reportdata_obj->getListWhereGameAdsDate($gameid, $adsnetworkid, $date);
		foreach ($reportdata as $item) {
			$countrycode = $item->countrycode;
			$budget = $item->budget;
			$cost = $item->cost;
			$costusd = $cost / config('tygia.cost');
			$cpitarget = $item->cpitarget;
			$ctr = $item->ctr;
			$cr = $item->cr;
			$install = $item->install;
			$revenue = $item->revenue;
			$revenueusd = $revenue / config('tygia.revenue');

			$result[$countrycode]['budget'] = $budget;
			$result[$countrycode]['cost'] = number_format($cost);
			$result[$countrycode]['costusd'] = round($costusd, 2);
			$result[$countrycode]['cpitarget'] = $cpitarget;
			$result[$countrycode]['ctr'] = $ctr;
			$result[$countrycode]['cr'] = $cr;
			$result[$countrycode]['install'] = $install;
			$result[$countrycode]['revenue'] = number_format($revenue);
			$result[$countrycode]['revenueusd'] = round($revenueusd, 2);
		}

		return json_encode($result);
	}

	public function postThemdulieu(Request $request)
	{
		$input = $request->all();

		$date = $input['date'];
		$gameid = $input['game'];
		$adsnetworkid = $input['adsnetwork'];
		$settingcountry = new settingcountry();
		$country = $settingcountry->getListCountryGame($gameid);
		$reportdata_obj = new reportdata();

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

		}

		return redirect()->back()->with('mess', 'Thêm dữ liệu thành công!');
	}

	public function getThongkedulieutheoquocgia(Request $request)
	{
		$adsnetwork_obj = new adsnetworks();
		$adsnetwork = $adsnetwork_obj->getListAdsGroup();
		$count_adsnetwork = count($adsnetwork);
		$input = $request->all();
		if (isset($input['game'])) {
			$gameid = $input['game'];
			$settingcountry = new settingcountry();
			$country = $settingcountry->getListCountryGame($gameid);
		} else {
			$country = [];
		}
		$game_obj = new game();
		$game = $game_obj->getListGame();
		$permission = json_decode(Auth::user()->permission, true);
		if (empty($permission)) $permission[0] = '';

		return view('marketing.thongkedulieutheoquocgia', compact('adsnetwork', 'count_adsnetwork', 'country', 'game', 'permission'));
	}

	public function getThongkegame(Request $request)
	{
		$input = $request->all();
		$date = $this->getDate($input);
		$datefrom = $date['from'];
		$dateto = $date['to'];
		$game_obj = new game();
		$game_arr = $game_obj->getListGameArrayGameid();
		$reportdata_obj = new reportdata();
		$reportdata = $reportdata_obj->getListAllWhereDate($datefrom, $dateto);

		$sum_install = 0;
		$sum_cost = 0;
		$sum_revenue = 0;
		$data = [];
		foreach ($reportdata as $item) {
			$gameid = $item->gameid;
			$install = $item->install;
			$cost = $item->cost;
			$revenue = $item->revenue;

			if (empty($data[$gameid]['install'])) $data[$gameid]['install'] = 0;
			if (empty($data[$gameid]['cost'])) $data[$gameid]['cost'] = 0;
			if (empty($data[$gameid]['revenue'])) $data[$gameid]['revenue'] = 0;

			$data[$gameid]['install'] += $install;
			$data[$gameid]['cost'] += $cost;
			$data[$gameid]['revenue'] += $revenue;

			$sum_install += $install;
			$sum_cost += $cost;
			$sum_revenue += $revenue;
		}

		$sum_performance = $sum_revenue - $sum_cost;
		if (empty($sum_cost)) {
			$sum_profit = 0;
		} else {
			$sum_profit = round($sum_performance / $sum_cost * 100, 2);
		}
		$sum_profit .= " %";

		?>
        <table class="table table-bordered table-hover" width="100%"
               cellspacing="0">
            <thead>
            <tr>
                <th>Game</th>
                <th>Install</th>
                <th>Cost</th>
                <th>Revenue</th>
                <th>Performance</th>
                <th>Profit Rate</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>Sum</th>
                <th><?= number_format($sum_install) ?></th>
                <th><?= number_format($sum_cost) ?></th>
                <th><?= number_format($sum_revenue) ?></th>
                <th><?= number_format($sum_performance) ?></th>
                <th><?= $sum_profit ?></th>
            </tr>
			<?php

			foreach ($game_arr as $gameid => $game_name) {
				$install = empty($data[$gameid]['install']) ? 0 : $data[$gameid]['install'];
				$cost = empty($data[$gameid]['cost']) ? 0 : $data[$gameid]['cost'];
				$revenue = empty($data[$gameid]['revenue']) ? 0 : $data[$gameid]['revenue'];

				$performance = $revenue - $cost;
				if (empty($cost)) {
					$profit = 0;
				} else {
					$profit = round($performance / $cost * 100, 2);
				}
				$profit .= " %";

				?>
                <tr>
                    <td style="text-align: left"><?= $game_name ?></td>
                    <td style="text-align: right"><?= number_format($install) ?></td>
                    <td style="text-align: right"><?= number_format($cost) ?></td>
                    <td style="text-align: right"><?= number_format($revenue) ?></td>
                    <td style="text-align: right"><?= number_format($performance) ?></td>
                    <td style="text-align: right"><?= $profit ?></td>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
		<?php
	}

	public function getOverall(Request $request)
	{
		$input = $request->all();
		$date = $this->getDate($input);
		$gameid = $input['game'];
		$datefrom = $date['from'];
		$dateto = $date['to'];

		$country_obj = new countries();
		$country = $country_obj->getListCountryArrayKeyCode($gameid);

		$reportdata_obj = new reportdata();
		$reportdata_game = $reportdata_obj->getListWhereGameDate($gameid, $datefrom, $dateto);

		$arrreport = [];
		foreach ($country as $code => $item) {
			$arrreport[$code]['name'] = $item;
			$arrreport[$code]['install'] = 0;
			$arrreport[$code]['revenue'] = 0;
			$arrreport[$code]['cost'] = 0;
		}

		foreach ($reportdata_game as $item) {
			$code = $item->countrycode;
			if (isset($country[$code])) {
				if (empty($arrreport[$code]['install'])) $arrreport[$code]['install'] = 0;
				if (empty($arrreport[$code]['revenue'])) $arrreport[$code]['revenue'] = 0;
				if (empty($arrreport[$code]['cost'])) $arrreport[$code]['cost'] = 0;

				$arrreport[$code]['name'] = $country[$code];
				$arrreport[$code]['install'] += $item->install;
				$arrreport[$code]['revenue'] += $item->revenue;
				$arrreport[$code]['cost'] += $item->cost;
			}
		}

		return json_encode($arrreport);
	}

	public function getOverallCountry(Request $request)
	{
		$input = $request->all();
		$date = $this->getDate($input);
		$gameid = $input['game'];
		$datefrom = $date['from'];
		$dateto = $date['to'];
		$countrycode = $input['countrycode'];
		$countryname = $input['countryname'];

		$reportdata_obj = new reportdata();
		$reportdata_gamecountry = $reportdata_obj->getListWhereGameCountryDate($gameid, $countrycode, $datefrom, $dateto);

		$sum_install = 0;
		$sum_revenue = 0;
		$sum_cost = 0;
		$arrreport = [];
		foreach ($reportdata_gamecountry as $item) {
			$date = $item->date;

			if (empty($arrreport[$date]['install'])) $arrreport[$date]['install'] = 0;
			if (empty($arrreport[$date]['revenue'])) $arrreport[$date]['revenue'] = 0;
			if (empty($arrreport[$date]['cost'])) $arrreport[$date]['cost'] = 0;

			$arrreport[$date]['install'] += $item->install;
			$arrreport[$date]['revenue'] += $item->revenue;
			$arrreport[$date]['cost'] += $item->cost;

			$sum_install += $item->install;
			$sum_revenue += $item->revenue;
			$sum_cost += $item->cost;
		}

		$sum_performance = $sum_revenue - $sum_cost;
		if (empty($sum_cost)) {
			$sum_profit = 0;
		} else {
			$sum_profit = round($sum_performance / $sum_cost * 100, 2);
		}
		$sum_profit .= " %";

		$begin = new DateTime($datefrom);
		$end = new DateTime($dateto);

		for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
			$date = $i->format("Y-m-d");
			if (!isset($arrreport[$date])) {
				$arrreport[$date]['install'] = 0;
				$arrreport[$date]['revenue'] = 0;
				$arrreport[$date]['cost'] = 0;
			}
		}

		?>
        <table class="table table-bordered table-hover" width="100%"
               cellspacing="0">
            <thead>
            <tr>
                <th colspan="6"><?= $countryname ?></th>
            </tr>
            <tr>
                <th>Ngày</th>
                <th>Install</th>
                <th>Cost</th>
                <th>Revenue</th>
                <th>Performance</th>
                <th>Profit Rate</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>Sum</th>
                <th><?= number_format($sum_install) ?></th>
                <th><?= number_format($sum_cost) ?></th>
                <th><?= number_format($sum_revenue) ?></th>
                <th><?= number_format($sum_performance) ?></th>
                <th><?= $sum_profit ?></th>
            </tr>
			<?php
			$begin = new DateTime($datefrom);
			$end = new DateTime($dateto);

			for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
				$date = $i->format("Y-m-d");
				$date_show = $i->format("d/m/Y");
				if (!isset($arrreport[$date])) {
					$arrreport[$date]['install'] = 0;
					$arrreport[$date]['revenue'] = 0;
					$arrreport[$date]['cost'] = 0;
				}

				$install = $arrreport[$date]['install'];
				$revenue = $arrreport[$date]['revenue'];
				$cost = $arrreport[$date]['cost'];

				$performance = $revenue - $cost;
				if (empty($cost)) {
					$profit = 0;
				} else {
					$profit = round($performance / $cost * 100, 2);
				}
				$profit .= " %";

				?>
                <tr>
                    <td><?= $date_show ?></td>
                    <td><?= number_format($install) ?></td>
                    <td><?= number_format($cost) ?></td>
                    <td><?= number_format($revenue) ?></td>
                    <td><?= number_format($performance) ?></td>
                    <td><?= $profit ?></td>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
		<?php
	}

	public function getSummary(Request $request)
	{
		$input = $request->all();
		$gameid = $input['game'];

		$reportdata_obj = new reportdata();
		$reportdata_game = $reportdata_obj->getListWhereGameAll($gameid);

		$sum_cost_all = 0;
		$sum_revenue_all = 0;
		$sum_install_all = 0;
		$arr_sumall = [];
		$arr_month = [];
		foreach ($reportdata_game as $item) {
			$sum_install_all += $item->install;
			$sum_revenue_all += $item->revenue;
			$sum_cost_all += $item->cost;

			$date = $item->date;
			$month = date('Y-m', strtotime($date));
			$arr_month[$month] = date('m/Y', strtotime($date));
			if (empty($arr_sumall[$month]['cost'])) $arr_sumall[$month]['cost'] = 0;
			if (empty($arr_sumall[$month]['revenue'])) $arr_sumall[$month]['revenue'] = 0;
			if (empty($arr_sumall[$month]['install'])) $arr_sumall[$month]['install'] = 0;
			$arr_sumall[$month]['cost'] += $item->cost;
			$arr_sumall[$month]['revenue'] += $item->revenue;
			$arr_sumall[$month]['install'] += $item->install;
		}

		$sum_performance_all = $sum_revenue_all - $sum_cost_all;
		if (empty($sum_cost_all)) {
			$sum_profit_all = 0;
		} else {
			$sum_profit_all = round($sum_performance_all / $sum_cost_all * 100, 2);
		}

		?>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-bordered table-hover" width="100%" id="datatable_summary"
                       cellspacing="0">
                    <thead>
                    <tr>
                        <th>Month</th>
                        <th>Cost</th>
                        <th>Revenue</th>
                        <th>Install</th>
                        <th>Performance</th>
                        <th>Profit Rate</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php
					foreach ($arr_month as $month => $month_show) {
						$cost = $arr_sumall[$month]['cost'];
						$revenue = $arr_sumall[$month]['revenue'];
						$install = $arr_sumall[$month]['install'];
						$performance = $revenue - $cost;
						if (empty($cost)) {
							$profit = 0;
						} else {
							$profit = round($performance / $cost * 100, 2);
						}
						?>
                        <tr>
                            <td><?= $month_show ?></td>
                            <td><?= number_format($cost) ?></td>
                            <td><?= number_format($revenue) ?></td>
                            <td><?= number_format($install) ?></td>
                            <td><?= number_format($performance) ?></td>
                            <td><?= $profit ?> %</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="clickXemMonth('<?= $month ?>','<?= $gameid ?>')">Xem
                                </button>
                            </td>
                        </tr>
						<?php
					}
					?>
                    </tbody>
                    <tfoot>
                    <tr class="cltr">
                        <th>SUM</th>
                        <th><?= number_format($sum_cost_all) ?></th>
                        <th><?= number_format($sum_revenue_all) ?></th>
                        <th><?= number_format($sum_install_all) ?></th>
                        <th><?= number_format($sum_performance_all) ?></th>
                        <th><?= $sum_profit_all ?> %</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div id="divsummary_month"></div>
		<?php
	}

	public function getSummarymonth(Request $request)
	{
		$input = $request->all();
		$datetoday = date('Y-m-d');
		$gameid = $input['game'];
		$month = $input['month'];
		$datefrom = date('Y-m-d', strtotime('first day of this month', strtotime($month)));
		$dateto = date('Y-m-d', strtotime('last day of this month', strtotime($month)));

		$ngaydauthang = $datefrom;
		$ngaycuoithang = $dateto;
		$nam = date('Y', strtotime($month));
		$tuandau = date('W', strtotime($ngaydauthang));
		$tuancuoi = date('W', strtotime($ngaycuoithang));
		$tuannamtruoc = date('W', strtotime(($nam - 1) . '-12-31'));
		$tuancuanam = date('W', strtotime($nam . '-12-31'));
		if ($tuancuoi < $tuandau) {
			$tuandau = $tuandau - $tuannamtruoc;
		}


		$dto = new DateTime();
		$dto->setISODate($nam, $tuandau);
		$datefrom = $dto->format('Y-m-d');

		$dto = new DateTime();
		$dto->setISODate($nam, $tuancuoi);
		$dto->modify('+6 days');
		$dateto = $dto->format('Y-m-d');

		$reportdata_obj = new reportdata();
		$reportdata_game = $reportdata_obj->getListWhereGameDate($gameid, $datefrom, $dateto);
		$arr_sumall = [];
		foreach ($reportdata_game as $item) {
			$date = $item->date;
			if (empty($arr_sumall[$date]['cost'])) $arr_sumall[$date]['cost'] = 0;
			if (empty($arr_sumall[$date]['revenue'])) $arr_sumall[$date]['revenue'] = 0;
			if (empty($arr_sumall[$date]['install'])) $arr_sumall[$date]['install'] = 0;
			$arr_sumall[$date]['cost'] += $item->cost;
			$arr_sumall[$date]['revenue'] += $item->revenue;
			$arr_sumall[$date]['install'] += $item->install;
		}

		$arr_sum_tuan = [];
		for ($tuan = $tuandau; $tuan <= $tuancuoi; $tuan++) {

			$dto = new DateTime();
			$dto->setISODate($nam, $tuan);
			$datedau_form = $dto->format('Y-m-d');
			$dto->modify('+6 days');
			$datedau_to = $dto->format('Y-m-d');

			$begin = new DateTime($datedau_form);
			$end = new DateTime($datedau_to);

			$arr_sum_tuan[$tuan]['cost'] = 0;
			$arr_sum_tuan[$tuan]['revenue'] = 0;
			$arr_sum_tuan[$tuan]['install'] = 0;

			for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
				$date = $i->format("Y-m-d");
				if (isset($arr_sumall[$date])) {
					$arr_sum_tuan[$tuan]['cost'] += $arr_sumall[$date]['cost'];
					$arr_sum_tuan[$tuan]['revenue'] += $arr_sumall[$date]['revenue'];
					$arr_sum_tuan[$tuan]['install'] += $arr_sumall[$date]['install'];
				} else {
					$arr_sumall[$date]['cost'] = 0;
					$arr_sumall[$date]['revenue'] = 0;
					$arr_sumall[$date]['install'] = 0;
				}
			}
		}

		$arr_thu[1] = 'Mon';
		$arr_thu[2] = 'Tue';
		$arr_thu[3] = 'Wed';
		$arr_thu[4] = 'Thu';
		$arr_thu[5] = 'Fri';
		$arr_thu[6] = 'Sat';
		$arr_thu[7] = 'Sun';

		$demtuan = 0;
		for ($tuan = $tuandau; $tuan <= $tuancuoi; $tuan++) {
			$demtuan++;
			$dto = new DateTime();
			$dto->setISODate($nam, $tuan);
			$datedau_form = $dto->format('Y-m-d');
			$dto->modify('+6 days');
			$datedau_to = $dto->format('Y-m-d');

			$cost_tuan = $arr_sum_tuan[$tuan]['cost'];
			$revenue_tuan = $arr_sum_tuan[$tuan]['revenue'];
			$install_tuan = $arr_sum_tuan[$tuan]['install'];
			$performance_tuan = $revenue_tuan - $cost_tuan;
			if (empty($cost_tuan)) {
				$profit_tuan = 0;
			} else {
				$profit_tuan = round($performance_tuan / $cost_tuan * 100, 2);
			}
			$profit_tuan .= " %";

			?>
            <hr>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-hover" width="100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th rowspan="2"><?= $demtuan ?></th>
                            <th rowspan="2">SUM</th>
                            <th colspan="7">Date</th>
                        </tr>
                        <tr>
							<?php
							$dem = 0;
							$begin = new DateTime($datedau_form);
							$end = new DateTime($datedau_to);
							for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
								$dem++;
								$date = $i->format("Y-m-d");
								$date_dm = $arr_thu[$dem] . ', ' . date('d/m', strtotime($date));
								?>
                                <th><?= $date_dm ?></th>
								<?php
							}
							?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th>Cost</th>
                            <th><?= number_format($cost_tuan) ?></th>
							<?php
							$begin = new DateTime($datedau_form);
							$end = new DateTime($datedau_to);
							for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
								$date = $i->format("Y-m-d");
								$cltd = '';
								if ($date < $ngaydauthang || $date > $ngaycuoithang) {
									$cost = number_format($arr_sumall[$date]['cost']);
									$cltd = 'cltr';
								} elseif ($date > $datetoday) {
									$cost = '';
								} else {
									$cost = number_format($arr_sumall[$date]['cost']);
								}
								?>
                                <td class="<?= $cltd ?>"><?= $cost ?></td>
								<?php
							}
							?>
                        </tr>
                        <tr>
                            <th>Revenue</th>
                            <th><?= number_format($revenue_tuan) ?></th>
							<?php
							$begin = new DateTime($datedau_form);
							$end = new DateTime($datedau_to);
							for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
								$date = $i->format("Y-m-d");
								$cltd = '';
								if ($date < $ngaydauthang || $date > $ngaycuoithang) {
									$revenue = number_format($arr_sumall[$date]['revenue']);
									$cltd = 'cltr';
								} elseif ($date > $datetoday) {
									$revenue = '';
								} else {
									$revenue = number_format($arr_sumall[$date]['revenue']);
								}
								?>
                                <td class="<?= $cltd ?>"><?= $revenue ?></td>
								<?php
							}
							?>
                        </tr>
                        <tr>
                            <th>Install</th>
                            <th><?= number_format($install_tuan) ?></th>
							<?php
							$begin = new DateTime($datedau_form);
							$end = new DateTime($datedau_to);
							for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
								$date = $i->format("Y-m-d");
								$cltd = '';
								if ($date < $ngaydauthang || $date > $ngaycuoithang) {
									$install = number_format($arr_sumall[$date]['install']);
									$cltd = 'cltr';
								} elseif ($date > $datetoday) {
									$install = '';
								} else {
									$install = number_format($arr_sumall[$date]['install']);
								}
								?>
                                <td class="<?= $cltd ?>"><?= $install ?></td>
								<?php
							}
							?>
                        </tr>
                        <tr>
                            <th>Performance</th>
                            <th><?= number_format($performance_tuan) ?></th>
							<?php
							$begin = new DateTime($datedau_form);
							$end = new DateTime($datedau_to);
							for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
								$date = $i->format("Y-m-d");
								$cltd = '';
								if ($date < $ngaydauthang || $date > $ngaycuoithang) {
									$cost = $arr_sumall[$date]['cost'];
									$revenue = $arr_sumall[$date]['revenue'];
									$performance = number_format($revenue - $cost);
									$cltd = 'cltr';
								} elseif ($date > $datetoday) {
									$performance = '';
								} else {
									$cost = $arr_sumall[$date]['cost'];
									$revenue = $arr_sumall[$date]['revenue'];
									$performance = number_format($revenue - $cost);
								}
								?>
                                <td class="<?= $cltd ?>"><?= $performance ?></td>
								<?php
							}
							?>
                        </tr>
                        <tr>
                            <th>Profit Rate</th>
                            <th><?= $profit_tuan ?></th>
							<?php
							$begin = new DateTime($datedau_form);
							$end = new DateTime($datedau_to);
							for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
								$date = $i->format("Y-m-d");
								$cltd = '';
								if ($date < $ngaydauthang || $date > $ngaycuoithang) {
									$cost = $arr_sumall[$date]['cost'];
									$revenue = $arr_sumall[$date]['revenue'];
									$performance = $revenue - $cost;
									if (empty($cost)) {
										$profit = 0;
									} else {
										$profit = round($performance / $cost * 100, 2);
									}
									$profit .= ' %';
									$cltd = 'cltr';
								} elseif ($date > $datetoday) {
									$profit = '';
								} else {
									$cost = $arr_sumall[$date]['cost'];
									$revenue = $arr_sumall[$date]['revenue'];
									$performance = $revenue - $cost;
									if (empty($cost)) {
										$profit = 0;
									} else {
										$profit = round($performance / $cost * 100, 2);
									}
									$profit .= ' %';
								}
								?>
                                <td class="<?= $cltd ?>"><?= $profit ?></td>
								<?php
							}
							?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
			<?php
		}
	}

	public function getCountry(Request $request)
	{
		$input = $request->all();
		$date = $this->getDate($input);
		$gameid = $input['game'];
		$datefrom = $date['from'];
		$dateto = $date['to'];
		$countrycode = $input['countrycode'];
		$adsnetworkid = $input['adsnetworkid'];
		$datetoday = date('Y-m-d');

		$sum_install_all = 0;
		$sum_revenue_all = 0;
		$sum_cost_all = 0;

		$reportdata_obj = new reportdata();
		$reportdata_game = $reportdata_obj->getListWhereGameAdsCountryDate($gameid, $adsnetworkid, $countrycode, $datefrom, $dateto);
		$reportdata_gamecountry = $reportdata_obj->getListWhereGameCountryDate($gameid, $countrycode, $datefrom, $dateto);
		$arrreport = [];
		foreach ($reportdata_gamecountry as $item) {
			$date = $item->date;

			if (empty($arrreport[$date]['install'])) $arrreport[$date]['install'] = 0;
			if (empty($arrreport[$date]['revenue'])) $arrreport[$date]['revenue'] = 0;
			if (empty($arrreport[$date]['cost'])) $arrreport[$date]['cost'] = 0;

			$arrreport[$date]['install'] += $item->install;
			$arrreport[$date]['revenue'] += $item->revenue;
			$arrreport[$date]['cost'] += $item->cost;

			$sum_install_all += $item->install;
			$sum_revenue_all += $item->revenue;
			$sum_cost_all += $item->cost;
		}

		$sum_performance_all = $sum_revenue_all - $sum_cost_all;
		if (empty($sum_cost_all)) {
			$sum_profit_all = 0;
		} else {
			$sum_profit_all = round($sum_performance_all / $sum_cost_all * 100, 2);
		}
		$sum_profit_all .= ' %';

		$sum_cost = 0;
		//$sum_revenue = 0;
		$sum_install = 0;
		$count_adword = 0;
		$sum_ctr = 0;
		$sum_cr = 0;
		$arr_all = [];
		foreach ($reportdata_game as $item) {
			$date = $item->date;
			$sum_install += $item->install;
			//$sum_revenue += $item->revenue;
			$sum_cost += $item->cost;
			$sum_ctr += $item->ctr;
			$sum_cr += $item->cr;

			$arr_all[$date]['ctr'] = $item->ctr;
			$arr_all[$date]['cr'] = $item->cr;

			$arr_all[$date]['budget'] = $item->budget;
			$arr_all[$date]['cpi_target'] = $item->cpitarget;
			$arr_all[$date]['cost'] = $item->cost;
			//$arr_all[$date]['revenue'] = $item->revenue;
			$arr_all[$date]['install'] = $item->install;
		}

		$begin = new DateTime($datefrom);
		$end = new DateTime($dateto);

		/*$adsnetwork_obj = new adsnetworks();
		$adsnetwork_list = $adsnetwork_obj->getListAdsGroup();
		$count_adsnetwork = count($adsnetwork_list);*/
		for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
			$date = $i->format("Y-m-d");
			if (empty($arr_all[$date]['cost'])) $arr_all[$date]['cost'] = 0;
			//if (empty($arr_all[$date]['revenue'])) $arr_all[$date]['revenue'] = 0;
			if (empty($arr_all[$date]['install'])) $arr_all[$date]['install'] = 0;

			if (empty($arr_all[$date]['budget'])) $arr_all[$date]['budget'] = 0;
			if (empty($arr_all[$date]['cpi_target'])) $arr_all[$date]['cpi_target'] = 0;

			if (empty($arr_all[$date]['ctr'])) $arr_all[$date]['ctr'] = 0;
			if (empty($arr_all[$date]['cr'])) $arr_all[$date]['cr'] = 0;

			if ($date <= $datetoday) {
				$count_adword++;
			}
		}

		if ($count_adword == 0) {
			$ctr = 0;
			$cr = 0;
		} else {
			$ctr = round($sum_ctr / $count_adword, 2);
			$cr = round($sum_cr / $count_adword, 2);
		}
		/*$sum_performance = $sum_revenue - $sum_cost;
		if (empty($sum_cost)) {
			$sum_profit = 0;
		} else {
			$sum_profit = round($sum_performance / $sum_cost * 100, 2);
		}
		$sum_profit .= " %";*/
		if (empty($sum_install)) {
			$sum_cpiavg = 0;
		} else {
			$sum_cpiavg = $sum_cost / $sum_install;
		}

		$country_obj = new countries();
		$country = $country_obj->getCountry($countrycode);
		if ($country) {
			$country_name = $country->name;
		} else {
			$country_name = '';
		}

		?>
        <div class="row">
            <div class="col-md-12 table-responsive contable">
                <table class="table table-bordered table-hover" width="100%"
                       cellspacing="0">
                    <thead>
                    <tr>
                        <th colspan="15"><?= $country_name ?></th>
                    </tr>
                    <tr>
                        <th style="width: 100px;">Date</th>
                        <th>Budget</th>
                        <th>Cost</th>
                        <th>CPI target</th>
                        <th>CPI (Avg)</th>
                        <th>CTR</th>
                        <th>CR</th>
                        <th>Install</th>
                        <!--<th>Revenue</th>
                        <th>Performance</th>
                        <th>Profit rate</th>-->
                        <th>Sum Install</th>
                        <th>Sum Cost</th>
                        <th>Sum Revenue</th>
                        <th>Sum Performance</th>
                        <th>Sum Profit Rate</th>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <th></th>
                        <th><?= number_format($sum_cost) ?></th>
                        <th></th>
                        <th><?= number_format($sum_cpiavg) ?></th>
                        <th><?= $ctr ?></th>
                        <th><?= $cr ?></th>
                        <th><?= number_format($sum_install) ?></th>
                        <!--<th><?/*= number_format($sum_revenue) */ ?></th>
                        <th><?/*= number_format($sum_performance) */ ?></th>
                        <th><?/*= $sum_profit */ ?> </th>-->
                        <th><?= number_format($sum_install_all) ?></th>
                        <th><?= number_format($sum_cost_all) ?></th>
                        <th><?= number_format($sum_revenue_all) ?></th>
                        <th><?= number_format($sum_performance_all) ?></th>
                        <th><?= $sum_profit_all ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php
					$begin = new DateTime($datefrom);
					$end = new DateTime($dateto);

					for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
						$date = $i->format("Y-m-d");
						$date_show = date('d/m', strtotime($date));
						$date_show_thu = date('l', strtotime($date));
						if ($date_show_thu == 'Sunday') {
							$show_thu = "Sun, " . $date_show;
							$cltr = "cltr";
						} else {
							$show_thu = $date_show;
							$cltr = "";
						}

						//$revenue = $this->checkEmptyNum($arr_all[$date]['revenue']);
						$cost = $this->checkEmptyNum($arr_all[$date]['cost']);
						$install = $this->checkEmptyNum($arr_all[$date]['install']);
						//$performance = $revenue - $cost;
						/*if (empty($cost)) {
							$profit = 0;
						} else {
							$profit = round($performance / $cost * 100, 2);
						}
						$profit .= ' %';*/
						if (empty($install)) {
							$cpiavg = 0;
						} else {
							$cpiavg = $cost / $install;
						}

						$sumcost = empty($arrreport[$date]['cost']) ? 0 : $arrreport[$date]['cost'];
						$sumrevenue = empty($arrreport[$date]['revenue']) ? 0 : $arrreport[$date]['revenue'];
						$suminstall = empty($arrreport[$date]['install']) ? 0 : $arrreport[$date]['install'];
						$sumperformance = $sumrevenue - $sumcost;
						if (empty($sumcost)) {
							$sumprofit = 0;
						} else {
							$sumprofit = round($sumperformance / $sumcost * 100, 2);
						}
						$sumprofit .= ' %';
						?>
                        <tr class="<?= $cltr ?>">
                            <td style="text-align: right"><?= $show_thu ?></td>
                            <td><?= $this->checkValueDate($this->checkEmptyNum($arr_all[$date]['budget']), $date, $datetoday) ?></td>
                            <td><?= $this->checkValueDate(number_format($cost), $date, $datetoday) ?></td>
                            <td><?= $this->checkValueDate($this->checkEmptyNum($arr_all[$date]['cpi_target']), $date, $datetoday) ?></td>
                            <td><?= $this->checkValueDate(number_format($cpiavg), $date, $datetoday) ?></td>
                            <td><?= $this->checkValueDate($arr_all[$date]['ctr'], $date, $datetoday) ?></td>
                            <td><?= $this->checkValueDate($arr_all[$date]['cr'], $date, $datetoday) ?></td>
                            <td><?= $this->checkValueDate(number_format($install), $date, $datetoday) ?></td>
                            <!--<td><?/*= $this->checkValueDate(number_format($revenue), $date, $datetoday) */ ?></td>
                            <td><?/*= $this->checkValueDate(number_format($performance), $date, $datetoday) */ ?></td>
                            <td><?/*= $this->checkValueDate($profit, $date, $datetoday) */ ?></td>-->
                            <td><?= number_format($suminstall) ?></td>
                            <td><?= number_format($sumcost) ?></td>
                            <td><?= number_format($sumrevenue) ?></td>
                            <td><?= number_format($sumperformance) ?></td>
                            <td><?= $sumprofit ?></td>
                        </tr>
						<?php
					}
					?>
                    </tbody>
                </table>
            </div>
        </div>
		<?php

	}

	public function getSettinggetrevenue()
	{
		$game_obj = new game();
		$games = $game_obj->getListGame();

		return view('marketing.settinggetrevenue', compact('games'));
	}

	public function getBangsettinggetrevenue(Request $request)
	{
		$input = $request->all();
		$gameid = $input['gameid'];
		$kenh = $input['kenh'];

		$settinggetrevenue_obj = new settinggetrevenue();
		$arr_setting = $settinggetrevenue_obj->getSettingWhereGameKenhToArr($gameid, $kenh);

		if ($kenh == 'ironsource') {
			$adsnetwork_obj = new adsnetworks();
			$list_adsnetwork = $adsnetwork_obj->getListAdsGroup();

			foreach ($list_adsnetwork as $item) {
				$adsnetworkid = $item->adsnetworkid;
				?>
                <div class="col-md-2">
                    <label>
						<?= $item->adsnetworkshow ?>
                        <br><input type="checkbox"
                                   name="checkbox[]" <?= isset($arr_setting[$adsnetworkid]) ? "checked" : "" ?>
                                   value="<?= $adsnetworkid ?>">
                    </label>
                </div>
				<?php
			}
		}

		if ($kenh == 'applovin') {
			$adsnetwork_obj = new adsnetworks();
			$list_adsnetwork = $adsnetwork_obj->getListAdsGroup();

			foreach ($list_adsnetwork as $item) {
				$adsnetworkid = $item->adsnetworkid;
				?>
                <div class="col-md-2">
                    <label>
						<?= $item->adsnetworkshow ?>
                        <br><input type="checkbox"
                                   name="checkbox[]" <?= isset($arr_setting[$adsnetworkid]) ? "checked" : "" ?>
                                   value="<?= $adsnetworkid ?>">
                    </label>
                </div>
				<?php
			}
		}
	}

	public function postSettinggetrevenue(Request $request)
	{
		$input = $request->all();
		$gameid = $input['gameid'];
		$kenh = $input['kenh'];
		$checkbox = isset($input['checkbox']) ? $input['checkbox'] : [];

		$settinggetrevenue_obj = new settinggetrevenue();
		$settinggetrevenue_obj->deleteSetting($gameid, $kenh);

		foreach ($checkbox as $adsnetworkid) {
			$settinggetrevenue_obj->insertSetting($gameid, $kenh, $adsnetworkid);
		}

		return redirect()->back()->with('mess', 'Cài đặt thành công!');
	}

	public function getSendNotiDung()
	{
		$datetoday = date('Y-m-d');
		$date = date('Y-m-d', strtotime($datetoday . " -1 day"));

		$game_obj = new game();
		$arr_game = $game_obj->getListGameArrayGameid();

		$reportdata_obj = new reportdata();
		$reportdata = $reportdata_obj->getListAllWhereOneDate($date);
		$data = [];
		foreach ($reportdata as $item) {
			$gameid = $item->gameid;
			$cost = $item->cost;
			$revenue = $item->revenue;
			$install = $item->install;

			if (empty($data[$gameid]['cost'])) $data[$gameid]['cost'] = 0;
			if (empty($data[$gameid]['revenue'])) $data[$gameid]['revenue'] = 0;
			if (empty($data[$gameid]['install'])) $data[$gameid]['install'] = 0;

			$data[$gameid]['cost'] += $cost;
			$data[$gameid]['revenue'] += $revenue;
			$data[$gameid]['install'] += $install;
		}

		$body = '';
		foreach ($data as $gameid => $item) {
			$gamename = $arr_game[$gameid];
			if ($gameid != 1009 && $gameid != 1010) {
				$cost = $item['cost'];
				$revenue = $item['revenue'];
				$install = $item['install'];
				$performance = $revenue - $cost;
				$body .= "👉" . $gamename . ": " . number_format($install) . " / " . number_format($performance) . "   ";
			}
		}

		$info = [
			"priority" => "high",
			"notification" => [
				"title" => "Thông báo Marketting",
				"body" => $body,
				//"sound" => "default"
			],
			"to" => "eRnUt0Cu3EZ6pwXSxpdYbB:APA91bGT8alUNM7CAY9f1ZpyyFOWY_Q3tpyfd9IWJNptV7uW4iSE53L9qs-7mlOkveopoyIHmZMJabcLdYELPQJkECtXaW4p9U_DA4c-ErNxi4iU69a1p7SkYD0ETIa9WPKrOE_4aBU8"
			//"to" => "eT3sljdx3kRnvjRezMoLLx:APA91bHApygiZ0MbuihFhg2taxAALDKEMOqFQhIt1_TqgbBLWuJ_jxTpEazc9MuLEbBoErsr-pnuYXLOnAMYU_V9LRWq4YvjKaqr24OAUyE3YYGBzgeqDyLf0eEMyvqf7AsWA2Bt8mx3"
		];
		$platform = 'IOS';
		if ($platform == 'IOS' || $platform == 'ios') {
			$serverkey = "AAAAGE0t6F0:APA91bFeOGyrCyCh172XGerOq-cdiVcoSd9wyq6eSBgFfwhqBTT_JQq2J5L8pmIIGdK49gnPFmpVPcBcFD5lPfpsstTRDMZ1jIEi7UHmWFkj8c3pzGVNJ9_FqhRKyCwLy9nQ853HauPc";
		}
		if ($platform == 'ANDROID' || $platform == 'android') {
			$serverkey = "AAAAb_fccfs:APA91bFc08bhNsDslJE2NtBycHSDDNu385T98o6Zsoravxvtc2uguuQLYPDH2cMR2dlSKJ35CydtxMAUFQSo3TV4eqvvC6OJr0mu1eHGA7Rti-YXx9mjVtFev4Sc__Yxk1FlZOUR1C1Q";
		}
		$sendinfo = json_encode($info);
		$ch = curl_init("https://fcm.googleapis.com/fcm/send");
		$header = array('Content-Type: application/json',
			"Authorization: key=" . $serverkey . "");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sendinfo);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	public function rmcomma($str)
	{
		return str_replace(',', '', $str);
	}

	public function checkEmptyNum($num)
	{
		if (empty($num)) {
			return 0;
		} else {
			return $num;
		}
	}

	public function checkValueDate($value, $date, $datetoday)
	{
		if ($date <= $datetoday) {
			return $value;
		} else {
			return '';
		}
	}

	public function getDate($input)
	{
		switch ($input['time']) {
			case "week":
				if (isset($input['week']) && !empty($input['week'])) {
					$week = explode('-W', $input['week']);
					$nam = $week[0];
					$tuan = $week[1];
					$dto = new DateTime();
					$dto->setISODate($nam, $tuan);
					$date['from'] = $dto->format('Y-m-d');
					$dto->modify('+6 days');
					$date['to'] = $dto->format('Y-m-d');
				}

				break;
			case "month":
				if (isset($input['month']) && !empty($input['month'])) {
					$date['from'] = date('Y-m-d', strtotime('first day of this month', strtotime($input['month'])));
					$date['to'] = date('Y-m-d', strtotime('last day of this month', strtotime($input['month'])));
				}

				break;
			case "ngay":
				if (isset($input['ngay']) && !empty($input['ngay'])) {
					$date['from'] = $input['ngay'];
					$date['to'] = $input['ngay'];
				}

				break;
			case "tuychon":
				if (isset($input['fromdate']) && !empty($input['fromdate']) && $input['todate'] && !empty($input['todate'])) {
					$date['from'] = $input['fromdate'];
					$date['to'] = $input['todate'];
				}

				break;
			default :
				break;
		}

		return $date;
	}

}
