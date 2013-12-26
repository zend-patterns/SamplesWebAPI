<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use WebAPI\SignatureGenerator;
use Zend\Uri\UriFactory;
use Zend\Uri\Http;

class BasicsController extends AbstractActionController
{
	public function sourceAction() {
		$reflector = new \ReflectionClass('WebAPI\SignatureGenerator');
		return array(
				'filepath' => $reflector->getFileName(),
				'body' => "namespace {$reflector->getNamespaceName()};\n{$this->reflectClassBody('WebAPI\SignatureGenerator')}");
	}
	
	
	public function versionNegotiationAction() {
		$getAcceptHeader = $this->reflectMethodBody('WebAPI\Http\Client', 'getAcceptHeader', 0, -6);
		return array('getAcceptHeader' => $getAcceptHeader);
	}
	
    public function signatureAction() {
    	$uri = UriFactory::factory('http://localhost:10081/ZendServer/Api/getSystemInfo');

    	$generateSignatureBody = "{$this->reflectMethodBody('Application\Controller\BasicsController', 'sampleSignature', 0, -6)}\n{$this->reflectMethodBody('Application\Controller\BasicsController', 'sampleSigHeader', 0, -6)}";
    	
    	$key = md5(rand(0, 10));
    	
    	$signed = $this->sampleSignature($uri, $key);
    	$shortSig = substr($signed, 0, 10) . '...' . substr($signed, -10);
    	$user = 'my-key-name';
    	
    	return new ViewModel(array(
    			'source' => "$generateSignatureBody",
    			'keyname' => $user,
    			'key' => $key,
    			'finalSignature' => $signed,
    			'shortSignature' => $shortSig,
    			'uri' => $uri,
    			'date' => gmdate('D, d M Y H:i:s') . ' GMT',
    			'useragent' => 'Zend\Http\Client',
    			'signatureHeaderFormatted' => $this->sampleSigHeader($user, $shortSig)
    	));
    }
    
    public function indexAction()
    {
    	return $this->forward()->dispatch('Application\Controller\Basics', array('action' => 'signature'));
    }
    
    /**
     * @param Http $uri
     * @param string $key
     * @return string
     */
    private function sampleSignature(Http $uri, $key) {

    	$sigGen = new SignatureGenerator();
    	$sigGen->setDate(gmdate('D, d M Y H:i:s') . ' GMT');
    	$sigGen->setUserAgent('Zend\Http\Client');
    	$sigGen->setRequestUri("{$uri->getHost()}:{$uri->getPort()}");
    	$sigGen->setRequestUri($uri->getPath());
    	return $sigGen->generate($key);
    }
    
    /**
     * @param string $user
     * @param string $signature
     * @return string
     */
    private function sampleSigHeader($user, $signature) {
    	return "X-Zend-Signature: {$user}:{$signature}";
    }
    
	/**
	 * @param string $class
	 * @param string $method
	 * @param number $suffixOffset
	 * @param number $prefixOffset
	 * @return string
	 */
	private function reflectClassBody($class, $suffixOffset = 0, $prefixOffset = -1) {
		$class = new \ReflectionClass($class);
		return $this->reflectOutput($class, $suffixOffset, $prefixOffset);
	}
    
	/**
	 * @param string $class
	 * @param string $method
	 * @param number $suffixOffset
	 * @param number $prefixOffset
	 * @return string
	 */
	private function reflectMethodBody($class, $method, $suffixOffset = 0, $prefixOffset = -1) {
		$method = new \ReflectionMethod($class, $method);
		return $this->reflectOutput($method, $suffixOffset, $prefixOffset);
	}
	
	private function reflectOutput($reflector, $suffixOffset, $prefixOffset) {
		$filename = $reflector->getFileName();
		$start_line = $reflector->getStartLine() + $prefixOffset;
		$end_line = $reflector->getEndLine() + $suffixOffset;
		$length = $end_line - $start_line;
			
		$source = file($filename);
		return implode("", array_slice($source, $start_line, $length));
	}
}
