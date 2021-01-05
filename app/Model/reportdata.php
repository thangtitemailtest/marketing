<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class reportdata extends Model
{
	protected $table = "reportdata";
	public $timestamps = false;

	public function checkReportdata($date = '', $gameid = '', $adsnetworkid = '', $countrycode = '')
	{
		$reportdata_obj = $this::where('date', '=', $date)
			->where('gameid', '=', $gameid)
			->where('adsnetworkid', '=', $adsnetworkid)
			->where('countrycode', '=', $countrycode)
			->first();

		return $reportdata_obj;
	}

	public function insertReportdata_revenue($date = '', $gameid = '', $adsnetworkid = '', $countrycode = '', $revenue = 0)
	{
		$checkReportdata = $this->checkReportdata($date, $gameid, $adsnetworkid, $countrycode);
		if ($checkReportdata) {
			$checkReportdata->revenue = round($revenue * config('tygia.revenue'));
			$checkReportdata->updatedate = date('Y-m-d H:i:s');
			$checkReportdata->save();
		} else {
			$reportdata_obj = new reportdata();
			$reportdata_obj->date = $date;
			$reportdata_obj->gameid = $gameid;
			$reportdata_obj->adsnetworkid = $adsnetworkid;
			$reportdata_obj->countrycode = $countrycode;
			$reportdata_obj->revenue = round($revenue * config('tygia.revenue'));
			$reportdata_obj->save();
		}

		return 1;
	}

	public function insertReportdata_revenue_applovin($date = '', $gameid = '', $adsnetworkid = '', $countrycode = '', $revenue = 0)
	{
		$loggetrevenue = new loggetrevenue();
		$checkLoggetrevenue = $loggetrevenue->getLogGetRevenue($date, $gameid, $adsnetworkid, $countrycode);
		$checkReportdata = $this->checkReportdata($date, $gameid, $adsnetworkid, $countrycode);
		if ($checkReportdata) {
			if ($checkLoggetrevenue) {
				$checkReportdata->revenue = round($revenue * config('tygia.revenue')) + $checkLoggetrevenue->revenue;
			} else {
				$checkReportdata->revenue = round($revenue * config('tygia.revenue'));
			}

			$checkReportdata->updatedate = date('Y-m-d H:i:s');
			$checkReportdata->save();
		} else {
			$reportdata_obj = new reportdata();
			$reportdata_obj->date = $date;
			$reportdata_obj->gameid = $gameid;
			$reportdata_obj->adsnetworkid = $adsnetworkid;
			$reportdata_obj->countrycode = $countrycode;
			if ($checkLoggetrevenue) {
				$reportdata_obj->revenue = round($revenue * config('tygia.revenue')) + $checkLoggetrevenue->revenue;
			} else {
				$reportdata_obj->revenue = round($revenue * config('tygia.revenue'));
			}
			$reportdata_obj->save();
		}

		return 1;
	}

	public function insertReportdata_adwords($date, $gameid, $adsnetworkid, $countrycode, $cost, $budget, $ctr, $cr, $install, $cpi = '')
	{
		$ngay_hom_qua = date('Y-m-d', strtotime($date . " -1 day"));
		$checkReportdata = $this->checkReportdata($date, $gameid, $adsnetworkid, $countrycode);
		if ($checkReportdata) {
			$checkReportdata->updatedate = date('Y-m-d H:i:s');
			$checkReportdata->cost = $cost;
			if (!empty($budget)) {
				$checkReportdata->budget = $budget;
			}
			$checkReportdata->ctr = $ctr;
			$checkReportdata->cr = $cr;
			$checkReportdata->install = $install;
			if ($adsnetworkid == 2) {
				// adwords
				if (empty($checkReportdata->cpitarget)) {
					$checkReportdata_homqua = $this->checkReportdata($ngay_hom_qua, $gameid, $adsnetworkid, $countrycode);
					if ($checkReportdata_homqua) {
						$checkReportdata->cpitarget = $checkReportdata_homqua->cpitarget;
					}
				}
			} else {
				if (!empty($cpi)) {
					$checkReportdata->cpitarget = $cpi;
				}
			}
			$checkReportdata->save();
		} else {
			$reportdata_obj = new reportdata();
			$reportdata_obj->date = $date;
			$reportdata_obj->gameid = $gameid;
			$reportdata_obj->adsnetworkid = $adsnetworkid;
			$reportdata_obj->countrycode = $countrycode;
			$reportdata_obj->cost = $cost;
			if (!empty($budget)) {
				$reportdata_obj->budget = $budget;
			}
			$reportdata_obj->ctr = $ctr;
			$reportdata_obj->cr = $cr;
			$reportdata_obj->install = $install;
			if ($adsnetworkid == 2) {
				// adwords
				$checkReportdata_homqua = $this->checkReportdata($ngay_hom_qua, $gameid, $adsnetworkid, $countrycode);
				if ($checkReportdata_homqua) {
					$reportdata_obj->cpitarget = $checkReportdata_homqua->cpitarget;
				}
			} else {
				if (!empty($cpi)) {
					$reportdata_obj->cpitarget = $cpi;
				}
			}
			$reportdata_obj->save();
		}
	}

	public function insertReportdata_all_form($date, $gameid, $adsnetworkid, $countrycode, $revenue, $cost, $budget, $cpitarget, $ctr, $cr, $install)
	{
		$checkReportdata = $this->checkReportdata($date, $gameid, $adsnetworkid, $countrycode);
		if ($checkReportdata) {
			$checkReportdata->updatedate = date('Y-m-d H:i:s');
			$checkReportdata->revenue = $revenue;
			$checkReportdata->cost = $cost;
			$checkReportdata->budget = $budget;
			$checkReportdata->cpitarget = $cpitarget;
			$checkReportdata->ctr = $ctr;
			$checkReportdata->cr = $cr;
			$checkReportdata->install = $install;
			$checkReportdata->save();
		} else {
			$reportdata_obj = new reportdata();
			$reportdata_obj->date = $date;
			$reportdata_obj->gameid = $gameid;
			$reportdata_obj->adsnetworkid = $adsnetworkid;
			$reportdata_obj->countrycode = $countrycode;
			$reportdata_obj->revenue = $revenue;
			$reportdata_obj->cost = $cost;
			$reportdata_obj->budget = $budget;
			$reportdata_obj->cpitarget = $cpitarget;
			$reportdata_obj->ctr = $ctr;
			$reportdata_obj->cr = $cr;
			$reportdata_obj->install = $install;
			$reportdata_obj->save();
		}

		return 1;
	}

	public function sumReportdata($colsum, $date, $gameid = '', $adsnetworkid = '', $countrycode = '')
	{
		$sum = $this::where('date', '=', $date);
		if (!empty($gameid)) {
			$sum = $sum->where('gameid', '=', $gameid);
		}
		if (!empty($adsnetworkid)) {
			$sum = $sum->where('adsnetworkid', '=', $adsnetworkid);
		}
		if (!empty($countrycode)) {
			$sum = $sum->where('countrycode', '=', $countrycode);
		}
		$sum = $sum->sum($colsum);

		return $sum;
	}

	public function sumReportdataGameDate($colsum, $gameid, $datefrom, $dateto)
	{
		$sum = $this::where('date', '>=', $datefrom)
			->where('date', '<=', $dateto)
			->where('gameid', '=', $gameid);
		$sum = $sum->sum($colsum);

		return $sum;
	}

	public function getListWhereGameDate($gameid, $datefrom, $dateto)
	{
		$reportdata = $this::select('date', 'cost', 'revenue', 'install', 'countrycode', 'budget', 'cpitarget', 'ctr', 'cr', 'adsnetworkid')
			->where('date', '>=', $datefrom)
			->where('date', '<=', $dateto)
			->where('gameid', '=', $gameid)
			->get();

		return $reportdata;
	}

	public function getListAllWhereDate($datefrom, $dateto)
	{
		$reportdata = $this::select('date', 'cost', 'revenue', 'install', 'countrycode', 'budget', 'cpitarget', 'ctr', 'cr', 'adsnetworkid', 'gameid')
			->where('date', '>=', $datefrom)
			->where('date', '<=', $dateto)
			->get();

		return $reportdata;
	}

	public function getListWhereGameAll($gameid)
	{
		$reportdata = $this::select('date', 'cost', 'revenue', 'install', 'countrycode', 'budget', 'cpitarget', 'ctr', 'cr', 'adsnetworkid')
			->where('gameid', '=', $gameid)
			->get();

		return $reportdata;
	}

	public function getListWhereGameAdsCountryDate($gameid, $adsnetworkid, $countrycode, $datefrom, $dateto)
	{
		$reportdata = $this::select('date', 'cost', 'revenue', 'install', 'countrycode', 'budget', 'cpitarget', 'ctr', 'cr', 'adsnetworkid')
			->where('date', '>=', $datefrom)
			->where('date', '<=', $dateto)
			->where('gameid', '=', $gameid)
			->where('adsnetworkid', '=', $adsnetworkid)
			->where('countrycode', '=', $countrycode)
			->get();

		return $reportdata;
	}

	public function getListWhereGameAdsDate($gameid, $adsnetworkid, $date)
	{
		$reportdata = $this::select('date', 'cost', 'revenue', 'install', 'countrycode', 'budget', 'cpitarget', 'ctr', 'cr', 'adsnetworkid')
			->where('date', '=', $date)
			->where('gameid', '=', $gameid)
			->where('adsnetworkid', '=', $adsnetworkid)
			->get();

		return $reportdata;
	}

	public function getListWhereGameCountryDate($gameid, $countrycode, $datefrom, $dateto)
	{
		$reportdata = $this::select('date', 'cost', 'revenue', 'install', 'countrycode', 'budget', 'cpitarget', 'ctr', 'cr', 'adsnetworkid')
			->where('date', '>=', $datefrom)
			->where('date', '<=', $dateto)
			->where('gameid', '=', $gameid)
			->where('countrycode', '=', $countrycode)
			->get();

		return $reportdata;
	}

	public function getListAllWhereOneDate($date)
	{
		$reportdata = $this::select('date', 'cost', 'revenue', 'install', 'countrycode', 'budget', 'cpitarget', 'ctr', 'cr', 'adsnetworkid', 'gameid')
			->where('date', '=', $date)
			->get();

		return $reportdata;
	}
}
