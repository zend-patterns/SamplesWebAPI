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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function storekeyAction() {
    	$user = $this->getRequest()->getPost('webapiuser');
    	$key = $this->getRequest()->getPost('webapikey');
    	
    	$session = new Container('webapiKey');
    	$session->name = $user;
    	$session->key = $key;
    	
    	return $this->redirect()->toRoute('home');
    }
}
