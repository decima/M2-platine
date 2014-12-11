<?php
class theme implements system_module, m_page {

    public function main() {
        
    }

    public function priority() {
        return -80;
    }

    public function system_init() {
        
    }

    public static function list_of_existing_themes() {
        $list = scandir("theme");
        $themes = array();
        foreach($list as $file)
                if(!( strpos($file, ".") === 0))
                    if(file_exists("theme/$file/theme.php")) {
                    $content = file_get_contents("theme/$file/theme.php");
                    $rows = explode("*", $content);
                    foreach($rows as $row) {
                        $row = trim($row);
                        if((strpos($row, "@") === 0))
                                $row = str_replace("@", "", $row);
                        if(strpos($row, "theme") === 0) {
                            $m = explode(" : ", $row);
                            $themes[] = array("theme" => $m[1],"file_path" => "theme/$file/");
                        }
                    }
                }
        return $themes;
    }

    public static function list_of_declared_themes() {
        $res = database::fetchAll(database::select("theme_manager"));
        return is_array($res) ? $res : array();
    }

    public static function scan_theme_folder() {
        $already_exists = array();
        $declared = self::list_of_declared_themes();
        $existing = self::list_of_existing_themes();
        foreach($declared as $d_theme) {
            $b = false;
            foreach($existing as $e) {
                if($e['theme'] == $d_theme->theme_name) {
                    $b = true;
                    $already_exists[] = array("theme" => $d_theme->theme_name,"path" => $e['file_path']);
                }
            }
            if($b == false) {
                self::remove_theme($d_theme->theme_name);
            }
        }

        foreach($existing as $e) {
            $b = false;
            foreach($already_exists as $ae) {
                if($ae['theme'] == $e['theme']) {
                    $b = true;
                }
            }
            if(!$b) {
                self::add_theme($e['theme'], $e['file_path']);
            }
        }
    }

    private static function remove_theme($theme_name) {
        database::delete("theme_manager", "theme_name='$theme_name'");
    }

    private static function add_theme($theme_name, $path) {
        database::insert("theme_manager",
                array(
            'theme_name' => $theme_name,
            'theme_path' => $path
        ));
    }

    public static function update_action($theme_name, $action = null) {
        database::update(
                "theme_manager", array("action" => $action),
                "theme_name='%theme'", array("%theme" => $theme_name)
        );
    }

    public static function action_set_default($theme_name) {
        database::update(
                "theme_manager", array("theme_default" => 0));
        database::update(
                "theme_manager", array("theme_default" => 1),
                "theme_name='%theme'", array("%theme" => $theme_name)
        );
    }

    public static function action_enable_theme($theme_name) {
        database::update(
                "theme_manager", array("theme_enabled" => 1),
                "theme_name='%theme'", array("%theme" => $theme_name)
        );
    }

    public static function action_disable_theme($theme_name) {
        database::update(
                "theme_manager", array("theme_enabled" => 0),
                "theme_name='%theme'", array("%theme" => $theme_name)
        );
    }

    public static function is_theme_enabled($theme_name) {
        return database::fetch(
                        database::select(
                                "theme_manager", array("theme_enabled"),
                                "theme_name='%theme'",
                                array("%theme" => $theme_name)),
                        PDO::FETCH_COLUMN)
                == 1;
    }

    public static function is_theme_default($theme_name) {
        return database::fetch(
                        database::select(
                                "theme_manager", array("theme_default"),
                                "theme_name='%theme'",
                                array("%theme" => $theme_name)),
                        PDO::FETCH_COLUMN)
                == 1;
    }

    public static function get_default_theme() {
        return database::fetch(database::select(
                                "theme_manager", array(), "theme_default=1"));
    }

    public static function include_theme() {
        $theme = self::get_default_theme();
        require_once $theme->theme_path."theme.php";
        if(file_exists($theme->theme_path."theme_settings.php")) {
            require_once $theme->theme_path."theme_settings.php";
        }
    }

    public static function t_table($array, $header = array()) {
        self::include_theme();
        return theme_ui::theme_table($array, $header);
    }

    public static function t_list($array) {
        self::include_theme();
        return theme_ui::theme_list($array);
    }

    public static function t_form($array) {
        self::include_theme();
        $elements = array();
        foreach($array as $k => $v) {
            if($k == "action") {
                $elements[0] = "form method='POST' 
                action='$v'
                ENCTYPE='x-www-form-urlencoded'";
            }
            if($k == "fields") {
                $elements[1] = array();
                foreach($v as $name => $arr) {
                    $elements[1][] = form::form_generate_field($name, $arr);
                }
            }
        }
        return theme_ui::theme_form($elements);
    }

    public function show_theme() {
        $theme = self::get_default_theme();
        require_once $theme->theme_path."page.php";
    }

    public function menu() {
        return array(
            "/admin/themes" => array(
                "callback" => "theme::theme_list",
                "access" => constants::get("PAGE_ACCESS_FUNCTION"),
                "permission" => "administrer",
            ),
            "/admin/themes/%/%" => array(
                "callback" => "theme::theme_action",
                "access" => constants::get("PAGE_ACCESS_FUNCTION"),
                "permission" => "administrer"
            ),
            "/admin/themes/scan" => array(
                "callback" => "theme::theme_refresh",
                "access" => constants::get("PAGE_ACCESS_FUNCTION"),
                "permission" => "administrer"
            ),
        );
    }

    public static function theme_list() {
        page::title("Themes");
        $array = array();
        $header = array("theme","path","action");
        $themes = theme::list_of_declared_themes();
        foreach($themes as $theme) {

            $res = "";
            if($theme->theme_enabled && !$theme->theme_default) {



                $res = page::link(
                                "admin/themes/".$theme->theme_name."/setdefault",
                                "Set default");
                $res .=" / ";
                $res .=page::link("admin/themes/".$theme->theme_name."/disable",
                                "disable");
            }
            if($theme->theme_enabled && $theme->theme_default) {
                $res = "<big>default theme</big>";
            }
            if(!$theme->theme_enabled) {
                $res = page::link("admin/themes/".$theme->theme_name."/enable",
                                "enable");
            }

            $array[] = array($theme->theme_name,"<small>".$theme->theme_path."</small>",$res);
        }
        $str = '  <a href="'.page::url("admin/themes/scan").'">Scan for more Themes</a><br/>';
        $str .= theme::t_table($array, $header);
        return $str;
    }

    public static function theme_action($theme, $action) {
        theme::update_action($theme, $action);
        page::redirect("/admin/themes");
    }

    public static function theme_refresh() {
        self::scan_theme_folder();
        page::redirect("/admin/themes");
    }

    public static function theme_blocks($position) {
        if(module_manager::is_enabled("blocks")) {
            $path = page::clean_path();
            
            $blocks = blocks::get_blocks_by_path($path,$position);
            $out = "";
            foreach($blocks as $b) {
                $out.=blocks::invoke_block($b);
            }
            return $out;
        }else {
            return " ";
        }
    }

    public static function theme_menu() {
        self::include_theme();

        $modules = invokable_modules("menu_ui");
        $a = array();


        foreach($modules as $module) {
            $b = method_invoke($module, "menu_ui");
            foreach($b as $k => $element) {
                $s = true;
                if(count($a) > 0) {
                    foreach($a as $l => $currents) {
                        if($currents['name'] == $element['name']) {
                            $s = false;
                            if(!isset($currents['url'])) {
                                $a[$l]['url'] = $currents['url'];
                            }
                            if(isset($element['submenu'])) {
                                $a[$l]['submenu'] = array_merge(
                                        $a[$l]['submenu'], $element['submenu']);
                            }
                        }
                    }
                    if($s) {
                        $a[] = $element;
                    }
                }else {
                    $a[] = $element;
                }
            }
        }
        /*         * */
        foreach($a as $key => $link) {

            if(!page::access_check_by_url($link['url'])) {

                unset($a[$key]);
            }else {
                if(isset($link['submenu'])) {
                    foreach($link['submenu'] as $subkey => $sublink) {
                        if(!page::access_check_by_url($sublink['url'])) {
                            unset($a[$key]['submenu'][$subkey]);
                        }
                    }
                }
            }
        }
        /*         * */

        return theme_ui::header_menu($a);
    }

}