<?php
/**
 *
 * @author Iuli Dercaci <luli.dercaci@site-me.info>
 * @date 10/29/12
 * @time 11:11 PM
 */
include_once 'View.php';


abstract class AbstractController
{
    protected $_view;
    protected $_config;
    protected $_helperPath;

    public function __construct(){
        $this->_config = $_SESSION['config'];
        $this->_view = new View($this->_config);
        $this->_helperPath = $this->_config['paths']['root'] . $this->_config['paths']['helpers'] . '/Action';
    }

    public function helper($name){
        $name = ucfirst($name);
        $path = sprintf('%s/%s.php', $this->_helperPath, $name);
        if(!is_file($path)){
            throw new Exception('view helper was not found in the path: ', $path);
        }
        include_once $path;

        return new $name;
    }
}
