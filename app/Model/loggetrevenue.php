<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class loggetrevenue extends Model
{
	protected $table = "loggetrevenue";
	public $timestamps = false;

	public function insertLogGetRevenue($date, $gameid, $adsnetworkid, $countrycode, $revenue)
	{
		$loggetrevenue_obj = new loggetrevenue();
		$loggetrevenue_obj->date = $date;
		$loggetrevenue_obj->gameid = $gameid;
		$loggetrevenue_obj->adsnetworkid = $adsnetworkid;
		$loggetrevenue_obj->countrycode = $countrycode;
		$loggetrevenue_obj->revenue = round($revenue * config('tygia.revenue'));
		$loggetrevenue_obj->save();
	}

	public function getLogGetRevenue($date, $gameid, $adsnetworkid, $countrycode)
	{
		$loggetrevenue = $this::where('date', '=', $date)
			->where('gameid', '=', $gameid)
			->where('adsnetworkid', '=', $adsnetworkid)
			->where('countrycode', '=', $countrycode)
			->first();

		return $loggetrevenue;
	}

	public function deleteDb()
	{
		$this::query()->delete();
	}
}
