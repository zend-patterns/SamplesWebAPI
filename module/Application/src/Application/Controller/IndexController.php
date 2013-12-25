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
use Zend\Session\Container;
use WebAPI\KeyManager;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    	$webapiKey = new KeyManager();
        return new ViewModel(array(
        	'hasKey' => $webapiKey->hasKeyInfo(),
        	'keyName' => $webapiKey->getKeyName(),
        	'shortkey' => substr($webapiKey->getKey(), 0, 5) . '...' . substr($webapiKey->getKey(), -5)
        ));
    }
}
