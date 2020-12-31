<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class settinggetrevenue extends Model
{
	protected $table = "settinggetrevenue";
	public $timestamps = false;

	public function getSettingWhereGameKenhToArr($gameid, $kenh)
	{
		$setting = $this::where('gameid', '=', $gameid)->where('kenh', '=', $kenh)->get();
		$arr = [];
		foreach ($setting as $item) {
			$arr[$item->adsnetworkid] = 1;
		}

		return $arr;
	}

	public function insertSetting($gameid, $kenh, $adsnetworkid)
	{
		$setting_obj = new settinggetrevenue();
		$setting_obj->gameid = $gameid;
		$setting_obj->kenh = $kenh;
		$setting_obj->adsnetworkid = $adsnetworkid;
		$setting_obj->save();
	}

	public function deleteSetting($gameid, $kenh)
	{
		$this::where('gameid', '=', $gameid)->where('kenh', '=', $kenh)->delete();
	}
}
