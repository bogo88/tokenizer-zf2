<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories'=>array(
        'Application\Model\MyAuthStorage' => function($sm){
            return new \Application\Model\MyAuthStorage('zf_tutorial'); 
        },
         
        'AuthService' => function($sm) {
                    //My assumption, you've alredy set dbAdapter
                    //and has users table with columns : user_name and pass_word
                    //that password hashed with md5
//            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $config = array(
              'driver' => 'Mysqli',
              'database' => 'zend2',
              'username' => 'root',
              'password' => 'root'  
            );
            $dbAdapter = new \Zend\Db\Adapter\Adapter($config);
                    $dbTableAuthAdapter  = new \Zend\Authentication\Adapter\DbTable($dbAdapter,
                                              'users','user_name','pass_word', 'MD5(?)');
             
            $authService = new AuthenticationService();
            $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Application\Model\MyAuthStorage'));
              
            return $authService;
        },
            ),
        );
    }
}
