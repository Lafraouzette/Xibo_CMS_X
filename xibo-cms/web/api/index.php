<?php


use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Xibo\Factory\ContainerFactory;

DEFINE('XIBO', true);
define('PROJECT_ROOT', realpath(__DIR__ . '/../..'));

error_reporting(0);
ini_set('display_errors', 0);

require PROJECT_ROOT . '/vendor/autoload.php';

if (!file_exists(PROJECT_ROOT . '/web/settings.php'))
    die('Not configured');

// Create the container for dependency injection.
try {
    $container = ContainerFactory::create();
} catch (Exception $e) {
    die($e->getMessage());
}

$container->set('logger', function () {
    $logger = new Logger('API');

    $uidProcessor = new UidProcessor();
    // db
    $dbhandler  =  new \Xibo\Helper\DatabaseLogHandler();

    $logger->pushProcessor($uidProcessor);
    $logger->pushHandler($dbhandler);

    return $logger;
});

// Create a Slim application
$app = \DI\Bridge\Slim\Bridge::create($container);
$app->setBasePath($container->get('basePath'));

// Config
$app->config = $container->get('configService');
$routeParser = $app->getRouteCollector()->getRouteParser();
$container->set('name', 'API');

// Handle additional Middleware
\Xibo\Middleware\State::setMiddleWare($app);

$app->add(new \Xibo\Middleware\ConnectorMiddleware($app));
$app->add(new \Xibo\Middleware\ListenersMiddleware($app));
$app->add(new \Xibo\Middleware\ApiAuthorization($app));
$app->add(new \Xibo\Middleware\State($app));
$app->add(new \Xibo\Middleware\Log($app));
$app->add(new \Xibo\Middleware\Storage($app));
$app->add(new \Xibo\Middleware\Xmr($app));
$app->addRoutingMiddleware();
$app->add(new \Xibo\Middleware\TrailingSlashMiddleware($app));

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(
    \Xibo\Helper\Environment::isDevMode() || \Xibo\Helper\Environment::isForceDebugging(),
    true,
    true
);
$errorMiddleware->setDefaultErrorHandler(\Xibo\Middleware\Handlers::jsonErrorHandler($container));

// All routes
require PROJECT_ROOT . '/lib/routes.php';

$app->get('/', ['\Xibo\Controller\Login','About']);
$app->post('/library/mcaas/{id}', ['\Xibo\Controller\Library','mcaas']);

// Run app
$app->run();
