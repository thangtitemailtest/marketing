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
			$checkReport->revenue = $revennue;
			$checkReport->updatedate = date('Y-m-d H:i:s');
			$checkReport->save();
		} else {
			$checkReport = new reportadsnetwork();
			$checkReport->date = $date;
			$checkReport->adsnetworkid = $adsnetworkid;
			$checkReport->revenue = $revennue;
			$checkReport->save();
		}

		return 1;
	}
}
