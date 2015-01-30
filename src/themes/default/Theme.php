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
        $this->set_title("Erreur 404");
        $this->add_to_body(file_get_contents("./themes/default/pages/404.php"));
        $this->process_theme(self::STRUCT_BLANK);
        //$this->process_theme(self::STRUCT_404);
    }

    public static function showMenu(){
        require_once './themes/default/templates/menu.php';
    }

    public static function tabling($rows, $headers = array(), $hcol = array()) {
        $output = "<div style='padding-left: 20px; padding-right: 20px; padding-bottom: 20px;'>";
        $output .= "<table class=\"tableau\">\n";
        if (count($headers) > 0) {
            $output .="<thead>\n<tr>\n";
            foreach ($headers as $h)
                $output .="<th>" . $h . "</th>\n";
            $output .="</tr>\n</thead>\n";
        }
        $output .="<tbody>\n";
        $variant1 = "variant1";
        $variant2 = "variant2";
        foreach ($rows as $k => $r) {
            $output .="<tr class='values ";
                if($k % 2 == 0)
                    $output .= $variant1;
                else
                    $output .= $variant2;
            $output .= "'>\n";
            if($a = array_shift($hcol) != null) {
                $output .= "<th>".$hcol."</th>\n";
            }
            foreach ($r as $r2)
                $output.="<td>$r2</td>\n";
            $output .="</tr>\n";
        }
        $output .="</tbody>\n";
        $output .= "</table>";
        $output .= "</div>";

        return $output;
    }


    public static function displayNotification(){
        $output = "";
        foreach(Notification::getStatusNotifications() as $n){
            $output .= "<table class=\"bandeau_info\">";
                $output .= "<tr class=\"bandeau_info_inside\">";
                    $output .= "<td>";
                    switch($n->type){
                        case Notification::STATUS_INFO:
                            $output .= "<i class=\"fa fa-info fa-lg bandeau_info_inside_td\"></i>";
                            break;
                        case Notification::STATUS_ERROR:
                            $output .= "<i class=\"fa fa-remove fa-lg bandeau_info_inside_td\"></i>";
                            break;
                        case Notification::STATUS_WARNING:
                            $output .= "<i class=\"fa fa-warning fa-lg bandeau_info_inside_td\"></i>";
                            break;
                        case Notification::STATUS_SUCCESS:
                            $output .= "<i class=\"fa fa-check fa-lg bandeau_info_inside_td\"></i>";
                            break;
                    }
                    $output .= "</td>";
                    $output .= "<td class=\"bandeau_info_inside_td\">";
                        $output .= $n->message;
                    $output .= "</td>";
                $output .= "</tr>";
            $output .= "</table>";
        }
        return $output;
    }
}
