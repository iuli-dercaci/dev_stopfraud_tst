<?php
/**
 * app config
 * @author Iuli Dercaci <luli.dercaci@site-me.info>
 * @date 10/29/12
 * @time 11:00 PM
 */

return array(
    'db' => array(
        'host'     => 'localhost',
        'port'     => '3306',
        'database' => 'data_stopfraud_db',
        'username' => 'phpuser',
        'password' => 'buratino'
    ),
    'paths' => array(
        'classes'     => 'Classes',
        'controllers' => 'Controllers',
        'views'       => 'Views',
        'layouts'     => 'Layouts',
        'helpers'     => 'Helpers',
        'upload'      => 'upload'
    ),
    'controllers' => array(
        'postfix' => 'Controller'
    ),
    'actions' => array(
        'postfix' => 'Action'
    ),
    'dev_mode' => true,
);
