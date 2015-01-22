<?php

class Theme extends Themed {

   
    public function process_theme() {
        require_once './themes/default/structure.php';
    }

    public function process_403() {
        require_once './themes/default/403.php';
    }
    public function process_404() {
        require_once './themes/default/404.php';
    }
}
