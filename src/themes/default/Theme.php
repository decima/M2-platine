<?php

class Theme extends Themed {


    public function process_theme($structure = "default") {
        require_once './themes/default/templates/'.$structure.'.php';
    }

    public function process_403() {
        $this -> add_to_body(file_get_contents("./themes/default/pages/403.php"));
        $this -> process_theme("blank");
    }
    public function process_404() {
        $this -> add_to_body(file_get_contents("./themes/default/pages/404.php"));
        $this -> process_theme("blank");
    }
}
