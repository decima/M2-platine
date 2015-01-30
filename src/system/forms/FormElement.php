<?php

class FormElement {

    public static function create($formElement, $name, $readableName) {
        if (class_exists("FE_" . $formElement)) {
            $e = "FE_" . $formElement;
            return new $e($name, $readableName);
        } else {
            $e = "FE_text";
            return new $e($name, $readableName);
        }
    }

}

class FE_Text extends AbstractFormElement{
    
}
