<?php

interface module {
    
}

interface common_module {
    
}

interface system_module {

    public function priority();
    public function system_init();
}

/**
 * get_all_modules
 * @return array List of modules.
 */
function get_all_modules() {
    $array = array();
    $array = array_merge($array, get_all_of_interface("system_module"));
    $array = array_merge($array, get_all_of_interface("common_module"));
    $array = array_merge($array, get_all_of_interface("module"));
    return $array;
}

function get_all_of_interface($interface) {

    $array = array();
    $array = array_filter(
            get_declared_classes(),
            function($className) use ($interface) {
                return in_array($interface, class_implements($className)
                );
            }
    );
    return $array;
}


function invokable_modules($method) {
    $r = array();
    $modules = get_all_modules();
    foreach($modules as $module) {
        if(method_exists($module, $method)) {
            $r[] = $module;
        }
    }
    return $r;
}




/**
 * method_invoke_all
 * @param String $method the method to invoke
 * @param mixed args...(Optionnal)
 * @return array of mixed results. 
 */
function method_invoke_all($method) {
    $params = func_get_args();
    $r = array();
    $modules = get_all_modules();
    foreach($modules as $module) {
        $utils = array($module);
        $utils = array_merge($utils, $params);
        if(method_exists($module, $method)) {
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
function method_invoke($module, $method) {
    if(method_exists($module, $method)) {

        $params = func_get_args();
        $utils = array();
        for($i = 0; $i < count($params); $i++) {
            if($i > 1) {
                $utils[] = $params[$i];
            }
        }
        $b = new $module();
        return call_user_func_array(array($b,$method), $utils);
    }
}
