<?php

namespace WebAPI\Http;

use Zend\Http\Client as baseClient;
use WebAPI\SignatureGenerator;

class Client extends baseClient {

	/**
	 * @var string
	 */
	private $key;
	
	/**
	 * @var string
	 */
	private $keyName;

	/**
	 * @var string
	 */
	private $output;
	
	/**
	 * @var string
	 */
	private $version;
	
	/* (non-PHPdoc)
	 * @see \Zend\Http\Client::__construct()
	 */
	public function __construct($uri = null, $options = null) {
		$this->key = $options['key'];
		$this->keyName = $options['keyName'];
		$this->output = isset($options['output']) ? $options['output'] : '';
		$this->version = isset($options['version']) ? $options['version'] : '';
		parent::__construct($uri, $options);
	}

	/* (non-PHPdoc)
	 * @see \Zend\Http\Client::doRequest()
	 */
	protected function doRequest(\Zend\Uri\Http $uri, $method, $secure = false, $headers = array(), $body = '') {
		$headers['Date'] = gmdate('D, d M Y H:i:s') . ' GMT';
		$headers['User-Agent'] = isset($headers['User-Agent']) ? $headers['User-Agent'] : 'Zend_http_Client';
		
		$signature = $this->generateSignature($headers['Date'], $headers['User-Agent'], "{$uri->getHost()}:{$uri->getPort()}", $uri->getPath());

		$headers['X-Zend-Signature'] = "{$this->keyName};$signature";
		$headers['Accept'] = $this->getAcceptHeader($this->output, $this->version);
		return parent::doRequest($uri, $method, $secure, $headers, $body);
	}

	/**
	 * @param string $output
	 * @param string $version
	 * @return string
	 */
	private function getAcceptHeader($output, $version) {
		$output = $output ? "+{$output}" : '';
		$version = $version ? ";version={$version}" : '';
		return "application/vnd.zend.serverapi{$output}{$version}";
	}
	
	/**
	 * @param string $date
	 * @param string $useragent
	 * @param string $host
	 * @param string $path
	 * @return string
	 */
	private function generateSignature($date, $useragent, $host, $path) {
		$signature = new SignatureGenerator();
		$signature->setDate($date);
		$signature->setUserAgent($useragent);
		$signature->setHost($host);
		$signature->setRequestUri($path);
		return $signature->generate($this->key);
	}
}

