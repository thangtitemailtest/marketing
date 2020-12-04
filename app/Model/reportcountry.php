<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class reportcountry extends Model
{
	protected $table = "reportcountry";
	public $timestamps = false;

	public function checkReportcountry($date = '', $countrycode = '')
	{
		$report_obj = $this::where('date', '=', $date)
			->where('countrycode', '=', $countrycode)
			->first();

		return $report_obj;
	}

	public function insertReportcountry_revenue($date = '', $countrycode = '', $revennue = 0)
	{
		$checkReport = $this->checkReportcountry($date, $countrycode);
		if ($checkReport) {
			$checkReport->revenue = $revennue;
			$checkReport->updatedate = date('Y-m-d H:i:s');
			$checkReport->save();
		} else {
			$checkReport = new reportcountry();
			$checkReport->date = $date;
			$checkReport->countrycode = $countrycode;
			$checkReport->revenue = $revennue;
			$checkReport->save();
		}

		return 1;
	}
}
