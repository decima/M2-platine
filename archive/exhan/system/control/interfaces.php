<?php
interface m_constants {

    public function constants();
}

interface m_database {

    public function schema();
}

interface m_install {
    
}

interface m_install_strict {

    public function enable();
    public function disable();
    public function install();
    public function uninstall();
}

interface m_update {

    public function update($update_id);
}

interface m_access {

    public function perms();
}

interface extend_permissions {

    public function check_permission($page,$page_data);
}

interface m_page {

    public function menu();
}

interface theme_template {

    public static function theme_form($array);
    public static function theme_list($array);
    public static function theme_table($array, $head_row = array());
    public static function error_404();
    public static function error_403();
    public static function error_notfound();
    public static function header_menu($args);
}