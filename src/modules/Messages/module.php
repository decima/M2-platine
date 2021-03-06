<?php

/**
 * @moduleName Messages
 *
 *
 * */
class Messages implements Module
{

    public function info()
    {
        return [
            "name"         => "Messages",
            "readablename" => "Messagerie",
        ];
    }

    public function schema($schema = [])
    {
        $schema["messages"] = [
            "mid"     => Database::FIELD_TYPE_INT + Database::PRIMARY_KEY + Database::AUTOINCREMENT,
            "sid"     => Database::FIELD_TYPE_INT + Database::NOTNULLVAL,
            "rid"     => Database::FIELD_TYPE_INT + Database::NOTNULLVAL,
            "message" => Database::FIELD_TYPE_TEXT,
            "sent_on" => Database::FIELD_TYPE_INT + Database::NOTNULLVAL,
            "read"    => Database::FIELD_TYPE_INT + Database::NOTNULLVAL,
        ];
        return $schema;
    }

    public function menu($item = [])
    {
        $item['/messages'] = [
            "access"   => "access content",
            "callback" => ["Messages", "discussions"],
        ];
        $item['/messages/@'] = [
            "access"   => "access content",
            "callback" => ["Messages", "chat"],
        ];
        $item['/messages/@/ajax'] = [
            "access"   => "access content",
            "callback" => ["Messages", "ajax_chat"],
        ];

        $item['/messages/@/ajax/send'] = [
            "access"   => "access content",
            "callback" => ["Messages", "ajax_post_message"],
        ];

        return $item;
    }

    public static function ajax_chat($id)
    {
        header("content-type: application/json");
        $page = 0;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        $r = MessagesDB::getConversation(User::get_user_logged_id(), $id, $page);
        foreach ($r as $t => $v) {
            $user = new UserObject();
            $user->load($v->sid);
            $r[$t]->avatar = $user->get_avatar();
        }


        return json_encode($r);
    }

    public function ajax_post_message($id)
    {
        if (isset($_POST['message'])) {
            MessagesDB::submitMessage(User::get_user_logged_id(), $id, htmlentities($_POST['message']));
        } elseif (isset($_GET['message'])) {

            MessagesDB::submitMessage(User::get_user_logged_id(), $id, htmlentities($_GET['message']));
        }
    }

    public static function chat($id)
    {
        $u = new UserObject();
        $u->load($id);
        $theme = new Theme();
        $theme->set_title(t("Messagerie · %first %last", ["%first" => $u->firstname, "%last" => $u->lastname]));
        $messages = MessagesDB::getConversation(User::get_user_logged_id(), $id);

        $script = "<script src='" . Page::url("/modules/Messages/chat.js") . "'></script>";
        $theme->add_to_body($script);
        $user = User::get_user_logged();
        $form = '<div method="post" id="messaging" class="actualite">
            <div class="actualite_avatar_area">
                <div class="actualite_avatar avatar" style="background-image:url(' . User::get_user_logged_avatar() . ');">            </div>
            </div>
            <div class="quoi_de_neuf_bloc">
                <textarea class="actualite_area_text" placeholder="Saisissez votre message"></textarea>
                <a class="btn actualite_btn">Envoyer</a>
            </div>
            </div>
            <div class="clear"></div>
        </div>';

        $theme->add_to_body($form);
        $theme->process_theme(Theme::STRUCT_DEFAULT);
    }

    public static function discussions()
    {
        if (User::get_user_logged_id() != null) {
            $messages = MessagesDB::getDiscussions(User::get_user_logged_id());
            $theme = new Theme();
            $theme->set_title(t("Messagerie"));

            foreach ($messages as $m) {
                $user = new UserObject();
                $user->load($m->conversation);
                $messagetype = "";
                if ($m->sid == $m->conversation && $m->read == 0) {
                    $messagetype = '<div class="messagerie_bloc_icone"><i class="fa fa-envelope fa-fw" title="Message lu"></i></div>';
                } else if ($m->sid == $m->conversation && $m->read == 1) {
                    $messagetype = '<div class="messagerie_bloc_icone"><i class="fa fa-envelope fa-fw" title="Message lu"></i></div>';
                } elseif ($m->rid == $m->conversation && $m->read == 0) {
                    $messagetype = '<div class="messagerie_bloc_icone"><i class="fa  fa-reply fa-fw" title="Réponse envoyée"></i></div>';
                } else {
                    $messagetype = '<div class="messagerie_bloc_icone"><i class="fa  fa-check fa-fw" title="Réponse envoyée et lu"></i></div>';
                }

                $theme->add_to_body('<div class="messagerie">
            <div class="messagerie_avatar_area">
                <div class="messagerie_avatar avatar" style="background-image:url(' . $user->get_avatar() . ')">
                </div>
                <div class="messagerie_nom"><a>' . $user->firstname . ' <br/>' . $user->lastname . '</a></div>
            </div>
            <div class="messagerie_bloc ' . ($m->read == 0 && $m->sid == $m->conversation ? "messagerie_bloc_new" : "") . '" onclick="window.location.href=\'' . Page::url("/messages/" . $m->conversation) . '\'">
                <div class="messagerie_bloc_informations"><span>' . $user->firstname . ' ' . $user->lastname . '</span> : 
                    <div class="messagerie_bloc_informations_date"><i class="fa  fa-clock-o fa-fw"></i> ' . date(t("d-m-Y à H:i"), $m->sent_on) . '</div></div>
                <div class="messagerie_bloc_texte">
                    <div class="messagerie_bloc_texte_inside">' . $m->message . '</div>
                </div>' . $messagetype . '</div>
            <div class="clear"></div>
        </div>');
            }

            $theme->process_theme(Theme::STRUCT_DEFAULT);
        }
    }

    public function hook_has_new_messages()
    {
        $e = MessagesDB::getUnreadMessages(User::get_user_logged_id());
        return $e;
    }

}

class MessagesDB
{

    public static function getUnreadMessages($uid, $uid2 = null)
    {
        $tbl = CONFIG_DB_PREFIX . "messages";
        $t = " ";
        if ($uid2 != null) {
            $t = "and sid=$uid2";
        }
        $s = "select count(*) from $tbl where rid=$uid $t and `read`=0";
        $e = Database::getValue($s);
        return $e != false ? $e : 0;
    }

    public static function getConversation($uid, $uid2, $page = 0)
    {
        $tbl = CONFIG_DB_PREFIX . "messages";
        $tbl2 = CONFIG_DB_PREFIX . "user";
        $int = 10;
        $page = $page * $int;
        $tcount = self::getUnreadMessages($uid, $uid2);

        Database::execute("UPDATE $tbl set `read`=1 where rid=$uid and sid=$uid2");
        if ($tcount > 10) {
            $int = $tcount;
        }
        $request = "SELECT u1.firstname as sender_firstname, u1.lastname as sender_lastname, u2.firstname as receiver_firstname, u2.lastname as receiver_lastname, m.* from $tbl m join $tbl2 u1 on m.sid = u1.uid JOIN $tbl2 u2 on m.rid = u2.uid where (m.sid=$uid AND m.rid=$uid2) OR (m.sid=$uid2 AND m.rid=$uid) order by m.sent_on DESC LIMIT $page,$int";
        $rest = Database::getAll($request);
        foreach ($rest as $k => $r) {
            $rest[$k]->sent_on = date("d/m/Y à H:i", $r->sent_on);
        }

        return $rest;
    }

    public static function getDiscussions($uid)
    {
        $messages = [];
        $messagers = [];
        $tbl = CONFIG_DB_PREFIX . "messages";
        $request = "SELECT m1.*
FROM $tbl m1 LEFT JOIN $tbl m2
 ON (m1.sid = m2.sid AND m1.rid=m2.rid AND m1.sent_on < m2.sent_on)
WHERE m2.mid IS NULL and m1.sid=$uid OR m1.rid=$uid ORDER BY m1.sent_on DESC;";
        $results = Database::getAll($request);
        foreach ($results as $r) {
            if ($r->sid != $uid) {
                if (!in_array($r->sid, $messagers)) {

                    $r->conversation = $r->sid;
                    $messagers[] = $r->sid;
                    $messages[] = $r;
                }
            } elseif ($r->rid != $uid) {
                if (!in_array($r->rid, $messagers)) {

                    $r->conversation = $r->rid;
                    $messagers[] = $r->rid;
                    $messages[] = $r;
                }
            } else {
                if (!in_array($r->rid, $messagers)) {

                    $r->conversation = $r->rid;
                    $messagers[] = $r->rid;
                    $messages[] = $r;
                }
            }
        }
        return $messages;
    }

    public static function submitMessage($sid, $rid, $message)
    {
        $a = ["sid" => $sid, "rid" => $rid, "message" => $message, "sent_on" => time(), "read" => 0];
        Database::insert("messages", $a);
    }

}
