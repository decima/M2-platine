<?php
class module_manager implements system_module, m_constants {

    public function main() {
        
    }

    public static function scan_module_folder($path) {
        $list = scandir($path);
        $modules = array();
        foreach($list as $file) {

            if(!(strpos($file, ".") === 0)) {
                $files = scandir($path."/".$file);
                foreach($files as $f) {
                    if(!("." === "" || strpos($f, ".") === 0)) {
                        if(stristr($f, ".php")) {
                            $content = file_get_contents("$path/$file/$f");
                            $content = htmlentities($content);
                            $rows = explode("*", $content);
                            foreach($rows as $row) {
                                $row = trim($row);
                                if((strpos($row, "@") === 0)) {
                                    $row = str_replace("@", "", $row);
                                    if(strpos($row, "mach") === 0) {
                                        $m = explode(" : ", $row);
                                        $modules[] = array("module" => $m[1],"file_path" => "$path/$file/$f");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $modules;
    }

    public static function list_of_existing_modules() {
        $modules = array();
        $modules['common'] = self::scan_module_folder("./common/modules");
        $modules['user'] = self::scan_module_folder("./modules");

        return $modules;
    }

    public static function list_of_declared_modules() {
        $res = database::fetchAll(database::select("module_manager", array(),
                                "1 ORDER BY module_type, module_name"));

        return $res;
    }

    public static function scan_and_update_list() {
        $already_exists = array();
        $declared = self::list_of_declared_modules();
        $existing = self::list_of_existing_modules();
        foreach($declared as $d_module) {
            $b = false;
            foreach($existing as $type => $existings) {
                foreach($existings as $e) {
                    if($e["module"] == $d_module->module_name) {
                        $b = true;
                        $already_exists[] = array("module" => $d_module->module_name,"path" => $e['file_path'],"type" => $type);
                    }
                }
            }
            if($b == false) {
                self::remove_module($d_module->module_name);
            }
        }
        foreach($existing as $type => $existings) {
            foreach($existings as $e) {
                $b = false;
                foreach($already_exists as $ae) {
                    if($ae['module'] == $e['module']) {
                        $b = true;
                    }
                }
                if($b == false) {
                    self::add_module($e['module'], $e['file_path'], $type);
                }
            }
        }
    }

    public static function add_module($module_name, $path, $type = null) {
        if($type == null) {
            $type = constants::get("MODULE_TYPE_USER");
        }
        database::insert("module_manager",
                array(
            "module_name" => $module_name,
            "module_type" => $type,
            "module_path" => $path
        ));
    }

    public static function get_module($module_name) {
        return database::fetch(database::select(
                                "module_manager", array(),
                                "module_name = '%module'",
                                array("%module" => $module_name)
                        ));
    }

    public static function remove_module($module_name) {
        database::delete("module_manager", "module_name = '%module'",
                array("%module" => $module_name));
    }

    public static function is_installed($module_name) {
        $res = database::fetch(database::select("module_manager",
                                array("module_installed"),
                                "module_name = '%module'",
                                array("%module" => $module_name)),
                        PDO::FETCH_COLUMN);
        if($res) {
            return $res == 1;
        }
        return false;
    }

    public static function is_enabled($module_name) {
        $res = database::fetch(database::select("module_manager",
                                array("module_enabled"),
                                "module_name = '%module'",
                                array("%module" => $module_name)),
                        PDO::FETCH_COLUMN);
        if($res) {
            return $res == 1;
        }
        return false;
    }

    public static function update_action($module_name, $action = null) {
        database::update(
                "module_manager", array("action" => $action),
                "module_name = '%module'", array("%module" => $module_name)
        );
    }

    public static function prepare_install_module($module_name) {
        if(!module_manager::is_installed($module_name)) {
            self::update_action($module_name,
                    constants::get("MM_ACTION_INSTALL"));
        }
    }

    public static function prepare_uninstall_module($module_name) {
        if(module_manager::is_installed($module_name)
                && !module_manager::is_enabled($module_name)) {

            self::update_action($module_name,
                    constants::get("MM_ACTION_UNINSTALL"));
        }
    }

    public static function prepare_enable_module($module_name) {
        if(module_manager::is_installed($module_name)
                && !module_manager::is_enabled($module_name)) {

            self::update_action($module_name, constants::get("MM_ACTION_ENABLE"));
        }
    }

    public static function prepare_disable_module($module_name) {
        if(module_manager::is_enabled($module_name)) {

            self::update_action($module_name,
                    constants::get("MM_ACTION_DISABLE"));
        }
    }

    public static function install_module($module_name) {
        if(!module_manager::is_installed($module_name)) {
            $module = self::get_module($module_name);
            require $module->module_path;

            method_invoke_all("install_schema", $module_name);
            $result = method_invoke($module_name, "install");
            database::update(
                    "module_manager", array("module_installed" => 1),
                    "module_name = '%module'", array("%module" => $module_name));

            self::update_action($module_name);
        }
    }

    public static function uninstall_module($module_name) {
        if(!module_manager::is_enabled($module_name)
                && module_manager::is_installed($module_name)) {
            $module = self::get_module($module_name);

            require $module->module_path;
            $result = method_invoke($module_name, "uninstall");

            method_invoke_all("uninstall_schema", $module_name);
            database::update(
                    "module_manager", array("module_installed" => 0),
                    "module_name = '%module'", array("%module" => $module_name));
            self::update_action($module_name);
        }
    }

    public static function disable_module($module_name) {
        if(module_manager::is_enabled($module_name)
                && module_manager::is_installed($module_name)) {

            database::update(
                    "module_manager", array("module_enabled" => 0),
                    "module_name = '%module'", array("%module" => $module_name));

            $result = method_invoke($module_name, "disable");

            self::update_action($module_name);
        }
    }

    public static function enable_module($module_name) {
        if(module_manager::is_installed($module_name)) {
            $module = self::get_module($module_name);
            require $module->module_path;
            $result = method_invoke($module_name, "enable");
            database::update(
                    "module_manager", array("module_enabled" => 1),
                    "module_name = '%module'", array("%module" => $module_name));
            self::update_action($module_name);
        }
    }

    public function priority() {
        return -98;
    }

    public function system_init() {
        
    }

    public function constants() {
        return array(
            'MODULE_TYPE_COMMON' => "common",
            'MODULE_TYPE_USER' => 'user',
            'MM_ACTION_INSTALL' => 'install',
            'MM_ACTION_UNINSTALL' => 'uninstall',
            'MM_ACTION_ENABLE' => 'enable',
            'MM_ACTION_DISABLE' => 'disable',
        );
    }

    public function menu() {
        $menu = array();
        $menu['admin/modules/scan'] = array(
            "callback" => "module_manager::page_scan_modules",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => 'administrer',
        );
        $menu['/admin/modules/'] = array(
            "callback" => "module_manager::page_list_modules",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => 'administrer',
        );
        $menu['/admin/modules/%/%'] = array(
            "callback" => "module_manager::page_action_modules",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => 'administrer',
        );

        return $menu;
    }

    public static function page_scan_modules() {
        self::scan_and_update_list();
        page::redirect("admin/modules");
    }

    public static function page_list_modules() {
        page::title("Modules");
        $res = array();
        $head = array("module","action");
        $modules = module_manager::list_of_declared_modules();

        foreach($modules as $module) {
            $b = array();

            $b[0] = $module->module_name;
            $b[1] = "";
            if($module->module_installed && !$module->module_enabled) {



                $b[1] .=page::link(
                                "admin/modules/".$module->module_name."/enable",
                                "Enable");
                $b[1] .=" / ";

                $b[1] .=page::link(
                                "admin/modules/".$module->module_name."/uninstall",
                                "Uninstall");
            }


            if($module->module_installed && $module->module_enabled)
                    $b[1] .=page::link("admin/modules/".$module->module_name."/disable",
                                "disable");
            if(!$module->module_installed)
                    $b[1] .=page::link("admin/modules/".$module->module_name."/install",
                                "install");
            $res[] = $b;
        }
        $str = '  <a href="'.page::url("admin/modules/scan").'">Scan for more modules</a><br/>';
        $str .= theme::t_table($res, $head);
        return $str;
    }

    public static function page_action_modules($module, $action) {
        switch($action) {
            case "install":
                self::prepare_install_module($module);
                break;
            case "uninstall":
                self::prepare_uninstall_module($module);
                break;
            case "enable":
                self::prepare_enable_module($module);
                break;
            case "disable":
                self::prepare_disable_module($module);
                break;
        }
        page::redirect("admin/modules/");
    }

}

