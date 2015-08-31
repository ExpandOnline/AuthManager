<?php
CakeLog::config('AuthManager', array(
	'engine' => 'FileLog',
	'types' => array('AuthManager'),
	'file' => 'authManager.log',
));

// PSR-4 Autoloader for Facebook-ads-sdk. We'll be using this until we default to PHP 5.4.
// Git clone the facebook/facebook-php-ads-sdk into the Lib folder.
spl_autoload_register(function ($class) {
	$prefix = 'FacebookAds\\';
	$baseDir = CakePlugin::path('AuthManager') . 'Lib' . DS . 'facebook-php-ads-sdk' . DS . 'src' . DS . 'FacebookAds' . DS;

	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return;
	}

	$relativeClass = substr($class, $len);
	$file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});

// PSR-4 Autoloader for facebook-php-sdk-v4. We'll be using this until we default to PHP 5.4.
// Git clone the facebook/facebook-php-sdk-v4 into the Lib folder.
spl_autoload_register(function ($class) {
	$prefix = 'Facebook\\';
	$baseDir = CakePlugin::path('AuthManager') . 'Lib' . DS . 'facebook-php-sdk-v4' . DS . 'src' . DS . 'Facebook' . DS;

	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return;
	}

	$relativeClass = substr($class, $len);
	$file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
	if (file_exists($file)) {
		require_once $file;
	}
});