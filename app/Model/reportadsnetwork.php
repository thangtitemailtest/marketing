<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class reportadsnetwork extends Model
{
    protected $table = "reportadsnetwork";
	public $timestamps = false;

	public function checkReportadsnetwork($date = '', $adsnetworkid = '')
	{
		$report_obj = $this::where('date', '=', $date)
			->where('adsnetworkid', '=', $adsnetworkid)
			->first();

		return $report_obj;
	}

	public function insertReportadsnetwork_revenue($date = '', $adsnetworkid = '', $revennue = 0)
	{
		$checkReport = $this->checkReportadsnetwork($date, $adsnetworkid);
		if ($checkReport) {
			$checkReport->revenue = round($revennue * config('tygia.revenue'));
			$checkReport->updatedate = date('Y-m-d H:i:s');
			$checkReport->save();
		} else {
			$reportadsnetworkd_obj = new reportadsnetwork();
			$reportadsnetworkd_obj->date = $date;
			$reportadsnetworkd_obj->adsnetworkid = $adsnetworkid;
			$reportadsnetworkd_obj->revenue = round($revennue * config('tygia.revenue'));
			$reportadsnetworkd_obj->save();
		}

		return 1;
	}
	
	public function insertReportadsnetwork_all_form($date, $adsnetworkid, $revenue, $cost, $budget, $cpitarget, $ctr, $cr, $install)
	{
		$checkReport = $this->checkReportadsnetwork($date, $adsnetworkid);
		if ($checkReport) {
			$checkReport->updatedate = date('Y-m-d H:i:s');
			$checkReport->revenue = $revenue;
			$checkReport->cost = $cost;
			$checkReport->budget = $budget;
			$checkReport->cpitarget = $cpitarget;
			$checkReport->ctr = $ctr;
			$checkReport->cr = $cr;
			$checkReport->install = $install;
			$checkReport->save();
		} else {
			$reportadsnetworkd_obj = new reportadsnetwork();
			$reportadsnetworkd_obj->date = $date;
			$reportadsnetworkd_obj->adsnetworkid = $adsnetworkid;
			$reportadsnetworkd_obj->revenue = $revenue;
			$reportadsnetworkd_obj->cost = $cost;
			$reportadsnetworkd_obj->budget = $budget;
			$reportadsnetworkd_obj->cpitarget = $cpitarget;
			$reportadsnetworkd_obj->ctr = $ctr;
			$reportadsnetworkd_obj->cr = $cr;
			$reportadsnetworkd_obj->install = $install;
			$reportadsnetworkd_obj->save();
		}

		return 1;
	}
}
