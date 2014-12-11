<?php
class content_page {

    public static function list_of_contents_types() {
        
    }

    public static function form_content($content_type, $nid = 0) {
        $content_type = content_database::load_node_type(
                        $content_type);
        if($nid != 0) {
            
        }else {
            page::title("add new %node", array("%node" => $content_type->name));
        }

        $form = array();
        $form['action'] = page::url("node/adding/".$content_type->type);
        $form['fields'] = array();
        if($content_type->has_title) {
            $form['fields']['title'] = array(
                "type" => "input",
                "label" => "title",
            );
        }
        if($content_type->has_body) {
            $form['fields']['body'] = array(
                "type" => "text",
                "label" => "Body",
            );
        }
        foreach($content_type->fields as $field) {
            $fname = $field['field_machine_name'];
            $form["fields"][$fname] = array(
                "label" => $field['field_name'],
            );
            switch($field["field_type"]) {
                case "TEXT" || "text":
                    $form["fields"][$fname]["type"] = "text";
                    break;
            }
        }

        $form["fields"]["submit"] = array(
            "type" => "submit",
            "value" => "save"
        );
        return theme::t_form($form);
    }

    public static function post_author_date($uid,$author, $date) {
        $u = page::link("user/".$uid, $author);
        $k = print_date($date);
        return t("posted by %user on %date", array("%user" => $u,"%date" => $k));
    }

    public function view_node($nid) {
        $node = content_database::node_load($nid);
        if(!$node) page::redirect("");

        $nt = content_database::load_node_type($node->type);
        $out = "";
        
        if($nt->has_title && isset($node->title) && $node->title != null) {
            page::title($node->title);
        }
        
        if($node->nid != 0) {
            $out .="<h6>";
            $out .= self::post_author_date($node->uid,$node->author, $node->date);
            $out .="</h6>";
        }
        if($nt->has_body && isset($node->description)) {
            $out .="<div class='body field'>";
            $out .= $node->description;
            $out .="</div>";
        }
        foreach($nt->fields as $field) {
            $out .="<div class='field' id='field_".$field['field_machine_name']."'>";
            $out .="<span class='field_title' id='title_".$field['field_machine_name']."'>".
                    $field['field_name']
                    ."</span>";
            $out .="<div class='field_content' id='content_".$field['field_machine_name']."'>";
            $f = $field['field_machine_name'];
            $out .=$node->$f;
            $out.="</div>";
            $out.="</div>";
        }
        return $out;
    }

    public function save_content($type) {
        global $user;
        if(isset($_POST['submit'])) {
            unset($_POST['submit']);
            if(isset($_POST['nid'])) {
                /* EDIT FORM */
            }else {
                $title = "";
                $body = "";

                if(isset($_POST['title'])) {
                    $title = $_POST['title'];
                    unset($_POST['title']);
                }
                if(isset($_POST['body'])) {
                    $body = $_POST['body'];
                    unset($_POST['body']);
                }

                $nid = content_database::insert_node($type, $title, $body,
                                $_POST
                                , (isset($user->uid) ? $user->uid : 0),
                                (isset($user->username) ? $user->username : "unknown")
                );

                page::redirect("/node/$nid");
            }
        }
    }

}