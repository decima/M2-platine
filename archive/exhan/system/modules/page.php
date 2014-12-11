<?php
class page implements system_module, m_constants, m_access, m_page {

    public static $title = null;

    public static $content = null;
    public function main() {
        $this->menu_calculation();
    }

    public static function title($t = null, $vars = array()) {
        if($t != null) self::$title = t($t, $vars);
        return self::$title;
    }

    public static function content($c = null) {
        if($c != null) self::$content = $c;
        return self::$content;
    }

    public static function full_access() {
        return true;
    }

    public static function get_all_menus() {
        $menus = method_invoke_all("menu");
        $m = array();
        foreach($menus as $menu) $m = array_merge($m, $menu);
        foreach($m as $k => $v) {
            unset($m[$k]);
            $m[self::clear_path($k)] = $v;
        }
        uksort($m, 'str_compare_count_percent_char');
        return $m;
    }

    public static function menu_get_array_key($url, &$vars = array()) {

        $m = self::get_all_menus();
        if(array_key_exists(self::clear_path($url), $m)) {
            return self::clear_path($url);
        }else {
            $uri = self::explode_path($url);
            $correct_path = null;
            foreach($m as $key => $page) {

                $found = false;
                unset($m[$key]);
                $exploded = self::explode_path($key);

                foreach($uri as $k => $element) {

                    if(isset($exploded[$k])) {

                        if(strpos($exploded[$k], "%") === 0
                                && self::arg($k) != null) {
                            $vars[] = $element;
                        }elseif($exploded[$k] == $element && self::arg($k) != null) {
                            
                        }else {
                            $b = false;
                            break;
                        }
                        $b = true;
                    }else {
                        $b = false;
                        break;
                    }

                    if(isset($exploded[$k + 1]) && !isset($uri[$k + 1])) {
                        $b = false;
                        break;
                    }
                }
                if($b) {

                    return $key;
                }else {
                    $vars = null;
                }
            }
        }
        return false;
    }

    private function menu_calculation() {

        $vars = array();

        $correct_page = array();
        $correct_page['callback'] = constants::get('PAGE_UNKNOWN_FUNCTION');
        $correct_page['access'] = constants::get("PAGE_ACCESS_FUNCTION");
        $correct_page['permission'] = "access content";
        $url = "/";
        $found = false;
        if(isset($_GET['page'])) {
            $menus = self::get_all_menus();
            $correct_path = self::menu_get_array_key($_GET['page'], $vars);

            $correct_page = $menus[$correct_path];
        }else {
            $correct_page['callback'] = constants::get('PAGE_MAIN_FUNCTION');
        }
        if($correct_page) {
            $b = true;
            $c = true;
            $b = self::access_check($url, $correct_page);
            if(isset($correct_page['access'])) {
                $c = call_user_func_array($correct_page['access'],
                        is_array($vars) && array_filter($vars) ? $vars : array());
            }
            if($b && $c) {
                $exec = call_user_func_array($correct_page['callback'],
                        is_array($vars) && array_filter($vars) ? $vars : array());
            }else {
                $exec = call_user_func_array(constants::get("PAGE_FORBIDDEN_FUNCTION"),
                        array());
            }
            if($exec != null && $exec != false) {
                self::content($exec);
                method_invoke_all("show_theme");
            }
        }
    }

    public function priority() {
        return -98;
    }

    public function system_init() {
        
    }

    public static function explode_path($path) {
        $res = explode("/", $path);
        $args = array();
        foreach($res as $resultats) if(!empty($resultats)) $args[] = $resultats;
        return $args;
    }

    public static function clean_path() {
        $path = "";
        $args = self::arg();
        if(is_array($args)) {
            foreach($args as $arg) {
                $path.="$arg/";
            }
        }else {
            $path = "./";
        }
        $path = substr($path, 0, strlen($path) - 1);
        return $path;
    }

    public static function clear_path($path) {
        $path = trim($path);
        if(strstr($path, "//")) {
            $path = str_replace("//", "/", $path);
            $path = self::clear_path($path);
        }
        if(strpos($path, "/") === 0) {
            $path = substr($path, 1);
        }
        if(substr($path, -strlen("/")) === "/") {
            $path = substr($path, 0, -1);
        }
        return $path;
    }

    public static function arg($index = null) {
        $args = array();
        if(isset($_GET['page'])) {
            $res = explode("/", $_GET['page']);
            foreach($res as $resultats)
                    if(!empty($resultats)) $args[] = $resultats;
            return is_numeric($index) ? (isset($args[$index]) ? $args[$index] : null)
                        : $args;
        }
        else {
            return null;
        }
    }

    public static function link($path, $name) {
        $r = "";
        /**
         * TODO vérifier si le lien est accessible.
         *  
         */
        $string = self::url($path);
        $param = "";

        if(strpos($path, "http://") === 0 || strpos($path, "https://") === 0) {
            $string = $path;
            $param="target='blank'";
            
        }

        $r = "<a href='".$string."'".$param.">$name</a>";
        return $r;
    }

    public static function home() {

        return home();
    }

    public static function unset_get_and_post() {
        unset($GLOBALS['HTTP_POST_VARS']);
        unset($GLOBALS['HTTP_GET_VARS']);
        unset($GLOBALS['_REQUEST']);
        unset($GLOBALS['HTTP_POST_FILES']);
        unset($GLOBALS['_FILES']);
        unset($_POST);
        unset($_GET);
    }

    public static function e404() {
        self::unset_get_and_post();
        page::title("404 - Not found");
        return "the requested page is not found or doesn't exist.";
    }

    public static function e403() {

        self::unset_get_and_post();
        page::title("403 - Forbidden");
        return "the requested page is forbidden.";
    }

    public function constants() {
        return array(
            "PAGE_MAIN_FUNCTION" => "page::home",
            "PAGE_UNKNOWN_FUNCTION" => "page::e404",
            "PAGE_FORBIDDEN_FUNCTION" => "page::e403",
            "PAGE_ACCESS_FUNCTION" => "page::full_access",
        );
    }

    public static function redirect($path) {
        header('Location: '.self::url($path));
    }

    public static function url($path) {
        if(strpos($path, "/") === 0) {
            return self::url(substr($path, 1));
        }
        return constants::get("URL")."/$path";
    }

    public static function access_check($page, $page_to_access) {
        $res = array();
        $res = method_invoke_all("check_permission", $page, $page_to_access);
        return count(array_filter($res)) == count($res);
    }

    public static function access_check_by_url($url) {
        $menus = self::get_all_menus();
        $vars = array();
        $key = self::menu_get_array_key($url, $vars);
        $a = self::access_check($key, $menus[$key]);
        $b = call_user_func_array($menus[$key]['access'],
                is_array($vars) && array_filter($vars) ? $vars : array());
        return $a && $b;
    }

    public function perms() {
        return array(
            "access content" => "Access all kind of content",
        );
    }

    public function menu() {
        $array = array();
        $array[0] = array(
            "callback" => "page::e404",
            "access" => constants::get("PAGE_ACCESS_FUNCTION"),
            "permission" => "access content"
        );
        return $array;
    }

}