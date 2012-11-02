<?php
/**
 *
 * @author Iuli Dercaci <luli.dercaci@site-me.info>
 * @date 11/1/12
 * @time 11:09 PM
 */
class Database
{
    private $_config;
    private $_db = false;

    public function __construct($config = array()){
        $this->_config = (!empty($config)) ? $config : $_SESSION['config']['db'];
    }

    /**
     * getting db instance
     * @param $key
     * @return bool|PDO
     */
    public function __get($key){
        if ($key == 'db'){
            return $this->_db ? $this->_db : $this->_getDb();
        }
    }

    /**
     * PDO instance
     * @return PDO
     */
    private function _getDb(){

        $user   = $this->_config['username'];
        $pass   = $this->_config['password'];
        $params = sprintf('mysql:host=%s;dbname=%s', $this->_config['host'], $this->_config['database']);

        $db = new PDO($params, $user, $pass);
        $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        return $db;
    }
}
