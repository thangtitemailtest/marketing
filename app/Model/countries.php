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

	public function getListCountryArrayKeyCode(){
		$country = $this::getListCountryShow();
		$arr = [];
		foreach ($country as $item) {
			$arr[$item->code] = $item->name;
		}

		return $arr;
	}
}
