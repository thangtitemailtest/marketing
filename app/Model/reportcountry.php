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
			$checkReport->revenue = round($revennue * config('tygia.revenue'));
			$checkReport->updatedate = date('Y-m-d H:i:s');
			$checkReport->save();
		} else {
			$reportcountry_obj = new reportcountry();
			$reportcountry_obj->date = $date;
			$reportcountry_obj->countrycode = $countrycode;
			$reportcountry_obj->revenue = round($revennue * config('tygia.revenue'));
			$reportcountry_obj->save();
		}

		return 1;
	}

	public function insertReportcountry_all_form($date, $countrycode, $revenue, $cost, $budget, $cpitarget, $ctr, $cr, $install)
	{
		$checkReport = $this->checkReportcountry($date, $countrycode);
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
			$reportcountry_obj = new reportcountry();
			$reportcountry_obj->date = $date;
			$reportcountry_obj->countrycode = $countrycode;
			$reportcountry_obj->revenue = $revenue;
			$reportcountry_obj->cost = $cost;
			$reportcountry_obj->budget = $budget;
			$reportcountry_obj->cpitarget = $cpitarget;
			$reportcountry_obj->ctr = $ctr;
			$reportcountry_obj->cr = $cr;
			$reportcountry_obj->install = $install;
			$reportcountry_obj->save();
		}

		return 1;
	}

	public function getListWhereDate($datefrom, $dateto)
	{
		$country = $this::where('date', '>=', $datefrom)
			->where('date', '<=', $dateto)
			->get();

		return $country;
	}
}
