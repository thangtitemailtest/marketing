<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class timeupdatedata extends Model
{
	protected $table = "timeupdatedata";
	public $timestamps = false;

	public function insertTimeUpdate($date = '', $adsnetwork = '', $mess = '')
	{
		$timeupdatedata = new timeupdatedata();
		$timeupdatedata->createdate = date('Y-m-d H:i:s');
		$timeupdatedata->updatedate = $date;
		$timeupdatedata->adsnetwork = $adsnetwork;
		$timeupdatedata->mess = $mess;
		$timeupdatedata->save();
	}
}
