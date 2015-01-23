<?php

class Theme extends Themed {

    const STRUCT_404 = "404";

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
        //$this->process_theme(self::STRUCT_404);
    }

    public static function menu(){
        require_once './themes/default/templates/menu.php';
    }

    public static function tabling($rows, $headers = array(), $hcol = array()) {
        $output = "<table class=\"tableau\">\n";
        if (count($headers) > 0) {
            $output .="<thead>\n<tr>\n";
            foreach ($headers as $h)
                $output .="<th>" . $h . "</th>\n";
            $output .="</tr>\n</thead>\n";
        }
        $output .="<tbody>\n";
        $variant1 = "variant1";
        $variant2 = "variant2";
        foreach ($rows as $r) {
            $output .="<tr>\n";
            if($a = array_shift($hcol) != null) {
                $output .= "<th>".$hcol."</th>\n";
            }
            foreach ($r as $r2)
                $output.="<td>$r2</td>\n";
            $output .="</tr>\n";
        }
        $output .="</tbody>\n";
        $output .= "</table>";

        return $output;
    }
}
