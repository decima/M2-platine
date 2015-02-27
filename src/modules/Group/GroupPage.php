<?php

class GroupModulePages {

    public static function list_of_groups() {
        if (isset($_GET['err'])) {
            switch ($_GET['err']) {
                case "reserved":
                    Notification::statusNotify(t("ce nom est reservé au système"), Notification::STATUS_ERROR);

                    break;
                case "exists":
                    Notification::statusNotify(t("ce groupe existe déjà"), Notification::STATUS_ERROR);

                    break;
                default:
                    Notification::statusNotify(t("une erreur est survenue"), Notification::STATUS_ERROR);

                    break;
            }
        }
        $groups = GroupObject::loadAll();
        $theme = new Theme();
        $theme->set_title(t("Groupes"));
        $rows = array();

        foreach ($groups as $g) {
            $rows[] = array($g->gid, $g->label, Theme::linking(Page::url("/admin/groups/" . $g->label), t("details")), Theme::linking(Page::url("/admin/groups/confirm/" . $g->label), t("supprimer")));
        }

        $head = array(t("identifiant du groupe"), t("nom du group"), t("actions"));
        $form = new Form("POST", Page::url("/admin/groups/create"));
        $form->addElement(new InputElement("group_name", t("nom du groupe"), ""));
        $form->addElement(new InputElement("add-group", "", t("créer le groupe"), "submit"));
        $theme->add_to_body(Theme::forming($form), t("Créer un nouveau groupe"));

        $theme->add_to_body(Theme::tabling($rows, $head), t("Liste des groupes existants"));
        $theme->process_theme(Theme::STRUCT_ADMIN);
    }

    public static function list_of_members($label) {
        $group = new GroupObject();
        $view = array();
        if ($group->load_by_label($label)) {
            $group->load_members();
            $members = $group->members();
            $rows = array(
            );
            $theme = new Theme();

            foreach ($members as $k => $v) {
                $rows[] = array($k, $v->firstname, $v->lastname, $theme->linking(Page::url("/admin/groups/$label/delete/$k"), t("retirer du groupe")));
            }

            $form = new Form("POST", Page::url("/admin/groups/$label/add"));
            $selector = new FormElement("select", "userid", t("selectionnez un utilisateur"));
            $users = UserObject::loadAll();
            foreach ($users as $u) {
                $selector->addElement(new FormElement("option", "", $u->lastname . " " . $u->firstname, $u->uid));
            }

            $form->addElement($selector);
            $form->addElement(new InputElement("add-element", null, t("ajouter un membre"), "submit"));


            $f = $theme->forming($form);
            $theme->set_title(t("Groupe %s", array("%s" => $label)));
            $theme->add_to_body($theme->linking(Page::url("/admin/groups"), t("retourner à la liste des groupes")));

            $theme->add_to_body($f, t("Ajouter un membre au groupe"));

            $theme->add_to_body($theme->tabling($rows, array(t("id"), t("firstname"), t("lastname"), t("actions"))), t("Liste des membres"));

            $theme->process_theme(Theme::STRUCT_ADMIN);
        } else {
            
        }
        return;
    }

    public function confirmDelete($label) {

        $group = new GroupObject();
        if ($group->load_by_label($label)) {
            if (isset($_POST['delete'])) {
                $group = new GroupObject();
                $group->load_by_label($label);
                $group->delete();
                header("Location:" . Page::url("/admin/groups"));
            }
            $theme = new Theme();
            $form = new Form("POST", Page::url("/admin/groups/confirm/$label"));
            $link = new FormElement("a", null, t("Retourner à la liste des groupes"), "", "");
            $link->setAttribute("href", Page::url("/admin/groups/"));
            $form->addElement($link);

            $form->addElement(new InputElement("delete", "", t("supprimer le groupe"), "submit"));
            $theme->add_to_body($theme->forming($form));
            $theme->set_title("Confirmer la suppression du groupe $label");
            $theme->process_theme(Theme::STRUCT_ADMIN);
        } else {
            header("Location:" . Page::url("/admin/groups?err=notexists"));
        }
    }

    public function action($label, $action, $id = 0) {
        if ($action == "add") {
            if (isset($_POST['userid'])) {
                $group = new GroupObject();
                $group->load_by_label($label);
                $group->load_members();
                $group->add_member($_POST['userid']);
            }
        } elseif ($action == "delete") {
            $group = new GroupObject();
            $group->load_by_label($label);
            $group->load_members();
            $group->remove_member($id);
        }


        header("Location:" . Page::url("/admin/groups/$label"));
    }

    public function creategroup() {
        $forbidden = array("create");
        if (isset($_POST['group_name'])) {
            $cname = strtolower($_POST['group_name']);
            if (in_array($cname, $forbidden)) {
                header("Location:" . Page::url("/admin/groups?err=reserved"));
            }
            $group = new GroupObject();
            if ($group->load_by_label($cname)) {
                header("Location:" . Page::url("/admin/groups?err=exists"));
            } else {
                $group->label = $cname;
                $group->save();
                header("Location:" . Page::url("/admin/groups"));
            }
        }
        header("Location:" . Page::url("/admin/groups?err=unknown"));
    }

}
