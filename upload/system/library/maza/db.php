<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */
namespace maza;

/**
* DB class
*/
class DB{
	private $adaptor;
        private $hostname;
        private $username;
        private $password;
        private $database;
        private $port;

	/**
	 * Constructor
	 *
	 * @param string $adaptor
	 * @param string $hostname
	 * @param string $username
         * @param string $password
	 * @param string $database
	 * @param int	 $port
	 *
 	*/
	public function __construct($adaptor, $hostname, $username, $password, $database, $port = NULL) {
		$class = 'maza\\db\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($hostname, $username, $password, $database, $port);
		} else {
			throw new \Exception('Error: Could not load database adaptor ' . $adaptor . '!');
		}
                
                $this->hostname = $hostname;
                $this->username = $username;
                $this->password = $password;
                $this->database = $database;
                $this->port = $port;
	}

	/**
        * @param string	$sql
        * @param boolean $use_result
        * @return class
        */
	public function query($sql, $use_result = false) {
		return $this->adaptor->query($sql, $use_result);
	}
        
        
	/**
        * @param string	$value
        * @return string
        */
	public function escape($value) {
		return $this->adaptor->escape($value);
	}

	/**
        * @return	int
        */
	public function countAffected() {
		return $this->adaptor->countAffected();
	}

	/**
        * @return	int
        */
	public function getLastId() {
		return $this->adaptor->getLastId();
	}
	
	/**
        * @return	bool
        */	
	public function connected() {
		return $this->adaptor->connected();
	}
        
        /**
         * @return DB New connection
         */
        public function openDB(){
            return new DB($this->adaptor, $this->hostname, $this->username, $this->password, $this->database, $this->port);
        }
}