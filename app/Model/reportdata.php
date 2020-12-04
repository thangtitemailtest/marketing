<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

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

	public function insertReportdata_revenue($date = '', $gameid = '', $adsnetworkid = '', $countrycode = '', $revennue = 0)
	{
		$checkReportdata = $this->checkReportdata($date, $gameid, $adsnetworkid, $countrycode);
		if ($checkReportdata) {
			$checkReportdata->revenue = $revennue;
			$checkReportdata->updatedate = date('Y-m-d H:i:s');
			$checkReportdata->save();
		} else {
			$reportdata_obj = new reportdata();
			$reportdata_obj->date = $date;
			$reportdata_obj->gameid = $gameid;
			$reportdata_obj->adsnetworkid = $adsnetworkid;
			$reportdata_obj->countrycode = $countrycode;
			$reportdata_obj->revenue = $revennue;
			$reportdata_obj->save();
		}

		return 1;
	}
}
