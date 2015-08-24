<?php

Router::connect('/AuthManager/:controller/:action/*', array('plugin' => 'AuthManager'));