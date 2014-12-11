<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of constants
 *
 * @author decima
 */

class constants implements system_module, m_constants {

    private static $_data = null;
    public function priority() {
        return -100;
    }

    public function system_init() {
        if(self::$_data == null) {
            self::$_data = new stdClass();
            $res = method_invoke_all("constants");
            foreach($res as $result) {
                if(is_array($result)) {
                    foreach($result as $key => $value) {
                        $this->$key = $value;
                    }
                }
            }
        }
    }

    public function main() {
        
    }

    public function __get($name) {
        return self::$_data->$name;
    }

    public function __set($name, $value) {
        if(isset(self::$_data->$name)) {
            throw new UnexpectedValueException("already set");
        }
        self::$_data->$name = $value;
    }

    public static function get($name) {
        return self::$_data->$name;
    }

    public function constants() {
        
        $path = "http://".$_SERVER["SERVER_NAME"];
        if(substr($path, -1) === "/"){
            $path=substr($path, 0, -1);
            
            }
        if($_SERVER["SERVER_PORT"] == 8888) {
            $path .=":".$_SERVER["SERVER_PORT"]."/exhan";
        }

        return array(
            "URL" => $path,
        );
    }

    /*
      public static function __callStatic($name, $arguments) {
      if(!empty($arguments)) {
      if(isset(self::$_data->$name)) {
      throw new UnexpectedValueException("already set");
      }
      self::$_data->$name = count($arguments) > 1 ? $arguments : $arguments[0];
      }else {
      if(isset(self::$_data->$name)) {
      return self::$_data->$name;
      }else {
      throw new UnexpectedValueException("empty value");
      }
      }
      }
     */
}
