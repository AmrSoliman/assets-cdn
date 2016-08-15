# assets-cdn
Easily display your Laravel assets through a cdn without having to change a line in your views.

## Installation
Add the following require to your `composer.json` file:

	"require": {
		...
		"thrustdivision/assets-cdn": "1.*"
	},
	...
    "post-install-cmd": [
    	...
    	"php artisan assets-cdn:update"
    ],

Then run `composer update`

Add `"ThrustDivision\AssetsCdn\AssetsCdnServiceProvider",` to the list of providers in `config/app.php`.

Run `php artisan vendor:publish`

Go to file `config/assets-cdn.php` and configure your CDN. We recommend only enabling it in production.

## Note on the CDN
Any CDN works provided that the following conditions are met:

1. The path on your server is the same as the path on the CDN (i.e. yoursite.com/assets/image.png = the.cdn.net/assets/image.png)
2. The CDN passes through query strings, so that we can invalidate previous versions of your file when you install a new commit?