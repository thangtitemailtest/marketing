<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class countries extends Model
{
	protected $table = "countries";
	public $timestamps = false;

	public function getListCountry()
	{
		$country_obj = $this::query()->get();

		return $country_obj;
	}

	public function getListCountryShow()
	{
		$coutry_obj = $this::where('show', '=', 1)->get();

		return $coutry_obj;
	}

	public function updateCountryShow($json)
	{
		$this::query()->update(['show' => 0]);

		foreach ($json as $item) {
			$this::where('code', '=', $item['code'])->update(['show' => 1]);
		}

		return 1;
	}

	public function getListCountryArrayKeyCode($gameid)
	{
		$settingcountry = new settingcountry();
		$country = $settingcountry->getListCountryGame($gameid);
		$arr = [];
		foreach ($country as $item) {
			$arr[$item->code] = $item->name;
		}

		return $arr;
	}

	public function getCountry($countrycode)
	{
		$country = $this::where('code', '=', $countrycode)->first();

		return $country;
	}

	public function getListCountryAdd()
	{
		$country = $this::where('id', '>', 250)->get();

		return $country;
	}

	public function insertCountry($tennuoc, $countrycode)
	{
		$country_obj = new countries();
		$country_obj->name = $tennuoc;
		$country_obj->code = $countrycode;
		$country_obj->save();

		return 1;
	}

	public function deleteCountry($id)
	{
		$this::where('id', '=', $id)->delete();

		return 1;
	}
}
