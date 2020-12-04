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
			$checkReport->revenue = $revennue;
			$checkReport->updatedate = date('Y-m-d H:i:s');
			$checkReport->save();
		} else {
			$checkReport = new reportgame();
			$checkReport->date = $date;
			$checkReport->gameid = $gameid;
			$checkReport->revenue = $revennue;
			$checkReport->save();
		}

		return 1;
	}
}
