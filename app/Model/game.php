<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class game extends Model
{
	protected $table = "game";
	public $timestamps = false;

	public function getListGame()
	{
		$game_obj = $this::query()->get();

		return $game_obj;
	}

	public function getListGameArrayPakage()
	{
		$games = $this->getListGame();
		$arr_game = [];
		foreach ($games as $item) {
			if (!empty($item->packagename)) {
				$arr_game[$item->packagename] = $item->gameid;
			}
		}

		return $arr_game;
	}

	public function getGameidDesc()
	{
		$game_obj = $this::select('gameid')->orderBy('gameid', 'DESC')->first();

		return $game_obj->gameid;
	}

	public function insertGame($packagename, $gamename, $platform = '')
	{
		$gameid_desc = $this->getGameidDesc();
		$gameid = $gameid_desc + 1;
		$games = new game();
		$games->gamename = $gamename;
		$games->packagename = $packagename;
		$games->platform = $platform;
		$games->gameid = $gameid;
		$games->save();

		return $gameid;
	}
}
