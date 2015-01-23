<?php

class Theme extends Themed {

    public function process_theme($structure = self::STRUCT_DEFAULT) {
        require_once './themes/default/templates/' . $structure . '.php';
    }

    public function process_403() {
        $this->add_to_body(file_get_contents("./themes/default/pages/403.php"));
        $this->process_theme(self::STRUCT_BLANK);
    }

    public function process_404() {
        $this->add_to_body(file_get_contents("./themes/default/pages/404.php"));
        $this->process_theme(self::STRUCT_BLANK);
    }

}
