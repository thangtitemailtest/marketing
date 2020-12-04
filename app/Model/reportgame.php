<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class reportgame extends Model
{
	protected $table = "reportgame";
	public $timestamps = false;

	public function checkReportgame($date = '', $gameid = '')
	{
		$report_obj = $this::where('date', '=', $date)
			->where('gameid', '=', $gameid)
			->first();

		return $report_obj;
	}

	public function insertReportgame_revenue($date = '', $gameid = '', $revennue = 0)
	{
		$checkReport = $this->checkReportgame($date, $gameid);
		if ($checkReport) {
			$checkReport->revenue = round($revennue * config('tygia.revenue'));
			$checkReport->updatedate = date('Y-m-d H:i:s');
			$checkReport->save();
		} else {
			$reportgame_obj = new reportgame();
			$reportgame_obj->date = $date;
			$reportgame_obj->gameid = $gameid;
			$reportgame_obj->revenue = round($revennue * config('tygia.revenue'));
			$reportgame_obj->save();
		}

		return 1;
	}

	public function insertReportgame_all_form($date, $gameid, $revenue, $cost, $budget, $cpitarget, $ctr, $cr, $install)
	{
		$checkReport = $this->checkReportgame($date, $gameid);
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
			$reportgame_obj = new reportgame();
			$reportgame_obj->date = $date;
			$reportgame_obj->gameid = $gameid;
			$reportgame_obj->revenue = $revenue;
			$reportgame_obj->cost = $cost;
			$reportgame_obj->budget = $budget;
			$reportgame_obj->cpitarget = $cpitarget;
			$reportgame_obj->ctr = $ctr;
			$reportgame_obj->cr = $cr;
			$reportgame_obj->install = $install;
			$reportgame_obj->save();
		}

		return 1;
	}
}
