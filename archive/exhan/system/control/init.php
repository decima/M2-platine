<?php
function system_reset_variables() {
    if(get_magic_quotes_gpc() === 1) {
        $_GET = json_decode(stripslashes(json_encode($_GET, JSON_HEX_APOS)),
                true);
        $_POST = json_decode(stripslashes(json_encode($_POST, JSON_HEX_APOS)),
                true);
        $_COOKIE = json_decode(stripslashes(json_encode($_COOKIE, JSON_HEX_APOS)),
                true);
        $_REQUEST = json_decode(stripslashes(json_encode($_REQUEST,
                                JSON_HEX_APOS)), true);
    }
}

function system_init() {
    $classes = get_all_of_interface('system_module');
    $order_of_execution = array();
    foreach($classes as $class) {
        $key = method_invoke($class, 'priority');
        if(!isset($order_of_execution[$key])) {
            $order_of_execution[$key] = array();
        }
        $order_of_execution[$key] = array_merge_recursive($order_of_execution[$key],
                array($class));
    }
    ksort($order_of_execution);

    foreach($order_of_execution as $key => $values) {
        foreach($values as $value) {
            method_invoke($value, "system_init");
        }
    }
}

function system_module_load() {
    $declared_modules = module_manager::list_of_declared_modules();

    foreach($declared_modules as $module) {
        if($module->module_installed == 1
                && $module->module_enabled == 1) {
            require $module->module_path;
        }else {
            
        }
    }
}

function system_module_action() {
    $declared_modules = module_manager::list_of_declared_modules();
    foreach($declared_modules as $module) {
        if($module->action != null) {
            module_manager::update_action($module->module_name);
            switch($module->action) {
                case constants::get("MM_ACTION_INSTALL"):
                    module_manager::install_module($module->module_name);
                    break;
                case constants::get("MM_ACTION_UNINSTALL"):
                    module_manager::uninstall_module($module->module_name);
                    break;
                case constants::get("MM_ACTION_ENABLE"):
                    module_manager::enable_module($module->module_name);
                    break;
                case constants::get("MM_ACTION_DISABLE"):
                    module_manager::disable_module($module->module_name);
                    break;
                default:
                    throw new InvalidArgumentException("unknown action");
            }
        }
    }
}

function system_update_cache() {
    permission::update_list();
    module_manager::scan_and_update_list();
    theme::scan_theme_folder();
}

function system_theme_action() {
    $declared_themes = theme::list_of_declared_themes();
    foreach($declared_themes as $theme) {
        if($theme->action != null) {
            theme::update_action($theme->theme_name);
            switch($theme->action) {
                case "setdefault":
                    theme::action_set_default($theme->theme_name);
                    break;
                case "disable":
                    theme::action_disable_theme($theme->theme_name);
                    break;
                case "enable":
                    theme::action_enable_theme($theme->theme_name);
                    break;
            }
        }
    }
}

function system_session_start() {
    global $user;
    session_start();
    if(!isset($_SESSION['user']) || !is_object($_SESSION['user'])) {
        $user = new stdClass();
        $user->groups = array(1);
    }else {
        $user = $_SESSION['user'];
    }
}

function system_session_save() {
    global $user;
    if(isset($user) && is_object($user)) {
        $_SESSION['user'] = $user;
    }
}

function system_session_logout() {
    global $user;
    $user = null;
    session_destroy();
}