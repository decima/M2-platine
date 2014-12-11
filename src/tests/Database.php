<?php

class Database implements Core_Plugin, Muffin_admin {

    public function info() {
        return new _Plugin("Database", "Database");
    }

    public function init() {
        
    }

    public function admin_muffin_info(&$muffins = array()) {
        if (!isset($muffins["Database"])) {
            $muffins["Database"] = array(
                "name" => "Database",
                "sections" => array(
                    "/admin/database/setting" => "Settings",
                    "/admin/database/backup" => "Backup",
                )
            );
            return $muffins;
        }
    }

}
