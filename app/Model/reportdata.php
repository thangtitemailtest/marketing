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

	public function insertReportdata_adwords($date, $gameid, $adsnetworkid, $countrycode, $cost, $budget, $ctr, $cr, $install, $cpi = '')
	{
		$checkReportdata = $this->checkReportdata($date, $gameid, $adsnetworkid, $countrycode);
		if ($checkReportdata) {
			$checkReportdata->updatedate = date('Y-m-d H:i:s');
			$checkReportdata->cost = $cost;
			$checkReportdata->budget = $budget;
			$checkReportdata->ctr = $ctr;
			$checkReportdata->cr = $cr;
			$checkReportdata->install = $install;
			if (!empty($cpi)){
				$checkReportdata->cpitarget = $cpi;
			}
			$checkReportdata->save();
		} else {
			$reportdata_obj = new reportdata();
			$reportdata_obj->date = $date;
			$reportdata_obj->gameid = $gameid;
			$reportdata_obj->adsnetworkid = $adsnetworkid;
			$reportdata_obj->countrycode = $countrycode;
			$reportdata_obj->cost = $cost;
			$reportdata_obj->budget = $budget;
			$reportdata_obj->ctr = $ctr;
			$reportdata_obj->cr = $cr;
			$reportdata_obj->install = $install;
			if (!empty($cpi)){
				$reportdata_obj->cpitarget = $cpi;
			}
			$reportdata_obj->save();
		}
	}

	public function insertReportdata_all_form($date, $gameid, $adsnetworkid, $countrycode, $revenue, $cost, $budget, $cpitarget, $ctr, $cr, $install)
	{
		$checkReportdata = $this->checkReportdata($date, $gameid, $adsnetworkid, $countrycode);
		if ($checkReportdata) {
			$checkReportdata->updatedate = date('Y-m-d H:i:s');
			if (!empty($revenue)) {
				$checkReportdata->revenue = $revenue;
			}
			if (!empty($cost)) {
				$checkReportdata->cost = $cost;
			}
			if (!empty($budget)) {
				$checkReportdata->budget = $budget;
			}
			if (!empty($cpitarget)) {
				$checkReportdata->cpitarget = $cpitarget;
			}
			if (!empty($ctr)) {
				$checkReportdata->ctr = $ctr;
			}
			if (!empty($cr)) {
				$checkReportdata->cr = $cr;
			}
			if (!empty($install)) {
				$checkReportdata->install = $install;
			}
			$checkReportdata->save();
		} else {
			$reportdata_obj = new reportdata();
			$reportdata_obj->date = $date;
			$reportdata_obj->gameid = $gameid;
			$reportdata_obj->adsnetworkid = $adsnetworkid;
			$reportdata_obj->countrycode = $countrycode;
			if (!empty($revenue)) {
				$checkReportdata->revenue = $revenue;
			}
			if (!empty($cost)) {
				$checkReportdata->cost = $cost;
			}
			if (!empty($budget)) {
				$checkReportdata->budget = $budget;
			}
			if (!empty($cpitarget)) {
				$checkReportdata->cpitarget = $cpitarget;
			}
			if (!empty($ctr)) {
				$checkReportdata->ctr = $ctr;
			}
			if (!empty($cr)) {
				$checkReportdata->cr = $cr;
			}
			if (!empty($install)) {
				$checkReportdata->install = $install;
			}
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
}
