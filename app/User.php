<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\Traits\Searchable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable, Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 *
	 */
	protected $table = 'user';
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	public function getUser($id){
		$user = $this::find($id);

		return $user;
	}

	public function getListuser(){
		$user = $this::query();

		return $user;
	}

	public function xoaUser($id){
		$user = $this->getUser($id);
		if ($user){
			$user->delete();

			return 1;
		}else{
			return 0;
		}
	}
}
