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
                header("HTTP/1.0 404 Not Found");
                return;
            }
        }
        $run = true;
        /*if (isset($res["access"])) {
            method_invoke_all("permissions", array($res["access"]));
        }*/
        if (isset($res["security"])) {
            
            $run = call_user_func_array($res["security"], $parameters);
            
        }
        if (isset($res["callback"]) && $run) {
            echo call_user_func_array($res["callback"], $parameters);
        } else {
            
        }
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

    public function menu_process() {
        
    }

}
