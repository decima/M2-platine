<?php
/*
 * @theme : slate
 * @description : official theme using bootstrap3
 * @author : d3cima
 */
class theme_ui implements theme_template {

    public static function theme_list($array) {
        $ret = "<ul class='list-group'>";
        foreach($array as $element) {
            $ret .= "<li class='list-group-item'>".$element."</li>";
        }
        $ret.="</ul>";
        return $ret;
    }
    public static function theme_table($array, $head_row = array()) {
        $ret = '<div class="panel panel-default"><table class="table">';
        if(sizeof($head_row) > 0) {
            $ret .='<thead><tr>';
            foreach($head_row as $h) {
                $ret.="<th>$h</th>";
            }
            $ret .='</tr></thead>';
        }
        foreach($array as $row) {
            $ret.="<tbody><tr>";
            foreach($row as $cell) {
                $ret.="<td>$cell</td>";
            }
            $ret.="</tr></tbody>";
        }

        $ret.='</table></div>';
        return $ret;
    }


    public static function theme_form($array) {
        $o = "<".$array[0]." role='form' >";
        foreach($array[1] as $element) {

            $p = '<div class="form-group">';
            $p.='<label>'.$element->label.'</label>';
            $attributes = "";
            if($element->html_tag == "button") {
                $element->attr["class"] .= "btn btn-primary";
            }else {
                $element->attr["class"] .= "form-control";
            }
            foreach($element->attr as $name => $val) {
                $attributes .= " $name=\"$val\"";
            }


            $p .= "<".$element->html_tag." ".$attributes." ";


            if($element->open_tag) {
                $p .="/>";
            }else {
                $p .=">".$element->html_content."</".$element->html_tag.">";
            }
            $p.="</div>";
            $o.=$p;
        }
        $o.="</form>";

        return $o;
    }

    public static function error_403() {
        
    }

    public static function error_404() {
        
    }

    public static function error_notfound() {
        
    }

    public static function header_menu($args) {
       
        $out = '<ul class="nav navbar-nav">';

        foreach($args as $menu) {
            if(isset($menu['submenu'])) {
                $out.="<li class='dropdown'>";
                $out.="<a href=\"".page::url($menu['url'])."\" 
                    class=\"dropdown-toggle\" 
                    data-toggle=\"dropdown\">";
                $out.=$menu['name'];
                $out.='<b class="caret caret-inverse"></b></a>
                    <ul class="dropdown-menu">';
                foreach($menu['submenu'] as $submenu) {
                    $out .="<li>".page::link($submenu['url'], $submenu['name'])."</li>";
                }
                $out.="</ul></li>";
            }else {
                $out .="<li>".page::link($menu['url'], $menu['name'])."</li>";
            }
        }


        $out .="</ul>";
        return $out;
    }

}