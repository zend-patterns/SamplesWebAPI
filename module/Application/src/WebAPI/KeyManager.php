<?php

namespace WebAPI;

class KeyManager {
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $key;
	/**
	 * @return string
	 */
	public function getKeyName() {
		if ($this->hasKeyInfo()) {
			return $this->name;
		}
		return '';
	}
	
	/**
	 * @return string
	 */
	public function getKey() {
		if ($this->hasKeyInfo()) {
			return $this->key;
		}
		return '';
	}
	
	/**
	 * @param string $name
	 * @param string $key
	 */
	public function storeKey($name, $key) {
		$this->name = $name;
		$this->key = $key;
	}
	
	/**
	 * @return boolean
	 */
	public function hasKeyInfo() {
		return $this->name && $this->key;
	}
	
	public function clearKey() {
		$this->name = null;
		$this->key = null;
	}
}