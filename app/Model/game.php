<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class game extends Model
{
    protected $table = "game";
	public $timestamps = false;

	public function getListGame(){
		$game_obj = $this::query()->get();

		return $game_obj;
	}
}
