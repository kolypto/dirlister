<?php
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
        'title' => 'Dignio files',
        'page' => $App->actionDirListing($_SERVER['PATH_INFO'])
    ));
} catch (Exception $e){
    throw $e;
}
