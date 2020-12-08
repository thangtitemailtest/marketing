<?php

namespace App\Http\Controllers;

use App\Model\adsnetworks;
use App\Model\countries;
use App\Model\game;
use App\Model\reportadsnetwork;
use App\Model\reportcountry;
use App\Model\reportdata;
use App\Model\reportgame;
use App\Model\settingcountry;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\Log;

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

	public function getCaidatnuoc()
	{
		$country_obj = new countries();
		$game_obj = new game();
		$games = $game_obj->getListGame();
		$country = $country_obj->getListCountry();

		return view('marketing.caidatnuoc', compact('country', 'games'));
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

		return view('marketing.themdulieu', compact('games', 'adsnetwork'));
	}

	public function getBangthemdulieu(Request $request)
	{
		$input = $request->all();
		$gameid = $input['gameid'];
		$settingcountry = new settingcountry();
		$country = $settingcountry->getListCountryGame($gameid);
		?>
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0"
                   id="dataTable">
                <thead>
                <tr>
                    <th rowspan="2">Country</th>
                    <th rowspan="2">Budget</th>
                    <th colspan="2">Cost</th>
                    <th rowspan="2">CPI target</th>
                    <th rowspan="2">CTR</th>
                    <th rowspan="2">CR</th>
                    <th rowspan="2">Install</th>
                    <th colspan="2">Revenue</th>
                </tr>
                <tr>
                    <th>USD</th>
                    <th>VND</th>
                    <th>USD</th>
                    <th>VND</th>
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
                        <td><input type="number" class="form-control" name="budget<?= $item->code ?>"
                                   id="budget<?= $item->code ?>">
                        </td>
                        <td><input type="number" class="form-control" name="costusd<?= $item->code ?>"
                                   id="costusd<?= $item->code ?>"
                                   onkeyup="changeCostUsd('<?= $item->code ?>')">
                        </td>
                        <td><input type="text" class="form-control" name="costvnd<?= $item->code ?>"
                                   onkeyup="changeCostVnd(this,'<?= $item->code ?>')"
                                   id="costvnd<?= $item->code ?>">
                        </td>
                        <td><input type="number" class="form-control"
                                   name="cpitarget<?= $item->code ?>" id="cpitarget<?= $item->code ?>">
                        </td>
                        <td><input type="number" class="form-control" name="ctr<?= $item->code ?>"
                                   id="ctr<?= $item->code ?>">
                        </td>
                        <td><input type="number" class="form-control" name="cr<?= $item->code ?>"
                                   id="cr<?= $item->code ?>"></td>
                        <td><input type="number" class="form-control" name="install<?= $item->code ?>"
                                   id="install<?= $item->code ?>">
                        </td>
                        <td><input type="number" class="form-control"
                                   name="revenueusd<?= $item->code ?>" id="revenueusd<?= $item->code ?>"
                                   onkeyup="changeRevenueUsd('<?= $item->code ?>')"></td>
                        <td><input type="text" class="form-control"
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

	public function postThemdulieu(Request $request)
	{
		$input = $request->all();

		$date = $input['date'];
		$gameid = $input['game'];
		$adsnetworkid = $input['adsnetwork'];
		$settingcountry = new settingcountry();
		$country = $settingcountry->getListCountryGame($gameid);
		$reportdata_obj = new reportdata();
		//$reportgame_obj = new reportgame();
		//$reportadsnetwork_obj = new reportadsnetwork();
		//$reportcountry_obj = new reportcountry();
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

			//$sumRevenue_country = $reportdata_obj->sumReportdata('revenue', $date, '', '', $countrycode);
			//$sumCost_country = $reportdata_obj->sumReportdata('cost', $date, '', '', $countrycode);
			//$suminstall_country = $reportdata_obj->sumReportdata('install', $date, '', '', $countrycode);
			//$reportcountry_obj->insertReportcountry_all_form($date, $countrycode, $sumRevenue_country, $sumCost_country, '', '', '', '', $suminstall_country);
		}

		//$sumRevenue_game = $reportdata_obj->sumReportdata('revenue', $date, $gameid);
		//$sumCost_game = $reportdata_obj->sumReportdata('cost', $date, $gameid);
		//$suminstall_game = $reportdata_obj->sumReportdata('install', $date, $gameid);
		//$reportgame_obj->insertReportgame_all_form($date, $gameid, $sumRevenue_game, $sumCost_game, '', '', '', '', $suminstall_game);

		//$sumRevenue_adsnetwork = $reportdata_obj->sumReportdata('revenue', $date, '', $adsnetworkid);
		//$sumCost_adsnetwork = $reportdata_obj->sumReportdata('cost', $date, '', $adsnetworkid);
		//$suminstall_adsnetwork = $reportdata_obj->sumReportdata('install', $date, '', $adsnetworkid);
		//$reportadsnetwork_obj->insertReportadsnetwork_all_form($date, $adsnetworkid, $sumRevenue_adsnetwork, $sumCost_adsnetwork, '', '', '', '', $suminstall_adsnetwork);

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

		return view('marketing.thongkedulieutheoquocgia', compact('adsnetwork', 'count_adsnetwork', 'country', 'game'));
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
                    <tr>
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

		$ngaydauthang = $datefrom;
		$ngaycuoithang = $dateto;
		$nam = date('Y', strtotime($month));
		$tuandau = date('W', strtotime($ngaydauthang));
		$tuancuoi = date('W', strtotime($ngaycuoithang));

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

		?>
		<?php
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
								if ($date < $ngaydauthang || $date > $ngaycuoithang || $date > $datetoday) {
									$cost = '';
								} else {
									$cost = number_format($arr_sumall[$date]['cost']);
								}
								?>
                                <td><?= $cost ?></td>
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
								if ($date < $ngaydauthang || $date > $ngaycuoithang || $date > $datetoday) {
									$revenue = '';
								} else {
									$revenue = number_format($arr_sumall[$date]['revenue']);
								}
								?>
                                <td><?= $revenue ?></td>
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
								if ($date < $ngaydauthang || $date > $ngaycuoithang || $date > $datetoday) {
									$install = '';
								} else {
									$install = number_format($arr_sumall[$date]['install']);
								}
								?>
                                <td><?= $install ?></td>
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
								if ($date < $ngaydauthang || $date > $ngaycuoithang || $date > $datetoday) {
									$performance = '';
								} else {
									$cost = $arr_sumall[$date]['cost'];
									$revenue = $arr_sumall[$date]['revenue'];
									$performance = number_format($revenue - $cost);
								}
								?>
                                <td><?= $performance ?></td>
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
								if ($date < $ngaydauthang || $date > $ngaycuoithang || $date > $datetoday) {
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
                                <td><?= $profit ?></td>
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

		$reportdata_obj = new reportdata();
		$reportdata_game = $reportdata_obj->getListWhereGameAdsCountryDate($gameid, $adsnetworkid, $countrycode, $datefrom, $dateto);

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
		}*/
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
                        <th colspan="8"><?= $country_name ?></th>
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
                        <!-- <th>Revenue</th>
						 <th>Performance</th>
						 <th>Profit rate</th>-->
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
                        <th><?/*= $sum_profit */ ?> %</th>-->
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
						/*$performance = $revenue - $cost;
						if (empty($cost)) {
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
