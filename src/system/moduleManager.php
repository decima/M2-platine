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
                        "callback" => array("ModuleManager", "list_modules")

        );
        $item['admin/modules/install/@'] = array(
        );
        $item['admin/modules/enable/@'] = array(
        );
        $item['admin/modules/disable/@'] = array(
        );
        $item['admin/modules/uninstall/@'] = array(
        );
        return $item;
    }
    public static function list_modules(){
        
        $modules = self::scan_all_modules();
        $theme = new Theme();
        $r = array_keys(end($modules));
       $theme->add_to_body($theme->tabling($modules,$r));
       $theme->process_theme();
       return;
    }
    public static function scan_all_modules() {
        $Directory = new RecursiveDirectoryIterator('./modules');
        $Iterator = new RecursiveIteratorIterator($Directory);
        $regex = new RegexIterator($Iterator, '/^.+\module.php$/i', RecursiveRegexIterator::GET_MATCH);
        $systemModules = get_all_classes_implementing_interfaces("SystemModule");
        $res = array();
        $enabled = method_invoke_all("info");
        $modules = array();


        $available = array();

        foreach ($enabled as $k => $e) {
            $modules[$e['name']] = $e;
                        $modules[$e['name']]["path"] = "";

            $modules[$e['name']]["system_module"] = in_array($e["name"], $systemModules) ? "1" : "0";
            $modules[$e['name']]["enabled"] = 1;

        }

        foreach ($regex as $item) {
            $data = file_get_contents($item[0]);
            if (preg_match("/@moduleName ([A-Za-z0-9_]+)/", $data, $matches)) {
                include_once $item[0];
                $res = method_invoke($matches[1], "info");
                $res["path"] = $item[0];
                $res["system_module"] = 0;
                $res['enabled']=0;
                $available[$matches[1]] = $res;
            }
        }
        
        foreach ($available as $k => $v) {
            if(in_array($k, array_keys($modules))){
                $modules[$k]['path'] = $v['path'];
            }else{
                $modules[$k] = $v;
            }
        }


        return $modules;
    }
    
    

}
