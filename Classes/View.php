<?php
/**
 * managing view
 * @author Iuli Dercaci <luli.dercaci@site-me.info>
 * @date 10/31/12
 * @time 12:28 AM
 */

class View
{
    private $_params = false;
    private $_viewsPath;
    private $_layoutPath;
    private $_config;
    private $_helperPath;

    public function __construct($config = false) {
        $this->_config     = $config ? $config : $_SESSION['config'];
        $this->_viewsPath  = $this->_config['paths']['root'] . $this->_config['paths']['views'];
        $this->_layoutPath = $this->_config['paths']['root'] . $this->_config['paths']['layouts'];
        $this->_helperPath = $this->_config['paths']['root'] . $this->_config['paths']['helpers'] . '/View';
    }

    /**
     * @param array $params
     * @return View
     */
    public function setParams(array $params){
        $this->_params = $params;
        return $this;
    }

    /**
     * params accessor
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public function getParam($key){
        if(!is_array($this->_params)){
            throw new Exception('Params are not set yet');
        }
        if(array_key_exists($key, $this->_params)){
            return $this->_params[$key];
        }
        return false;
    }

    /**
     * getting view helpers output
     * @param $name
     * @return string
     * @throws Exception
     */
    public function helper($name){
        $path = sprintf('%s/%s.phtml', $this->_helperPath, $name);
        if(!is_file($path)){
            throw new Exception('view helper was not found in the path: ', $path);
        }
        return file_get_contents($path);
    }

    /**
     * render template with the data
     * @param bool $data
     */
    public function render($data = false){
        $data = empty($data) ? array() : $data;
        echo $this->_getContent($data);
    }

    /**
     * getting the view script file path
     * @param bool $controller
     * @param bool $action
     * @return string
     * @throws Exception
     */
    private function _getTemplate($controller = false, $action = false){

        $controller = $controller ? $controller : $this->getParam('controller');
        $action     = $action     ? $action     : $this->getParam('action');

        $path = $this->_viewsPath . '/' . $controller . '/' . strtolower($action) . '.phtml';
        if (!file_exists($path)){
            throw new Exception('view script was not found at: ' . $path);
        }
        return $path;
    }

    /**
     * getting the view script file path
     * @return string
     * @throws Exception
     */
    private function _getLayout(){
        if (false == ($name = $this->getParam('layout'))){
            $name = 'default';
        }
        $path = sprintf('%s/%s.phtml', $this->_layoutPath, strtolower($name));
        if (!file_exists($path)){
            throw new Exception('layout was not found at: ' . $path);
        }
        return $path;
    }

    /**
     * getting buffered content
     * @param array $data
     * @return string
     */
    private function _getContent(array $data){
        extract($data);
        ob_start();
        include $this->_getTemplate($this->getParam('controller'), $this->getParam('action'));
        $content = ob_get_clean();
        ob_start();
        include $this->_getLayout();

        return ob_get_clean();
    }

}
