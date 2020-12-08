<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class settingcountry extends Model
{
	protected $table = "settingcountry";
	public $timestamps = false;

	public function getListCountryGame($gameid)
	{
		$list = $this::where('gameid', '=', $gameid)->orderBy('name','ASC')->get();

		return $list;
	}

	public function getListCountryGameDesc($gameid)
	{
		$list = $this::where('gameid', '=', $gameid)->orderBy('name','DESC')->get();

		return $list;
	}

	public function checkSettingCountry($gameid, $countrycode)
	{
		$check = $this::where('gamid','=',$gameid)->where('code','=',$countrycode)->first();

		return $check;
	}

	public function deleteSettingCountryGame($gameid){
		$this::where('gameid','=',$gameid)->delete();

		return 1;
	}

	public function insertSettingCountry($gameid, $countrycode, $countryname)
	{
		$settingcountry_obj = new settingcountry();
		$settingcountry_obj->gameid = $gameid;
		$settingcountry_obj->code = $countrycode;
		$settingcountry_obj->name = $countryname;
		$settingcountry_obj->save();

		return 1;
	}
}
