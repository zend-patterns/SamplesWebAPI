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
    public function indexAction()
    {
        return $this->forward()->dispatch('Application\Controller\Basics', array('action' => 'signature'));
    }
    
    public function signatureAction() {
    	$uri = UriFactory::factory('http://localhost:10081/ZendServer/Api/getSystemInfo');

    	$method = new \ReflectionMethod('Application\Controller\BasicsController', 'sampleSignature');
    	$filename = $method->getFileName();
    	$start_line = $method->getStartLine() -6;
    	$end_line = $method->getEndLine();
    	$length = $end_line - $start_line;
    	
    	$source = file($filename);
    	$generateSignatureBody = implode("", array_slice($source, $start_line, $length));
    	
    	$method = new \ReflectionMethod('Application\Controller\BasicsController', 'sampleSigHeader');
    	$filename = $method->getFileName();
    	$start_line = $method->getStartLine() -6;
    	$end_line = $method->getEndLine();
    	$length = $end_line - $start_line;
    	
    	$source = file($filename);
    	$generateSignatureBody .= "\n".implode("", array_slice($source, $start_line, $length));
    	
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
}
