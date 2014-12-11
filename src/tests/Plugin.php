<?php
interface Plugin {

    public function info();
}

interface Core_Plugin extends Plugin {

    public function init();
}

interface Muffin extends Plugin {

    public function muffin_info(&$muffins = array());
}

interface Muffin_admin extends Plugin {

    public function admin_muffin_info(&$muffins = array());
}
class _Plugin {

    private $_name = null;
    private $_readable_name = null;
    private $_required_plugins = array();

    public function __construct($_name, $_readable_name) {
        $this->_name = $_name;
        $this->_readable_name = $_readable_name;
    }

    public function get_name() {
        return $this->_name;
    }

    public function get_readable_name() {
        return $this->_readable_name;
    }

    public function get_required_plugins() {
        return $this->_required_plugins;
    }

    public function set_name($_name) {
        $this->_name = $_name;
    }

    public function set_readable_name($_readable_name) {
        $this->_readable_name = $_readable_name;
    }

    public function add_required_plugins($_required_plugin) {
        $this->_required_plugins[] = $_required_plugin;
    }

}
