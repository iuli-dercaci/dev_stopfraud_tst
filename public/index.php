<?php
/**
 *
 * @author Iuli Dercaci <luli.dercaci@site-me.info>
 * @date 10/29/12
 * @time 10:58 PM
 */
session_start();
error_reporting(-1);
$root = $_SERVER['DOCUMENT_ROOT'] . '/../';

//config
try {
    if(!file_exists($root . '/config/main.php')) {
        throw new Exception('config file is not found');
    }
    $_SESSION['config'] = require($root . '/config/main.php');
    $_SESSION['config']['paths']['root'] = $root;
} catch (Exception $e) {
    echo $e->getMessage();
}
if (isset($config['dev_mode']) && (bool)$config['dev_mode']){
    error_reporting(-1);
}

//get dispatch params
$params = array(
    'controller' => isset($_GET['page']) ? $_GET['page'] : false,
    'action'     => isset($_GET['action']) ? $_GET['action'] : false,
    'root'       => $root
);
//dispatcher
include_once $root . $_SESSION['config']['paths']['classes'] . '/Dispatcher.php';
include_once $root . $_SESSION['config']['paths']['classes'] . '/View.php';
//run application
try {
    $dispatcher = new Dispatcher($_SESSION['config'], $params);
    $dispatcher->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
