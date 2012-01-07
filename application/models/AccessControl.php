<?php

class Espartanos_Model_AccessControl extends Zend_Acl{
    
    public function __construct() {
        
        $this->addRole(new Zend_Acl_Role('visitor'))
             ->addRole(new Zend_Acl_Role('student'), 'visitor')
             ->addRole(new Zend_Acl_Role('admin'));
        
        $this->addResource(new Zend_Acl_Resource('index'));
        $this->addResource(new Zend_Acl_Resource('login'));
        $this->addResource(new Zend_Acl_Resource('logout'));
        
        $this->allow('visitor', 'login');
        $this->allow('visitor', 'logout');
        
        $this->allow('student', 'index');
        
        
        $this->allow('admin', null);
        
    }
}

?>
