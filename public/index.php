<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 12.02.15
 * Time: 18:31
 */
use Application\Tools\XMLSaver;

define('APP_DIR', dirname(__DIR__ . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
define('VIEW_APP_DIR', APP_DIR . 'views' . DIRECTORY_SEPARATOR);
define('PRODUCT_HOST', 'dod-product.local');

$loader = require_once APP_DIR .'/vendor/autoload.php';

// register classes with namespaces
$loader->add('', APP_DIR);
//require_once(APP_DIR . 'src/App.php');
$view = VIEW_APP_DIR . 'index.phtml';
$config = new Config(include APP_DIR . 'config/config.php');
$app = new App(
    $config,
    MysqlDb::getInstance($config->get('mysql'))
);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$app->setTitle('Main page');
$result = false;
switch (true) {
    case ($uri == '/success' && isset($_GET['order_id'])):
        $app->setSaver(new XMLSaver(APP_DIR . 'xml' . DIRECTORY_SEPARATOR));
        $result = $app->success($_GET['order_id']);
        break;
    case ($uri == '/test'):
        $app->test();
        break;
    case (isset($_POST['checkout']) && $_POST['checkout']) :
        $result = $app->checkout($_POST);
        break;
    default:
        $app->index();
        break;
}
$view = $result ? VIEW_APP_DIR . 'success.phtml' : VIEW_APP_DIR . 'index.phtml';

$app->setView($view)->render(APP_DIR . 'layout.phtml');