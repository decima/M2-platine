<?php

/**
 * @moduleName Publication
 *
 *
 * */
require_once("QRender.php");
require_once("PublicationObject.php");

class Publication implements Module {

    public function info() {

        return array(
            "name" => "Publication",
            "readablename" => "Publications and sharings",
            "dependencies" => array("User", "Group", "VGroups"),
        );
    }

    Public function schema($item = array()) {
        return PublicationObject::schema($item);
    }

    public function hook_timeline() {
        if (isset($_POST['send'])) {
            $result = $this->render_form_submit("Text");
            $f = new PublicationObject();
            $f->content = $result;
            $f->qrender = "Text";
            $f->save();
        }
        $form_publish = '<div class="actualite">
                <div class="actualite_avatar_area">
                    <div class="actualite_avatar">
                        <a href="profil_patrick_jane.html"><img src="../images/patrick_jane_m.png" alt=""></a>
                    </div>
                </div>
                <div class="quoi_de_neuf_bloc">
                    <form method="POST" action="">' . $this->render_form("Text") . '
                    <input type="submit" value="publier" name="send"/></form>
                <div style="position: absolute; display: none; word-wrap: break-word; white-space: pre-wrap; border-left: 0px none rgb(51, 51, 51); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; font-weight: 400; width: 510px; font-family: monospace; line-height: 14px; font-size: 12px; padding: 10px;">&nbsp;</div></div>
                <div class="clear"></div>
            </div>';
        $friendshipO = new FriendshipObject();
        $friends = $friendshipO->loadAllFriends(User::get_user_logged_id());
        $friends[] = user::get_user_logged_id();
        $f = PublicationObject::loadAllByFriendship($friends);
        $content_publish = "";
        foreach ($f as $t) {
            $content_publish .='<div class="actualite">
                <div class="actualite_avatar_area">
                    <div class="actualite_avatar">
                        <i class="fa fa-user fa-4x"></i>
                    </div>
                    <div class="actualite_nom"><a href="profil_patrick_jane.html">Patrick<br>Jane</a></div>
                </div>
                <div class="actualite_bloc">
                    <div class="actualite_area">
                        <div class="actualite_area_text">
' . $this->render_render($t->qrender, $t->content) . '                           
</div>
                    </div>
                    <div class="actualite_buttons">
                        
                        <div class="actualite_date"><i class="fa fa-calendar fa-fw"></i> '.date("H:i:s d/m/Y",$t->date_published).'</div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>';
        }
        return array($form_publish, '<div class="page_contenu_sep"></div>', $content_publish);
    }

    public function menu($items = array()) {

        $items['ajax/publish-form/@'] = array(
            "access" => "publish",
            "callback" => array("Publication", "render_form"),
        );
        return $items;
    }

    public function render_form($type) {
        $type = ucfirst($type);
        $type .= "_QRender";
        if (class_exists($type)) {
            $fe = new $type();
            return $fe->form();
        }
        return "e5001_missing_type_publication";
    }

    public function render_form_submit($type) {
        $type = ucfirst($type);
        $type .= "_QRender";
        if (class_exists($type)) {
            $fe = new $type();
            return $fe->form_treatment();
        }
        return "e5001_missing_type_publication";
    }

    public function render_render($type, $content) {
        $type = ucfirst($type);
        $type .= "_QRender";
        if (class_exists($type)) {
            $fe = new $type();
            return $fe->render(json_decode($content));
        }
        return "e5001_missing_type_publication";
    }

}
