<?php

use Slim\App;

require_once __DIR__ . "/../vendor/autoload.php";

$settings = require_once  __DIR__ . "/../config/settings.php";

// $app = new App($settings);
$app = new App();

$container = $app->getContainer();

require_once __DIR__ . '/../config/errHandler.php';

// $routeContainers = require_once __DIR__. '/routecontainers.php';

// $routeContainers($container);

require_once __DIR__ . "/../config/database.php";
// require_once __DIR__  . "/DbConnect.php";
// require_once __DIR__ . "/../config/DbConnect.php";
require_once __DIR__ . "/../config/DbHandler.php";
require_once __DIR__ . "/../lib/Helper.php";
require_once __DIR__ . "/../lib/HelperPage.php";
require_once __DIR__ . "/../lib/MailerHelper.php";
require_once __DIR__ . "/../lib/UtilHelper.php";
require_once __DIR__ . "/../lib/HtmlTemplateHelper.php";
require_once __DIR__ . "/../lib/auth.php";

// error_reporting(E_ALL ^ E_NOTICE);
require_once __DIR__ . '/routes/Routes.php';



$middleware = require_once __DIR__ . "/../config/middleware.php";

$middleware($app);

// $app->stop();


$app->run();
