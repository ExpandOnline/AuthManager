<?php
CakeLog::config('AuthManager', array(
	'engine' => 'FileLog',
	'types' => array('AuthManager'),
	'file' => 'authManager.log',
));