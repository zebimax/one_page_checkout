<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 12.02.15
 * Time: 18:31
 */
define('APP_DIR', dirname(__DIR__ . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
define('VIEW_APP_DIR', APP_DIR . 'views' . DIRECTORY_SEPARATOR);
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
switch (true) {
    case (isset($_POST['success']) && $_POST['success']) :
        $view = VIEW_APP_DIR . 'success.phtml';
        $app->success();
        break;
    case (isset($_POST['checkout']) && $_POST['checkout']) :
        $view = VIEW_APP_DIR . $app->checkout($_POST) . '.phtml';
        break;
    default:
        $view = VIEW_APP_DIR . 'index.phtml';
        $app->index(VIEW_APP_DIR . 'index.phtml');
        break;
}

$app->setView($view)->render(APP_DIR . 'layout.phtml');