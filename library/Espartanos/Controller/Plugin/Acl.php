<?php

class Espartanos_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract{
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        
        $acl = new Espartanos_Model_AccessControl();
        
        //We need to append the module name to the resource that will be passed
        //to the Acl isAllowed method for authorization. Recall that this module
        //name serves as a namespace for the resource.
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        if($module != 'default'){
            $resource = $module . ":"  . $controller;
        }
        else{
            //Controllers in the default module do not include a preppended module
            //name
            $resource = $controller;
        }
        //Get the action name
        $action = $request->getActionName();
        
        /**
         * Now we need to verify whether a user has been authenticated. If not,
         * its role will be defaulted to "visitor", and so it will be redirected
         * to the login page. Note that there is no need of persisting the
         * "visitor" role value
        **/
        
        //Begin by getting the reference to the Zend_Auth object
        $auth = Zend_Auth::getInstance();
        
        //Check if the user has been identified
        if($auth->hasIdentity()){
            //Get the user identity
            $identity = $auth->getIdentity();
            //The login controller must have instructions to persist the username,
            //first name, last name and role attributes of the authenticated user
            //Here we are only interested in finding the user's role
            $role = $identity->role;
        }
        else{
            //The user has not been identified. Set the role to "visitor"
            $role = 'visitor';
        }
        
        /**
         * All of the authorization data has been set and stored. Now is time to
         * implement it by redirecting a user if he doesn't have the privileges
         * to access the requested resource
         */
        if(!$acl->isAllowed($role, $resource)){
            if($role == 'visitor')
            {
                
                $request->setModuleName('default');
                
                if($controller == 'logout')
                    $request->setControllerName('logout');
                else
                    $request->setControllerName('login');
                $request->setActionName('index');
            }
            else if($role == 'student'){
                $request->setModuleName('default');
                $request->setControllerName('logout');
                $request->setActionName('index');
            }
            else{
                $request->setModuleName('default');
                $request->setControllerName('error');
                $request->setActionName('error');
            }
        }
    }
}

?>
