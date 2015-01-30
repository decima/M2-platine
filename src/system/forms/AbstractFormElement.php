<?php

abstract class AbstractFormElement {

    private $_name;
    private $_readable_name;

    public function __construct($_name, $_readable_name) {
        $this->_name = $_name;
        $this->_readable_name = $_readable_name;
    }

    public abstract function call_template();

}
