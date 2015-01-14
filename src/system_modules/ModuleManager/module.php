<?php

class ModuleManager implements SystemModule {

    public function info() {
        _Security::version(0, 1);
        return array(
            "name" => "ModuleManager",
            "readablename" => "Module Manager"
        );
    }

    public function init_system_module() {
        $system_modules = get_all_classes_implementing_interfaces("SystemModule");
        uasort($system_modules, function($a, $b) {
            $temp = new $a();
            $temp2 = new $b();
            return $temp->priority() > $temp2->priority();
        });
        array_map(function($a) {
            $r = new $a();
            $r->system_init();
        }, $system_modules);
    }

    public function system_init() {
        
    }

    public function priority() {
        return -99;
    }

    public function install_module($moduleName,$path) {
        /* to continue */
        method_invoke($moduleName, "schema");
    }

}

class Database implements SystemModule {

    const FIELD_TYPE_INT = 1;
    const FIELD_TYPE_FLOAT = 2;
    const FIELD_TYPE_STRING = 3;
    const FIELD_TYPE_DATE = 4;

    public static $connector = null;

    public function info() {
        _Security::version(0, 1);
        return array(
            "name" => "Database",
            "readablename" => "Database"
        );
    }

    public function priority() {
        return -100;
    }

    public function system_init() {
        if (self::$connector == null) {
            $servername = "localhost";
            $dbname = "jinn";
            $username = "jinn";
            $password = "jAdNK23AHMmupzWE";
            self::$connector = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        }
    }

    public static function execute($sql) {
        return self::$connector->execute($sql);
    }

    public static function getAll($sql) {
        return self::$connector->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public static function getRow($sql) {
        return self::$connector->query($sql)->fetch(PDO::FETCH_OBJ);
    }

    public static function getValue($sql) {
        return self::$connector->query($sql)->fetch(PDO::FETCH_COLUMN);
    }

}
