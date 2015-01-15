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
        return 0;
    }

    public static function get_list_of_modules() {
        return method_invoke_all("info");
    }

    public function install_module($moduleName, $path) {
        require_once($path);
        $schema = method_invoke($moduleName, "schema");
        Database::schema_installer($schema);
    }

    public function uninstall_module($moduleName, $path) {
        require_once($path);
        $schema = method_invoke($moduleName, "schema");
        Database::schema_installer($schema);
    }

    public function enable_module($moduleName, $path) {
        $exists = method_invoke($moduleName, "info");
        if ($exists == NULL) {
            $rust = file_get_contents("./cache/classes.php");
            $rust .= "// @module:$moduleName\nrequire_once('$path');\n";
            file_put_contents("./cache/classes.php", $rust);
        }
    }

    public function disable_module($moduleName) {
        $rust = file_get_contents("./cache/classes.php");

        $file = explode("//", $rust);

        foreach ($file as $row_id => $row) {
            if (strstr($row, "@module:$moduleName")) {
                unset($file[$row_id]);
            }
        }
        $rust = implode("//", $file);
        file_put_contents("./cache/classes.php", $rust);
    }

    public function menu($item = array()) {
        $item['admin/modules'] = array(
        );
        $item['admin/modules/install/@'] = array(
            "callback"=>array("ModuleManager","makemedream")
        );
        $item['admin/modules/enable/@'] = array(
        );
        $item['admin/modules/disable/@'] = array(
        );
        $item['admin/modules/uninstall/@'] = array(
        );
        return $item;
    }


}
