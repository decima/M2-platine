<?php

class Page implements SystemModule {

    public function info() {
        return array(
            "name" => "Page",
            "readablename" => "Page system"
        );
    }

    public function priority() {
        return -99;
    }

    public function system_init() {

        $res = $this->get_declared_pages();
        $jpage = "";
        $page = array("/");
        if (isset($_GET['jpage'])) {
            $jpage = trim($_GET['jpage'], "/");
            $page = explode("/", $jpage);
        }
        $parameters = array();
        foreach ($page as $p) {
            if (isset($res[$p])) {
                $res = $res[$p];
            } elseif (isset($res['@'])) {
                $parameters[] = $p;
                $res = $res['@'];
            } else {
                echo $this->E404();
                return;
            }
        }
        $run = true;

        if (isset($res["access"])) {

            $r = method_invoke_all("permissions", array($res["access"]));

            foreach ($r as $tt) {
                if ($tt == false) {
                    $run = false;
                }
            }
        }
        if (isset($res["security"])) {
            if (is_array($res['security']) && count($res['security']) > 1) {
                if (method_exists($res['security'][0], $res['security'][1])) {
                    $run = $run && call_user_func_array($res["security"], $parameters);
                }
            } elseif (function_exists($res['security'])) {
                $run = $run && call_user_func_array($res["security"], $parameters);
            } else {
                $run = false;
            }
        }
        if (isset($res['headers'])) {
            foreach ($res['headers'] as $k => $header) {
                header($k . ": " . $header);
            }
        }
        $executed = false;
        $allowed = true;

        if (isset($res["callback"]) && $run) {

            if (is_array($res['callback']) && count($res['callback']) == 2) {
                if (method_exists($res['callback'][0], $res['callback'][1])) {
                    $executed = true;
                    echo call_user_func_array($res["callback"], $parameters);
                }
            } elseif (function_exists($res['callback'])) {

                $executed = true;
                echo call_user_func_array($res["callback"], $parameters);
            }
        } elseif (!$run) {
            $allowed = false;
        }

        if (!$allowed) {
            echo $this->E403();
            return;
        }
        if (!$executed) {
            echo $this->E404();
            return;
        }
    }

    public function E404() {
        $t = new Theme();
        $t->process_404();
        return;
    }

    public function E403() {

        $t = new Theme();
        $t->process_403();
        return;
    }

    public static function url($path = "/") {
        $s = isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : "http";
        $s .="://" . $_SERVER['SERVER_NAME'];
        if ($_SERVER['SERVER_PORT'] != 443 && $_SERVER['SERVER_PORT'] != 80)
            $s .=":" . $_SERVER['SERVER_PORT'];

        $s .=str_replace("/index.php", "", $_SERVER['PHP_SELF']);
        return $s . $path;
    }
    public static function path($path="/"){
        $s =  getcwd();
        return $s.$path;
    }
    public function get_declared_pages() {
        $res = (method_invoke_all("menu", array(), true));
        $t = array();
        foreach ($res as $k => $v) {
            $k = trim(preg_replace("(/+)", "/", $k), "/");
            if ($k == "") {
                $keys = array("/" => $v);
            } else {
                $keys = $this->sub_menu($k, $v);
            }
            $t = array_merge_recursive($t, $keys);
        }
        return $t;
    }

    private function sub_menu($path, $val) {
        $tab = explode("/", trim($path, "/"));
        $tmptab = $tab[0];
        if (count($tab) > 1) {
            array_shift($tab);
            return array($tmptab => $this->sub_menu(implode("/", $tab), $val));
        } else {
            return array($tmptab => $val);
        }
    }

}
