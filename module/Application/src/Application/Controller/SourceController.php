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

class SourceController extends AbstractActionController
{
	public function sourceAction() {
		$routeMatch = $this->getEvent()->getRouteMatch();
		$workflow = $routeMatch->getParam('workflow');
		$topic = $routeMatch->getParam('topic');
		
		$config = $this->getServiceLocator()->get('Configuration');
		$reflections = array();
		if (isset($config['sources'][$workflow]) && isset($config['sources'][$workflow][$topic])) {
			$reflections = $config['sources'][$workflow][$topic];
		}
		
		$sources = array();
		
		if (isset($reflections['classes'])) {
			foreach ($reflections['classes'] as $class) {
				$reflector = new \ReflectionClass($class);
				$sources[] = array(
						'filepath' => $reflector->getFileName(),
						'body' => file_get_contents($reflector->getFileName())
				);
			}
		}
		
		return array('source' => $sources);
	}
	
    public function indexAction()
    {
    	return $this->forward()->dispatch('Application\Controller\Source', array('action' => 'source'));
    }
}
