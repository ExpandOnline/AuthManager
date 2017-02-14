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
