<?php

require_once "Plugin.php";

require_once 'Database.php';

class Hooks {

    const RETURN_ARRAY_NEXT = "next";
    const RETURN_MERGE = "merge";
    const RETURN_ADDITION = "addition";
    const RETURN_MULTIPLY = "multiply";
    const PLUGINS = "Plugin";
    const CORE_PLUGINS = "Core_Plugin";
    const MUFFINS = "Muffin";
    const MUFFINS_ADMIN = "Muffin_admin";

    private static $_plugins = array();

    /**
     *
     * @var String the name of the hook needed to be called
     */
    protected $_hook_name = null;

    /**
     *
     * @var String the type of the plugin
     */
    protected $_plugin_type = self::PLUGINS;

    /**
     * 
     * @param String the name of the hook needed
     */
    public function __construct($hook_name) {
        $this->_hook_name = $hook_name;
    }

    /**
     * Permit to change the plugin type.
     * @param String $plugin_type change the plugin type (Plugin/Core_Plugin/...)
     */
    public function change_plugin_type($plugin_type = self::PLUGINS) {
        $this->_plugin_type = $plugin_type;
    }

    /**
     * Permit to reset the plugin type
     */
    public function reset_plugin_type() {
        $this->_plugin_type = self::PLUGINS;
    }

    /**
     * get all declared plugins
     * @return array Array of plugins
     */
    protected function get_all_plugins() {
        if (isset(self::$_plugins[$this->_plugin_type]) && is_array(self::$_plugins[$this->_plugin_type])) {
            return self::$_plugins[$this->_plugin_type];
        } else {
            $classes = get_declared_classes();
            $implementsPlugin = array();
            foreach ($classes as $c) {
                $reflect = new ReflectionClass($c);
                if ($reflect->implementsInterface($this->_plugin_type)) {
                    $implementsPlugin[] = $c;
                }
            }
            self::$_plugins[$this->_plugin_type] = $implementsPlugin;
            return $implementsPlugin;
        }
    }

    /**
     * 
     * @return Array Array of modules which had delcared the hook
     */
    protected function get_all_hooks_declaration() {
        $classes = $this->get_all_plugins();
        $o = array();
        foreach ($classes as $c) {
            if (method_exists($c, $this->_hook_name)) {
                $o[] = $c;
            }
        }
        return $o;
    }

    /**
     * 
     * @param String $module_name the name of the module where to search for the hook.
     * @return mixed the result of the invocation
     */
    public function invoke_hook($module_name, $args = array()) {
        $classes = $this->get_all_hooks_declaration();
        if (in_array($module_name, $classes)) {
            $reflectionMethod = new ReflectionMethod($module_name, $this->_hook_name);
            return $reflectionMethod->invokeArgs(new $module_name, $args);
        }
    }

    /**
     * 
     * @return mixed array the results of the multiples invocations
     */
    public function invoke_hooks($args = array(), $merge = self::RETURN_ARRAY_NEXT) {
        $classes = $this->get_all_hooks_declaration();
        $ret = array();
        foreach ($classes as $c) {
            $tmp = new $c();
            $ret[] = call_user_func_array(array(&$tmp, $this->_hook_name), $args);
        }
        if ($merge == self::RETURN_ARRAY_NEXT) {
            $res = $ret;
        } elseif ($merge == self::RETURN_MERGE) {
            $res = call_user_func_array("array_merge", $ret);
        } elseif ($merge == self::RETURN_ADDITION) {
            $res = 0;
            foreach ($ret as $r) {
                $res+=$r;
            }
        } elseif ($merge == self::RETURN_MULTIPLY) {
            $res = 1;
            foreach ($ret as $r) {
                $res*=$r;
            }
        }
        return $res;
    }

}
