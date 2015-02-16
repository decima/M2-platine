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

    public function __construct($balise = "div", $name = null, $label = null, $value = "", $id = "") {
        $this->balise = $balise;
        $this->name = $name;
        $this->label = $label;
        $this->value = $value;
        $this->id = $id;
    }

    function is_closed() {
        return false;
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

    function getId() {
        return $this->id;
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

    function setId($id) {
        $this->id = $id;
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

    public function __construct($name, $label, $value, $type = "text", $id = "") {
        parent::__construct("input", $name, $label, $value, $id);
        $this->setAttribute("type", $type);
    }

}

class ClosedElement extends FormElement {

    function is_closed() {
        return true;
    }

}


class Form extends FormElement {

    public function __construct($method = "POST", $action = "/404", $name = "") {
        parent::__construct("form", $name);
        $this->setAttribute("method", $method);
        $this->setAttribute("action", $action);
    }

}
