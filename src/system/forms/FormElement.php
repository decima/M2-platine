<?php

class FormElement {
    public static function create($formElement, $name, $readableName) {
        if (class_exists($formElement)) {
            return new $formElement($name, $readableName);
        }
    }

}
