<?php
/**
 *
 * @author Iuli Dercaci <luli.dercaci@site-me.info>
 * @date 10/30/12
 * @time 12:23 AM
 */

class Dispatcher
{
    private $_config = array();
    private $_params = array();
    private $_controller;

    /**
     * class constructor
     * @param array $config
     * @param array $params
     */
    public function __construct(array $config, $params = false){
        if(!is_array($params)){
            $params = $_SESSION['config'];
        }
        if (!isset($params['root'])){
            throw new Exception('Root is not defined in config passed to dispatcher');
        }
        $this->_config = $config;
        $this->_params = $params;
    }

    /**
     * run dispatcher (main side effect functionality)
     */
    public function run(){
        $this->_dispatch();
        $action = $this->_getAction($this->_controller);
        echo $this->_controller->{$action}();
    }

    /**
     * determining and including controller
     * @return bool
     * @throws Exception
     */
    private function _dispatch(){
        //controller
        $controllerName = $this->_getControllerName();

        try {
            $controllerPath = $this->_params['root']
                . '/' . $this->_config['paths']['controllers']
                . '/' . $controllerName . '.php';

            if (!file_exists($controllerPath)){
                header('HTTP/1.0 404 Not Found');
                throw new Exception('Controller not found at: ' . $controllerPath);
            }
            include_once $controllerPath;
            if (!class_exists($controllerName)){
                throw new Exception('controller class name ' . $controllerName . ' could not found');
            }
            $this->_controller = new $controllerName;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
    /**
     * getting the controller class name
     * @return string
     */
    private function _getControllerName(){

        if (array_key_exists('controller', $this->_params)
            && false != ($controller = trim($this->_params['controller']))
        ){
            $controller = filter_var($this->_params['controller'], FILTER_SANITIZE_STRING);
        } else {
            $controller = 'Index';
        }
        $controller = ucfirst($controller) . ucfirst($this->_config['controllers']['postfix']);

        return $controller;
    }

    private function _getAction($class){

        if (array_key_exists('action', $this->_params)
            && false != ($action = trim($this->_params['action']))
        ){
            $action = filter_var($this->_params['action'], FILTER_SANITIZE_STRING);
        } else {
            $action = 'Index';
        }
        $action =  $action . ucfirst($this->_config['actions']['postfix']);

        if(!method_exists($class, $action)){
            header('HTTP/1.0 404 Not Found');
            throw new Exception('Action ' . $action . ' does not exists in class ' . get_class($class));
        }

        return $action;
    }
}
