<?php
define('AUTH_MANAGER_API_CONFIG_DIR', CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS);

define('GOOGLE_ANALYTICS_READ_WRITE_SETTINGS_JSON_FILE', AUTH_MANAGER_API_CONFIG_DIR . 'googleAnalyticsReadWrite.json');

CakeLog::config('AuthManager', array(
	'engine' => 'FileLog',
	'types' => array('CIA'),
	'file' => 'authManager.log',
));