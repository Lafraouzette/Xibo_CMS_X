<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

define('XIBO', true);
define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once PROJECT_ROOT . '/tests/LocalWebTestCase.php';
require_once PROJECT_ROOT . '/tests/XmdsTestCase.php';

if (!file_exists(PROJECT_ROOT . '/web/settings.php'))
    die('Not configured');

\Xibo\Tests\LocalWebTestCase::setEnvironment();

\Xibo\Helper\Translate::InitLocale(null, 'en_GB');