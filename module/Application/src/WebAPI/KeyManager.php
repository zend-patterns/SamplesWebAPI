<?php

namespace WebAPI;

use Zend\Session\Container;
class KeyManager {
	
	/**
	 * 
	 * @var Container
	 */
	private $container;
	
	public function __construct() {
		$this->container = new Container('webapi');
	}
	/**
	 * @return string
	 */
	public function getKeyName() {
		if ($this->hasKeyInfo()) {
			return $this->container->name;
		}
		return '';
	}
	
	/**
	 * @return string
	 */
	public function getKey() {
		if ($this->hasKeyInfo()) {
			return $this->container->key;
		}
		return '';
	}
	
	/**
	 * @param string $name
	 * @param string $key
	 */
	public function storeKey($name, $key) {
		$this->container->name = $name;
		$this->container->key = $key;
	}
	
	/**
	 * @return boolean
	 */
	public function hasKeyInfo() {
		return isset($this->container->name) && $this->container->name
			&& isset($this->container->key) && $this->container->key;
	}
	
	public function clearKey() {
		$this->storeKey('','');
	}
}