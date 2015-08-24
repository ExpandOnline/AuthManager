<?php
/**
 * @var array $mediaPlatformUsers
 * @var array $mediaPlatforms
 */
echo $this->Session->flash();

echo $this->Html->tag('h1', __d('AuthManager', 'Media platformen koppelingen'));

echo $this->Form->create('MediaPlatform', array(
	'url' => array(
		'plugin' => 'auth_manager',
		'controller' => 'media_platform_users',
		'action' => 'addUser'
	)
));
echo $this->Html->div('well', null);
echo $this->Html->tag('strong', __d('AuthManager', 'Koppel een nieuw account')) . '&nbsp;'
	. $this->Form->input('id', array(
		'options' => $mediaPlatforms,
		'label' => __d('AuthManager', 'Media platform')
	))
	. $this->Form->button('Koppel', array(
		'class' => 'btn btn-primary'
	));
echo '</div>';
$this->Form->end();

echo $this->Table->create();
echo $this->Table->head(array(
	__d('AuthManager', 'Gebruikersnaam'),
	__d('AuthManager', 'Media platform'),
	__d('AuthManager', 'Datum toegevoegd'),
	__d('AuthManager', 'Datum gewijzigd'),
));
foreach ($mediaPlatformUsers as $mediaPlatformUser) {
	echo $this->Table->row(array(
		$mediaPlatformUser['MediaPlatformUser']['username'],
		$mediaPlatformUser['MediaPlatform']['name'],
		$mediaPlatformUser['MediaPlatformUser']['created'],
		$mediaPlatformUser['MediaPlatformUser']['modified'],
	));
}
echo $this->Table->end();