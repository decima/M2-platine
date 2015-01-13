<?php

function get_all_classes_implementing_interfaces($interface) {

    $array = array();
    $array = array_filter(
            get_declared_classes(), function($className) use ($interface) {
        return in_array($interface, class_implements($className)
        );
    }
    );
    
    return $array;
}

function get_all_modules($interface="Module") {
    return (get_all_classes_implementing_interfaces($interface));
}

/**
 * method_invoke_all
 * @param String $method the method to invoke
 * @param mixed args...(Optionnal)
 * @return array of mixed results. 
 */
function method_invoke_all($method, $parameters = array(),$interface="Module") {
    $r = array();
    $modules = get_all_modules($interface);
    foreach ($modules as $module) {
        $utils = array($module, $method, $parameters);
        if (method_exists($module, $method)) {
            $r[] = call_user_func_array("method_invoke", $utils);
        }
    }
    return $r;
}

/**
 * method_invoke
 * @param string $module
 * @param string $method
 * @param mixed args...(Optionnal)
 * @return mixed results.
 */
function method_invoke($module, $method, $utils = array()) {
    if (method_exists($module, $method)) {
        $b = new $module();
        return call_user_func_array(array($b, $method), $utils);
    }
}

interface Module {
    public function info();
}

interface SystemModule extends Module {
    public function priority();
    public function system_init();
}
