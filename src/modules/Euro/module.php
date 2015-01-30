<?php

/**
 * @moduleName Euro
 * 
 * 
 * */
class Euro implements Module {

    public function info() {
        _Security::version(0, 1);
        return array(
            "name" => "Euro",
            "readablename" => "Euro - Sample Module"
        );
    }

    public function menu($item = array()) {
        $item["/euro/@"] = array("callback" => array("Euro", "price"));

        return $item;
    }

    public static function price($euro) {
        $theme = new Theme();
        $theme->set_title("Convertion euro > franc");
        $theme->add_to_body($euro * 6.55957);
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }
}
