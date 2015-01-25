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

    public static function is_installed($moduleName) {
        $r = file_get_contents("./cache/installed.txt");
        $res = explode(",", $r);
        return (in_array($moduleName, $res));
    }

    public static function cache_i($mname) {
        file_put_contents("./cache/installed.txt", "," . $mname, FILE_APPEND);
    }

    public static function cache_ui($mname) {
        $r = file_get_contents("./cache/installed.txt");
        $res = explode(",", $r);
        foreach ($res as $k => $v) {
            if (strtolower($v) == strtolower($mname)) {
                unset($res[$k]);
            }
        }
        $r = implode(",", $res);
        file_put_contents("./cache/installed.txt", $r);
    }

    public static function is_enabled($moduleName) {
        $r = file_get_contents("./cache/enabled.txt");
        $res = explode(",", $r);
        return (in_array($moduleName, $res));
    }

    public static function cache_ea($mname) {
        file_put_contents("./cache/enabled.txt", "," . $mname, FILE_APPEND);
    }

    public static function cache_da($mname) {
        $r = file_get_contents("./cache/enabled.txt");
        $res = explode(",", $r);
        foreach ($res as $k => $v) {
            if (strtolower($v) == strtolower($mname)) {
                unset($res[$k]);
            }
        }
        $r = implode(",", $res);
        file_put_contents("./cache/enabled.txt", $r);
    }

    public static function install_module($moduleName, $path) {
        require_once($path);
        if (!self::is_installed($moduleName)) {
            $schema = method_invoke($moduleName, "schema");
            try {
                Database::schema_installer($schema);
                $res = method_invoke($moduleName, "install");
                $t = $res == null ? true : $res;
                if ($t) {
                    self::cache_i($moduleName);
                }
                return $t;
            } catch (Exception_Database $e) {
                return false;
            }
        }
        return false;
    }

    public static function uninstall_module($moduleName, $path) {
        require_once($path);
        if (!self::is_installed($moduleName) || self::is_enabled($moduleName)) {
            return false;
        }
        $schema = method_invoke($moduleName, "schema");
        Database::schema_uninstaller($schema);
        $res = method_invoke($moduleName, "uninstall");
        $t = $res == null ? true : $res;
        if ($t) {
            self::cache_ui($moduleName);
        }
        return $t;
    }

    public static function enable_module($moduleName, $path) {
        $exists = method_invoke($moduleName, "info");
        $res = false;
        if (!self::is_enabled($moduleName) && self::is_installed($moduleName)) {
            $rust = file_get_contents("./cache/classes.php");
            $rust .= "// @module:$moduleName\nrequire_once('$path');\n";
            file_put_contents("./cache/classes.php", $rust);

            $res = method_invoke($moduleName, "enable");
        }
        $t = $res !== false ? true : false;

        if ($t) {
            self::cache_ea($moduleName);
        }
        return $t;
    }

    public static function disable_module($moduleName) {
        $rust = file_get_contents("./cache/classes.php");
        if (!self::is_installed($moduleName) || !self::is_enabled($moduleName)) {
            return false;
        }
        $file = explode("//", $rust);

        foreach ($file as $row_id => $row) {
            if (strstr($row, "@module:$moduleName")) {
                unset($file[$row_id]);
            }
        }
        $rust = implode("//", $file);
        file_put_contents("./cache/classes.php", $rust);
        $res = method_invoke($moduleName, "disable");
        $t = $res == null ? true : $res;
        if ($t) {
            self::cache_da($moduleName);
        }
        return $t;
    }

    public function menu($item = array()) {
        $item['admin/modules'] = array(
            "access" => "administration",
            "callback" => array("ModuleManager", "list_modules")
        );
        $item['admin/modules/install/@'] = array(
            "access" => "administration",
            "callback" => array("ModuleManager", "installModulePage")
        );
        $item['admin/modules/enable/@'] = array(
            "access" => "administration",
            "callback" => array("ModuleManager", "enableModulePage")
        );
        $item['admin/modules/disable/@'] = array(
            "access" => "administration",
            "callback" => array("ModuleManager", "disableModulePage")
        );
        $item['admin/modules/uninstall/@'] = array(
            "access" => "administration",
            "callback" => array("ModuleManager", "uninstallModulePage")
        );
        return $item;
    }

    public static function list_modules() {
        if (isset($_GET['err'])) {
            switch ($_GET['err']) {
                case 'install':

                    Notification::statusNotify(t("Echec de l'installation du module"), Notification::STATUS_ERROR);
                    break;
                case 'enable':
                    Notification::statusNotify(t("Echec de l'activation du module"), Notification::STATUS_ERROR);
                    break;
                case 'disable':
                    Notification::statusNotify(t("Echec de la desactivation du module"), Notification::STATUS_ERROR);
                    break;
                case 'uninstall':
                    Notification::statusNotify(t("Echec de la désinstation du module"), Notification::STATUS_ERROR);
                    break;
                default:
                    Notification::statusNotify(t("Une erreur inconnue est survenue"), Notification::STATUS_ERROR);

                    break;
            }
        }
        $modules = self::scan_all_modules();
        $theme = new Theme();

        $theme->set_title(t("Liste des modules disponibles"));
        Notification::statusNotify(t("%cnt modules disponibles",array("%cnt"=>count($modules))), Notification::STATUS_INFO);
        $r = array(t("Nom du module"), t("Etat du module"), t("Actions"));
        $array = array();
        foreach ($modules as $m) {

            $install = $theme->linking(Page::url("/admin/modules/install/" . $m['name']), t("installer"));
            $uninstall = $theme->linking(Page::url("/admin/modules/uninstall/" . $m['name']), t("désinstaller"));
            $disable = $theme->linking(Page::url("/admin/modules/disable/" . $m['name']), t("désactiver"));
            $enable = $theme->linking(Page::url("/admin/modules/enable/" . $m['name']), t("activer"));

            $statement = t("activé");
            $link_1 = $disable;
            $link_2 = null;
            if (!self::is_enabled($m['name'])) {
                $statement = t("installé");
                $link_1 = $enable;
                $link_2 = $uninstall;
            }
            if (!self::is_installed($m['name'])) {
                $link_1=$install;$link_2 = null;
                $statement = t("désinstallé");
            }


            if ($m["system_module"] == 1) {
                $rtm = "système";
            } else {
                $rtm = ($link_1).($link_2==null?"":"- ").$link_2;
            }
            $array[] = array($m["readablename"], $statement, $rtm);
        }



        usort($array, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });
        $theme->add_to_body($theme->tabling($array, $r));
        $theme->process_theme(Theme::STRUCT_ADMIN);
        return;
    }

    public static function installModulePage($moduleName) {

        $modules = self::scan_all_modules();
        if (isset($modules[$moduleName])) {
            if (self::install_module($modules[$moduleName]["name"], $modules[$moduleName]["path"])) {
                header("location: " . Page::url("/admin/modules/"));
            } else {
                header("location: " . Page::url("/admin/modules/?err=install"));
            }
        } else {
            header("location: " . Page::url("/admin/modules/?err=unknown"));
        }
        return;
    }

    public static function enableModulePage($moduleName) {

        $modules = self::scan_all_modules();
        if (isset($modules[$moduleName])) {
            if (self::enable_module($modules[$moduleName]["name"], $modules[$moduleName]["path"])) {
                header("location: " . Page::url("/admin/modules/"));
            } else {
                header("location: " . Page::url("/admin/modules/?err=enable"));
            }
        } else {
            header("location: " . Page::url("/admin/modules/?err=unknown"));
        }
        return;
    }

    public static function disableModulePage($moduleName) {

        $modules = self::scan_all_modules();
        if (isset($modules[$moduleName])) {
            if (self::disable_module($modules[$moduleName]["name"], $modules[$moduleName]["path"])) {
                header("location: " . Page::url("/admin/modules/"));
            } else {
                header("location: " . Page::url("/admin/modules/?err=disable"));
            }
        } else {
            header("location: " . Page::url("/admin/modules/?err=unknown"));
        }
        return;
    }

    public static function uninstallModulePage($moduleName) {

        $modules = self::scan_all_modules();
        if (isset($modules[$moduleName])) {
            if (self::uninstall_module($modules[$moduleName]["name"], $modules[$moduleName]["path"])) {
                header("location: " . Page::url("/admin/modules/"));
            } else {
                header("location: " . Page::url("/admin/modules/?err=uninstall"));
            }
        } else {
            header("location: " . Page::url("/admin/modules/?err=unknown"));
        }
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
        $systemModules = array_map('strtolower', $systemModules);
        $available = array();

        foreach ($enabled as $k => $e) {
            $modules[$e['name']] = $e;
            $modules[$e['name']]["path"] = "";

            $modules[$e['name']]["system_module"] = in_array(strtolower($e["name"]), $systemModules) ? "1" : "0";
            $modules[$e['name']]["enabled"] = 1;
        }

        foreach ($regex as $item) {
            $data = file_get_contents($item[0]);
            if (preg_match("/@moduleName ([A-Za-z0-9_]+)/", $data, $matches)) {
                include_once $item[0];
                $res = method_invoke($matches[1], "info");
                $res["path"] = $item[0];
                $res["system_module"] = 0;
                $res['enabled'] = 0;
                $available[$matches[1]] = $res;
            }
        }

        foreach ($available as $k => $v) {
            if (in_array($k, array_keys($modules))) {
                $modules[$k]['path'] = $v['path'];
            } else {
                $modules[$k] = $v;
            }
        }


        return $modules;
    }

}
