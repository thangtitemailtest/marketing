<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		/**
		 * Paginate a standard Laravel Collection.
		 *
		 * @param int $perPage
		 * @param int $total
		 * @param int $page
		 * @param string $pageName
		 * @return array
		 */
		Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
			$page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

			return new LengthAwarePaginator(
				$this->forPage($page, $perPage),
				$total ?: $this->count(),
				$perPage,
				$page,
				[
					'path' => LengthAwarePaginator::resolveCurrentPath(),
					'pageName' => $pageName,
				]
			);
		});

		view()->composer('master', function ($view) {
			$permission = json_decode(Auth::user()->permission, true);
			if (empty($permission)) $permission[0] = '';

			$view->with('permission', $permission);
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
