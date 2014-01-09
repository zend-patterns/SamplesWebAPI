<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
	'navigation' => array(
		'default' => array(
			'home' => array(
				'label' => 'Home',
				'route' => 'home',
			),
			'sdk' => array(
				'label' => 'Web API Connector',
				'route' => 'sdk',
			),
		)
	),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'sdk' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/sdk',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sdk',
                    ),
                ),
            ),
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
	                'default' => array(
	                		'type'    => 'Segment',
	                		'options' => array(
	                				'route'    => '/[:controller[/:action]]',
	                				'constraints' => array(
	                						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
	                						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
	                				),
	                				'defaults' => array(
	                				),
	                		),
	                ),
                    'source' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/ShowSource/:workflow/:topic',
                            'constraints' => array(
                                'workflow' => '[a-zA-Z]+',
                                'topic'     => '[a-zA-Z]+',
                            ),
                            'defaults' => array(
                            	'__NAMESPACE__' => 'Application\Controller',
                            	'controller' => 'Source',
                            	'action' => 'source',
                            	'workflow' => '',
                            	'topic' => ''
                            ),
                        ),
                    ),
                    
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
	        'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Basics' => 'Application\Controller\BasicsController',
            'Application\Controller\Source' => 'Application\Controller\SourceController',
            'Application\Controller\Simple' => 'Application\Controller\SimpleController',
            'Application\Controller\Advanced' => 'Application\Controller\AdvancedController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'sources' => array(
        'Basics' => array(
            'Signature' => array(
            	'classes' => array(
			    	'WebAPI\SignatureGenerator',
			    )
            ),
            'VersionNegotiation' => array(
	            'classes' => array(
            		'WebAPI\Http\Client',
            		'WebAPI\SignatureGenerator',
	            ),
	            'public' => array(
	            	'examples/version.php'
	            )
            ),
        ),
        'Simple' => array(
        	'ListApplications' => array(
	        	'classes' => array(
            		'WebAPI\Http\Client',
            		'WebAPI\SignatureGenerator',
	            ),
	            'public' => array(
	            	'examples/applicationGetStatus.php'
	            )
	        ),
        	'EventInfo' => array(
	        	'classes' => array(
            		'WebAPI\Http\Client',
            		'WebAPI\SignatureGenerator',
	            ),
	            'public' => array(
	            	'examples/monitorGetEvents.php'
	            )
	        ),
        	'LogContent' => array(
	        	'classes' => array(
            		'WebAPI\Http\Client',
            		'WebAPI\SignatureGenerator',
	            ),
	            'public' => array(
	            	'examples/logsReadLines.php'
	            )
	        ),
        ),
        'Advanced' => array(
        	'tasksPolling' => array(
	        	'classes' => array(
            		'WebAPI\Http\Client',
            		'WebAPI\SignatureGenerator',
	            ),
	            'public' => array(
	            	'examples/tasksComplete.php'
	            )
	        ),
        	'DeployApplication' => array(
	        	'classes' => array(
            		'WebAPI\Http\Client',
            		'WebAPI\SignatureGenerator',
	            ),
	            'public' => array(
	            	'examples/deployApplication.php'
	            )
	        ),
        	'ChangeDirective' => array(
	        	'classes' => array(
            		'WebAPI\Http\Client',
            		'WebAPI\SignatureGenerator',
	            ),
	            'public' => array(
	            	'examples/changeDirective.php'
	            )
	        ),
        ),
    ),
);
