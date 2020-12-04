<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Checklogin
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (Auth::check()) {
			$url = $request->path();
			$permission = json_decode(Auth::user()->permission, true);
			if (empty($permission)) $permission[0] = '';
			if (in_array($url, $permission) || $permission[0] == 'admin' || $url == '/') {
				return $next($request);
			}else {
				return redirect()->route('get-khongcoquyen');
			}
		}else {
			return redirect()->route('get-login');
		}

		//return $next($request);

	}
}
