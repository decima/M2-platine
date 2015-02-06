<?php

class FormElement {

    private $balise = "div";
    private $name = "";
    private $label = null;
    private $value = "";
    private $attributes = array();
    private $classes = array();
    private $subelement = array();

    public function __construct($balise = "div", $name = "", $label = null, $value = "") {
        $this->balise = $balise;
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
    }
    function getBalise() {
        return $this->balise;
    }

    function getName() {
        return $this->name;
    }

    function getLabel() {
        return $this->label;
    }

    function getValue() {
        return $this->value;
    }

    function setBalise($balise) {
        $this->balise = $balise;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setLabel($label) {
        $this->label = $label;
    }

    function setValue($value) {
        $this->value = $value;
    }

        public function setAttribute($attribute, $value) {
        $this->attributes[$attribute] = $value;
    }

    public function addClasses($classname) {
        $this->classes[] = $classname;
    }

    public function addElement(FormElement $element) {
        $this->subelement[] = $element;
    }

    public function getElements() {
        return $this->subelement;
    }

    public function getClasses() {
        return implode(" ", $this->classes);
    }

    public function getAttributes() {
        return array_merge($this->attributes, array("class" => $this->classes));
    }

}

class Form extends FormElement {

    public function __construct($method = "POST", $action = "/404") {
        parent::__construct("form");
        $this->setAttribute("method", $method);
        $this->setAttribute("action", $action);
    }
    
    

}
