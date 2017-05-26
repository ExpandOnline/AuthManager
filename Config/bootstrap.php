<?php
CakeLog::config('AuthManager', array(
	'engine' => 'FileLog',
	'types' => array('AuthManager'),
	'file' => 'authManager.log',
));
Configure::load('AuthManager.API/UserCredentials');
Configure::write('AuthManager.live', Configure::read('DEO.live'));