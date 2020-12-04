<?php
return [
	//Environment=> test/production
	'env' => 'test',
	//Google Ads
	'production' => [
		'developerToken' => "twRYeMqpeDSJQXJYFuBNAQ",
		'clientCustomerId' => "256-247-7293",
		'userAgent' => "xgamestudio",
		'clientId' => "1015179900122-il8sgguneof0ik9vm7r30kndp87rok0m.apps.googleusercontent.com",
		'clientSecret' => "YrDw9iAowanFikZaQiAYR9Pc",
		'refreshToken' => "1//0eUBp9kCuqKDTCgYIARAAGA4SNwF-L9IrGenpmtZ75T2uUvhRkN6kXIFSGl7V_HJp6eu-k40ki8ESreAGIP7LRZJBqZjD61tIkRM"
	],
	'test' => [
		'developerToken' => "twRYeMqpeDSJQXJYFuBNAQ",
		'clientCustomerId' => "256-247-7293",
		'userAgent' => "xgamestudio",
		'clientId' => "1015179900122-il8sgguneof0ik9vm7r30kndp87rok0m.apps.googleusercontent.com",
		'clientSecret' => "YrDw9iAowanFikZaQiAYR9Pc",
		'refreshToken' => "1//0eUBp9kCuqKDTCgYIARAAGA4SNwF-L9IrGenpmtZ75T2uUvhRkN6kXIFSGl7V_HJp6eu-k40ki8ESreAGIP7LRZJBqZjD61tIkRM"
	],
	'oAuth2' => [
		'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
		'redirectUri' => 'urn:ietf:wg:oauth:2.0:oob',
		'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
		'scope' => 'https://www.googleapis.com/auth/adwords'
	]
];