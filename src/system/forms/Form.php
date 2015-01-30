<?php

require_once 'FormElement.php';
require_once 'AbstractFormElement.php';

class Form {

    private $_action = "/404";
    private $_method = "POST";
    private $_attributes = array();
    private $_elements = array();

    public function __construct($_action, $_method) {
        $this->_action = $_action;
        $this->_method = $_method;
    }

    public function addElement(AbstractFormElement $feElement) {
        $this->_elements[] = $feElement;
    }

    public function removeElement($index) {
        
    }

}
