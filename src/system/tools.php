<?php

class _Security {

    private static $err = null;

    static function addSlahesLoop($array) {
        return is_array($array) ?
                array_map(self::addSlahesLoop, $array) :
                addslashes($array);
    }

    static function stripSlashesLoop($array) {
        return is_array($array) ?
                array_map(self::stripSlashesLoop, $array) :
                stripSlashes($array);
    }

    public static function enable_debug($enable = true) {
        if ($enable) {
            ini_set("display_startup_errors", true);
            ini_set("display_errors", true);
            error_reporting(E_ALL);
        } else {
            ini_set("display_startup_errors", false);
            ini_set("display_errors", false);
        }
    }

    public static function enable_error_handling() {
        self::enable_debug(true);
        set_error_handler(array(__CLASS__, "jinn_error_handler"), E_ALL ^ E_STRICT);
    }

    public static function jinn_error_handler($errno, $errstr, $errfile, $errline) {
        if (ini_get("display_errors") == true) {
            echo $errstr;
        }
        if ($errno == 256)
            exit();
        return false;
    }

    public static function deprecated() {
        $callers = debug_backtrace();
        $definition = self::error_generate("is deprecated and should not be used", $callers);
        user_error("$definition", E_USER_DEPRECATED);
    }

    public static function version($minv = 0, $maxv = CURRENT_VERSION) {

        if ($minv > CURRENT_VERSION || $maxv < CURRENT_VERSION) {
            $callers = debug_backtrace();
            user_error(self::error_generate("is not available for Jinn " . CURRENT_VERSION . " ( min: $minv, max: $maxv )", $callers), E_USER_ERROR);
            return false;
        }
        return true;
    }

    public static function generated_function_signature($c) {
        $str = "<pre style='display:inline'>function ";
        $functionName = $c['function'];
        $args = $c["args"];
        $str .="<span style='color:#993333;font-weight:bold;' class='jinn_error_signature_function_name'>$functionName</span>(";
        $ags = array();
        foreach ($args as $k => $r) {
            $type = gettype($r);
            if ($type == "object") {
                $type = get_class($r);
            }
            $ags[] = "<span style='color:#339933;font-weight:bold;' class='jinn_error_signature_argument_type'>$type</span> <span style='font-style:italic;' class='jinn_error_signature_argument_var'>\$arg$k</span>";
        }
        $str .=implode(",", $ags);
        $str .=")</pre>";
        return $str;
    }

    public static function error_generate($error, $callers = array()) {
        $res = self::generated_function_signature($callers[1]) . " " . $error . " in<br/>";
        $i = 0;
        $res.="<pre style='display:inline'>    " . $callers[$i]['file'] . ":" . $callers[$i]['line'] . "</pre><br/>";
        /*
          for ($i = 1; $i < count($callers); $i++) {
          $res.="<pre style='display:inline'>    " . $callers[$i]['function'] . " - " . $callers[$i]['file'] . ":" . $callers[$i]['line'] . "</pre><br/>";
          }

         */
        return $res;
    }

}

function arrayToObject($d) {
    return is_array($d) ? (object) array_map(__FUNCTION__, $d) : $d;
}

function objectToArray($d) {
    $d = is_object($d) ? get_object_vars($d) : $d;
    return is_array($d) ? array_map(__FUNCTION__, $d) : $d;
}
