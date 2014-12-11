<?php
/**
 * @name : Variable Module
 * @desc : Variable Module.
 * @mach : variable
 * @author : d3cima
 * 
 */
class variable implements common_module, m_install, m_database {

    public function install() {
        
    }

    public function schema() {
        $db = array();
        $db['variables'] = array(
            "fields" => array(
                "variable_name" => "VARCHAR(255) NOT NULL",
                "variable_value" => "TEXT",
            ),
            "pk" => array("variable_name"),
        );
        return $db;
    }

    public static function set($variable, $value) {
        $v = json_encode($value);
        if(!self::exists($variable)) {
            database::insert("variables",
                    array(
                "variable_name" => $variable,
                "variable_value" => $v
            ));
        }else {
            database::update("variables",
                    array(
                "variable_value" => $v
                    ), "variable_name='%var'", array("%var" => $variable));
        }
    }

    public static function exists($variable) {
        $s = database::select("variables", array("variable_value"),
                        "variable_name='%var'", array("%var" => $variable));
        if(($b = database::fetch($s, PDO::FETCH_COLUMN) ) != null) {
            return true;
        }
        return false;
    }

    public static function get($variable, $default) {
        $s = database::select("variables", array("variable_value"),
                        "variable_name='%var'", array("%var" => $variable));
        if(($b = database::fetch($s, PDO::FETCH_COLUMN)) != null) {
            return json_decode($b, true);
        }else {
            return $default;
        }
    }

    public static function del($variable) {
        database::delete("variables", "variable_name='%var'",
                array("%var" => $variable));
    }

}

?>
