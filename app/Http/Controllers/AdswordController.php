<?php

namespace App\Http\Controllers;

use Edujugon\GoogleAds\GoogleAds;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Illuminate\Http\Request;

class AdswordController extends Controller
{
	function getAdsword()
	{
		$ads = new GoogleAds();
		$camp = $ads->service(CampaignService::class)->select(['Id', 'Name', 'Status', 'ServingStatus', 'StartDate', 'EndDate'])->get();
		echo "<pre>";
		print_r($camp);
		echo "</pre>";


	}
}
