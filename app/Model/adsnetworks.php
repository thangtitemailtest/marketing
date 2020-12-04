<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class adsnetworks extends Model
{
    protected $table = "adsnetworks";
	public $timestamps = false;

	public function getListAdsnetworks(){
		$adsnetworks = $this::query()->get();

		return $adsnetworks;
	}

	public function getAdsnetworkidDesc(){
		$adsnetworks = $this::select('adsnetworkid')->orderBy('adsnetworkid','DESC')->first();

		return $adsnetworks->adsnetworkid;
	}

	public function insertAdsnetworks($adsnetworkname){
		$adsnetworkid_desc = $this->getAdsnetworkidDesc();
		$adsnetworkid = $adsnetworkid_desc + 1;
		$adsnerwork_obj = new adsnetworks();
		$adsnerwork_obj->adsnetworkid = $adsnetworkid;
		$adsnerwork_obj->adsnetworkname = $adsnetworkname;
		$adsnerwork_obj->save();

		return $adsnetworkid;
	}
}
