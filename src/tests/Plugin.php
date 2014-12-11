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
