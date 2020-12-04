<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class listmodule extends Model
{
    protected $table = "listmodule";
	public $timestamps = false;

	public function getListmodule(){
		$listmodule = $this::query();

		return $listmodule;
	}
}
