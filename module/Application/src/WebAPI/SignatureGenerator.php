<?php

namespace WebAPI;

class SignatureGenerator {

	/**
	 * @var string
	 */
	private $date 		= '';
	
	/**
	 * @var string
	 */
	private $userAgent  = '';
	
	/**
	 * @var string
	 */
	private $host 		= '';
	
	/**
	 * @var string
	 */
	private $requestUri	= '';
	
	/**
	 * @return string
	 */
	public function getRequestUri() {
		return $this->requestUri;
	}

	/**
	 * @return string
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @return string
	 */
	public function getUserAgent() {
		return $this->userAgent;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * @param string $requestUri
	 * @return SignatureGenerator
	 */
	public function setRequestUri($requestUri) {
		$this->requestUri = $requestUri;
		return $this;
	}
	
	/**
	 * @param string $date
	 * @return SignatureGenerator
	 */
	public function setDate($date) {
		$this->date = $date;
		return $this;
	}

	/**
	 * @param string $userAgent
	 * @return SignatureGenerator
	 */
	public function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
		return $this;
	}

	/**
	 * @param string $host
	 * @return SignatureGenerator
	 */
	public function setHost($host) {
		$this->host = $host;
		return $this;
	}

	/**
	 * @param string $seed
	 * @return string
	 */
	public function generate($seed) {
		$concatString = $this->host . ':' . $this->requestUri . ':' . $this->userAgent . ':' . $this->date;
		return hash_hmac('sha256', $concatString, $seed);
	}
}