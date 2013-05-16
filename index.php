<?php
# Sets
ini_set('error_log', __FILE__.'.error.log'); # Log everything to ts.php.error.log
ini_set('log_errors', 1);
ini_set('display_errors', 0);
ini_set('ignore_repeated_errors', 1);
ini_set('html_errors', 0);
error_reporting(E_ALL);

# Require
require_once 'app/lib/Config.php';
require_once 'app/lib/Renderer.php';
require_once 'app/src/Config.php';
require_once 'app/src/App.php';

use Dirlister\src\Config,
    Dirlister\src\App,
    Dirlister\lib\Renderer
    ;

# Init App
$App = new App( new Config('app/config/app.php') );

# Authenticate
if (!$App->authenticate()) return;

# List the dir
try {
    $_SERVER += array('PATH_INFO' => '/');

    $base = new Renderer('app/resources/views/base.tpl.php');
    echo $base->render(array(
        'title' => $_SERVER['PATH_INFO'].' — '.$App->config->listing['title'],
        'page' => $App->actionDirListing($_SERVER['PATH_INFO'])
    ));
} catch (Exception $e){
    header('HTTP/1.1 500 Server error');
    echo '<h1>Server error</h1>';
    throw $e;
}
