<?php 

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $_acl = null;
    protected $_auth = null;    
         
    
    protected function _initAutoload(){
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace("Espartanos_");
        $autoloader->registerNamespace("ZendX_");
        
        $this->_acl = new Espartanos_Model_AccessControl();
        $this->_auth = Zend_Auth::getInstance();
    }
    
    protected function __initControllerPlugins(){
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new Espartanos_Controller_Plugin());
    }
    
    protected function _initView(){
        
        $navConfig = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $navigation = new Zend_Navigation($navConfig);
        
        $acl = $this->_acl;
        $auth = Zend_Auth::getInstance();//$this->_auth;
        
        if($auth->hasIdentity()){
            $role = $auth->getStorage()->read()->role;
        }
        else{
            $role = 'visitor';
        }
        
        $view = new Zend_View();
        
        $view->navigation($navigation)->setAcl($acl)->setRole($role);
        
        
        
        /**
         * Since only view helpers that are inside the accessed module are loaded
         * into the stack, we need to add a path to those that are in the 
         * default view helpers' directory so that the Zend autoleader can
         * find them
         */
        $view->addHelperPath("../application/views/helpers");
        
        
        //This defines the document type declaration
        $view->doctype('HTML5');
        
        //This is the title of the website
        $view->headTitle('Espartanos');
        
        
        //Let's store all the stylesheets into an array
        $stylesheets = array(
            '/css/style.css'
            );
        
        //Now we pass each stylesheet to the view throug the 
        //headLink helper function
        foreach($stylesheets as $stylesheet){
            $view->headLink()->appendStylesheet($stylesheet);
        }
        
        //This sets a headScript file to be passed to the view. It contains
        //all the javascript functions to provide the application with its
        //front-end basic functionality
        $view->headScript()->appendFile("/js/mylibs/jquery-1.6.2.min.js");
        $view->headScript()->appendFile("/js/mylibs/jquery-ui-1.8.15.custom.min.js");
        
        
        // Helper path for the jQuery View Helper
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");     
        $view->jQuery()->setLocalPath('/js/mylibs/jquery-1.6.2.min.js')
                ->setUiLocalPath('/js/mylibs/jquery-ui-1.8.15.custom.min.js');
        $view->jQuery()->enable()->uiEnable();      
        
        
        
        
        //We have to make sure the view is rendered
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        
        //Return the view object so that it can be stored by the bootstrap
        return $view;
    }
    

}


?>