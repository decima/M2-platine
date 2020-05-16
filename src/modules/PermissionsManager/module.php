<?php

/**
 * @moduleName PermissionsManager
 *
 *
 * */
require_once("PermissionsObject.php");

class PermissionsManager implements Module {

    public function info() {
        return array(
            "name" => "PermissionsManager",
            "readablename" => "Permissions Manager",
            "dependencies" => array("Group"),
        );
    }

    public function menu($items = array()) {
        $items['/admin/permissions'] = array(
            "access" => "administrer",
            "callback" => array("PermissionsManager", "page_permissions")
        );
        return $items;
    }

    public function schema($schema = array()) {
        PermissionObject::schema($schema);
        return $schema;
    }

    public function install() {
        $p = new PermissionsManager();
        $p->scanForPermission();
        $a1 = new PermissionObject();
        if ($a1->loadByName("full access")) {
            $a1->addPermission($a1->pid, 1);
            $a1->addPermission($a1->pid, 2);
        }


        return true;
    }

    public static function page_permissions() {
        if (isset($_POST['save-permissions'])) {
            PermissionObject::removeAllPermissions();
            if (isset($_POST['permission']))
                foreach ($_POST['permission'] as $perm => $groups) {
                    foreach ($groups as $grp => $d) {
                        PermissionObject::addPermission($grp, $perm);
                    }
                }
            Notification::statusNotify(t("configuration enregistrée"), Notification::STATUS_SUCCESS);
        }
        $defined_permissions = PermissionObject::loadAllPermissions();
        $df = array();

        foreach ($defined_permissions as $p) {
            if (!isset($df[$p->pid])) {
                $df[$p->pid] = array();
            }
            $df[$p->pid][$p->gid] = 1;
        }
        $p = new PermissionsManager();
        $permissions = $p->scanForPermission();
        $groups = GroupObject::loadAll();

        $table = array();
        $hcol = array("");
        $hrow = array();
        foreach ($permissions as $u => $t) {
            $hrow[] = $t;

            $row = array();
            foreach ($groups as $gd => $g) {


                if ($u == 0) {
                    $hcol[] = $g->label;
                }
                $pm = new PermissionObject();
                $pm->loadByName($t);

                $tlabel = "permission[" . $g->gid . "][" . $pm->pid . "]";
                if (isset($df[$pm->pid][$g->gid])) {
                    $row[] = "<input type='checkbox' name='$tlabel' id='$tlabel' checked='checked'/>";
                } else {
                    $row[] = "<input type='checkbox'  name='$tlabel' id='$tlabel' />";
                }
            }
            $table[] = $row;
        }
        $theme = new Theme();
        $theme->set_title(t("Permissions déclarées"));
        $table = Themed::tabling($table, $hcol, $hrow);

        $theme->add_to_body("<form method='POST' action=''>$table <input type='submit' name='save-permissions' value='" . t("Enregistrer") . "'/></form>");
        $theme->process_theme(Theme::STRUCT_ADMIN);
    }

    public function scanForPermission() {
        $p = new Page();
        $permissions = array();
        self::scanPermission($p->get_declared_pages(), $permissions);
        $permissions = array_unique($permissions);
        asort(($permissions));
        $real_permissions = array_values($permissions);
        $saved_permission = array();
        $sp = PermissionObject::loadAll();
        $permissions = array();

        foreach ($saved_permission as $t) {
            $saved_permission[$t->permission_name] = $t->pid;
        }
        foreach ($real_permissions as $p) {
            $permissions[] = $p;

            $d = new PermissionObject();
            $d->loadByName($p);
            $d->permission_name = $p;
            if (isset($saved_permission[$p])) {
                unset($saved_permission[$p]);
            }
            $d->save();
        }
        foreach ($saved_permission as $k => $t) {
            $p = new PermissionObject();
            $p->load($t);
            $p->delete();
        }
        return $permissions;
    }

    public static function scanPermission($array, &$result = array()) {
        foreach ($array as $k => $v) {
            if ($k === "access") {
                $result = array_merge($result, array($v));
            } elseif (is_array($v)) {
                self::scanPermission($v, $result);
            }
        }
    }

    public function hook_group_create($gid) {
        $p = new PermissionObject();
        $p->loadByName("full access");
        $p->addPermission($p->pid, $gid);
    }

    public function permissions($permission) {

        if (strtolower($permission) == "full access") {
            return true;
        }
        $s = new stdClass();
        $s->gid = 1;
        $groups = array($s);
        if (User::get_user_logged_id() == 1) {
            return true;
        }
        if (User::get_user_logged_id() != null) {
            $groups = GroupObject::GetAllGroupsMembership(User::get_user_logged_id());
        }
        $p = new PermissionObject();

        if ($p->loadByName($permission)) {
            foreach ($groups as $g) {
                if ($p->groupIsAllowed($g->gid)) {
                    return true;
                }
            }
        }
        return false;
    }

}
