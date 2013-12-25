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
use Zend\Http\Client;
use Zend\Session\Container;
use Zend\Uri\UriFactory;
use WebAPI\KeyManager;

class BasicsController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->forward()->dispatch('Application\Controller\Basics', array('action' => 'signature'));
    }
    
    public function signatureAction() {
    	$keyManager = new KeyManager();
    	
    	$uri = UriFactory::factory('http://localhost:10081/ZendServer/Api/getSystemInfo');
    	
    	$date = gmdate('D, d M Y H:i:s') . ' GMT';
    	$agent = 'Zend_Http_Client';
    	
    	$signature = new SignatureGenerator();
    	$signature->setDate($date);
    	$signature->setHost($uri->getHost());
    	$signature->setRequestUri($uri->getPath());
    	$signature->setUserAgent($agent);
    	$signed = $signature->generate($keyManager->getKey());
    	
    	$client = new Client();
    	$client->setHeaders(array(
    		'Date' => $date,
    		'User-Agent' => $agent,
    		'Accept' => 'application/vnd.zend.serverapi+xml',
    		'X-Zend-Signature' => "{$keyManager->getKeyName()}:{$signed}"
    	));
    	$client->setUri($uri);
    	
    	$response = $client->send();
    	
    	
    	$method = new \ReflectionMethod('Application\Controller\BasicsController', 'signatureAction');
    	$filename = $method->getFileName();
    	$start_line = $method->getStartLine();
    	$end_line = $method->getEndLine() -1;
    	$length = $end_line - $start_line;
    	
    	$source = file($filename);
    	$methodbody = implode("", array_slice($source, $start_line, $length));
    	
    	return new ViewModel(array(
    			'webapiResponse' => $response->getBody(), 
    			'source' => $methodbody,
    			'keyname' => $keyManager->getKeyName(),
    			'key' => substr($keyManager->getKey(), 0, 5) . '...' . substr($keyManager->getKey(), -5),
    			'finalSignature' => $signed,
    			'shortSignature' => substr($signed, 0, 10) . '...' . substr($signed, -10),
    			'uri' => $uri,
    			'date' => $date,
    			'useragent' => $agent
    	));
    }
}
