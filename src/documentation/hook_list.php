<?php

interface all_hooks_declared {

    /**
     * method called on module installation
     * @return TRUE to complete the installation, FALSE to abort the installation
     */
    public function install();

    /**
     * method called on module activation
     * @return TRUE to complete the activation, FALSE to abort the activation
     */
    public function enable();

    /**
     * method called on module desactivation
     * @return TRUE to complete the desactivation, FALSE to abort the desactivation
     */
    public function disable();

    /**
     * method called on module uninstallation
     * @return TRUE to complete the uninstallation, FALSE to abort the uninstallation
     */
    public function uninstall();

    /**
     * declare the scheme of the tables in the database
     * @return array containing the scheme of the different tables of the database
     */
    public function schema();

    /**
     * on module installation
     * @param String $module_name the name of the module
     */
    public function hook_module_install($module_name);

    /**
     * on module eanbled
     * @param String $module_name the name of the module
     */
    public function hook_module_enable($module_name);

    /**
     * on module disabled
     * @param String $module_name the name of the module
     */
    public function hook_module_disable($module_name);

    /**
     * on module uninstallation
     * @param String $module_name the name of the module
     */
    public function hook_module_uninstall($module_name);

    /**
     * on user creation
     * @param int $user_id the id of the created user
     */
    public function hook_user_create($user_id);

    /**
     * on group creation
     * @param int $group_id the id of the created group
     */
    public function hook_group_create($group_id);

    /**
     * on view profile of a user
     * @param int $user_id the id of the profile viewed.
     */
    public function hook_profile_view($user_id);
}
