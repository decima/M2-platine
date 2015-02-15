<?php

class FormElement {

    private $balise = "div";
    private $id = "";
    private $name = "";
    private $label = null;
    private $value = "";
    private $attributes = array();
    private $classes = array();
    private $subelement = array();

    public function __construct($balise = "div", $id = null, $name = null, $label = null, $value = "") {
        $this->balise = $balise;
        $this->id = $id;
        $this->name = $name;
        if($id != null AND $name == null){
            $this->name = $id;
        }
        $this->label = $label;
        $this->value = $value;
    }

    function is_closed() {
        return false;
    }

    function getBalise() {
        return $this->balise;
    }

    function getId() {
        return $this->id;
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

    function setId($id) {
        $this->id = $id;
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
        $b = $this->attributes;
        $b["class"] = $this->getClasses();
        return ($b);
    }

}

class InputElement extends ClosedElement {

    public function __construct($id, $name, $label, $value, $type = "text") {
        parent::__construct("input", $id, $name, $label, $value);
        $this->setAttribute("type", $type);
    }

}

class ClosedElement extends FormElement {

    function is_closed() {
        return true;
    }

}


class Form extends FormElement {

    public function __construct($method = "POST", $action = "/404") {
        parent::__construct("form");
        $this->setAttribute("method", $method);
        $this->setAttribute("action", $action);
    }

}
