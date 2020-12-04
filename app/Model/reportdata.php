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
}
