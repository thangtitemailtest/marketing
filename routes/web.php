<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/login', 'HomeController@getLogin')->name('get-login');
Route::post('/login-post', 'HomeController@postLogin')->name('post-login');
Route::get('/logout', 'HomeController@logout')->name('get-logout');
Route::post('/thaydoithongtin-post', 'HomeController@postThaydoithongtin')->name('post-thaydoithongtin');
Route::post('/themmoiuser-post', 'HomeController@postThemmoiuser')->name('post-themmoiuser');
Route::post('/phanquyenuser-post', 'HomeController@postPhanquyenuser')->name('post-phanquyenuser');

Route::get('/khongcoquyentruycap', 'HomeController@getKhongcoquyen')->name('get-khongcoquyen');
Route::get('/thaydoithongtin', 'HomeController@getThaydoithongtin')->name('get-thaydoithongtin');

Route::middleware('Checklogin')->group(function () {
	Route::get('/', 'HomeController@getIndex')->name('get-index');

	Route::get('/danhsachuser', 'HomeController@getDanhsachuser')->name('get-danhsachuser');
	Route::get('/themmoiuser', 'HomeController@getThemmoiuser')->name('get-themmoiuser');
	Route::get('/xoauser/{id}', 'HomeController@getXoauser')->name('get-xoauser');
	Route::get('/resetpassword/{id}', 'HomeController@getResetpassword')->name('get-resetpassword');

	Route::get('/phanquyenuser/{id}', 'HomeController@getPhanquyenuser')->name('get-phanquyenuser');

	Route::get('/capnhatdulieu', 'MarketingController@getCapnhatdulieu')->name('get-capnhatdulieu');

	/*Report*/
	Route::group(['prefix' => 'report'], function () {
		//Route::get('/', 'chartController@index')->name('get.report');
	});
	/*END Report*/
});

Route::middleware('Checklogout')->group(function () {
	Route::get('/themdulieu', 'MarketingController@getThemdulieu')->name('get-themdulieu');
	Route::post('/themdulieu-post', 'MarketingController@postThemdulieu')->name('post-themdulieu');
	Route::get('/bangthemdulieu', 'MarketingController@getBangthemdulieu')->name('get-bangthemdulieu');
	Route::get('/bangthemdulieuthongsoads', 'MarketingController@getBangthemdulieuThongsoads')->name('get-bangthemdulieu-thongsoads');
	Route::get('/countrygame', 'MarketingController@getCountrygame')->name('get-countrygame');
	Route::get('/caidatnuoc', 'MarketingController@getCaidatnuoc')->name('get-caidatnuoc');
	Route::post('/caidatnuoc-post', 'MarketingController@postCaidatnuoc')->name('post-caidatnuoc');
	Route::get('/thongkedulieutheoquocgia', 'MarketingController@getThongkedulieutheoquocgia')->name('get-thongkedulieutheoquocgia');

	Route::get('/getoverall', 'MarketingController@getOverall')->name('get-overall');
	Route::get('/getoverallcountry', 'MarketingController@getOverallCountry')->name('get-overall-country');
	Route::get('/getsummary', 'MarketingController@getSummary')->name('get-summary');
	Route::get('/summary-month', 'MarketingController@getSummarymonth')->name('get-summary-month');
	Route::get('/getcountry', 'MarketingController@getCountry')->name('get-country');
	Route::get('/thongkegame', 'MarketingController@getThongkegame')->name('get-thongkegame');
});


/*Ads*/
Route::get('/getdatamarketing', 'MarketingController@getDataMarketing')->name('get-datamarketing');

Route::get('/adsword', 'AdswordController@getAdsword')->name('get-asdwords');
Route::get('/ironsource', 'IronsourceController@getIronsource')->name('get-ironsource');
Route::get('/unityads', 'UnityadsController@getUnityads');
//Route::get('/revenueironsource', 'IronsourceController@getRevenueIronsource');
/*END Ads*/

//Route::get('/taoadmin', 'HomeController@taoadmin');

//Auth::routes();