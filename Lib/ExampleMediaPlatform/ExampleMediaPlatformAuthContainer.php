<?php
App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class ExampleMediaPlatformAuthContainer
 *
 * This class will be returned to dependencies of the AuthManager plugin.
 * The authenticated API object will be stored by this class and used by the dependencies to communicate
 * with the Media Platform API.
 * This class has no methods because every API is different.
 * The only thing required to set is the Media Platform user ID.
 */
class ExampleMediaPlatformAuthContainer extends AuthContainer {

}