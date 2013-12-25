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
use WebAPI\Http\Client;
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
    	
    	$client = new Client($uri, array('key' => $keyManager->getKey(), 'keyName' => $keyManager->getKeyName()));
    	$response = $client->send();
    	
    	$request = $client->getRequest()->fromString($client->getLastRawRequest());
    	
    	$date = $request->getHeader('Date')->getFieldValue();
    	$agent = $request->getHeader('User-Agent')->getFieldValue();
    	$signatureHeader = $request->getHeader('X-Zend-Signature')->getFieldValue();
    	$accept = $request->getHeader('Accept')->getFieldValue();
    	
    	$signatureParts = explode(';', $signatureHeader);
    	$signed = $signatureParts[1];
    	$signatureParts[1] = substr($signatureParts[1], 0, 10) . '...' . substr($signatureParts[1], -10);
    	$signatureHeaderFormatted = "{$request->getHeader('X-Zend-Signature')->getFieldName()}: {$signatureParts[0]};{$signatureParts[1]}";
    	
    	
    	$method = new \ReflectionMethod('WebAPI\Http\Client', 'doRequest');
    	$filename = $method->getFileName();
    	$start_line = $method->getStartLine() -1;
    	$end_line = $method->getEndLine();
    	$length = $end_line - $start_line;
    	
    	$source = file($filename);
    	$doRequestBody = implode("", array_slice($source, $start_line, $length));
    	
    	
    	$method = new \ReflectionMethod('WebAPI\Http\Client', 'generateSignature');
    	$filename = $method->getFileName();
    	$start_line = $method->getStartLine() -1;
    	$end_line = $method->getEndLine();
    	$length = $end_line - $start_line;
    	
    	$source = file($filename);
    	$generateSignatureBody = implode("", array_slice($source, $start_line, $length));
    	
    	$config = array(
    			'indent'         => true,
    			'output-xml'     => true,
    			'input-xml'     => true,
    			'wrap'         => '1000');
    	
    	$tidy = new \tidy();
    	$tidy->parseString($response->getBody(), $config, 'utf8');
    	$tidy->cleanRepair();
    	
    	return new ViewModel(array(
    			'webapiResponse' => tidy_get_output($tidy), 
    			'source' => "$doRequestBody\n$generateSignatureBody",
    			'keyname' => $keyManager->getKeyName(),
    			'key' => substr($keyManager->getKey(), 0, 5) . '...' . substr($keyManager->getKey(), -5),
    			'finalSignature' => $signed,
    			'shortSignature' => substr($signed, 0, 10) . '...' . substr($signed, -10),
    			'uri' => $uri,
    			'date' => $date,
    			'useragent' => $agent,
    			'signatureHeaderFormatted' => $signatureHeaderFormatted
    	));
    }
}
