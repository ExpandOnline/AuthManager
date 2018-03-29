<?php

/**
 * API routes
 */
Router::connect('/AuthManager/api/1/users',
	array(
		'plugin' => 'AuthManager',
		'controller' => 'media_platform_users_api',
		'action' => 'index'
	)
);

Router::connect('/auth/add-platform/:platform', [
	'plugin' => 'AuthManager',
	'controller' => 'media_platform_users',
	'action' => 'createByPlatform',

], ['pass' => ['platform']]);

Router::connect('/auth/validate/:platform/:userId/:timestamp/:hash', [
	'plugin' => 'AuthManager',
	'controller' => 'media_platform_users',
	'action' => 'validateRedirect',

], ['pass' => ['platform', 'userId', 'timestamp', 'hash']]);