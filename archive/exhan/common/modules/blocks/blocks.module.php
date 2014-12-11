<?php
/**
 * @name : Blocks Module
 * @desc : Blocks Module.
 * @mach : blocks
 * @author : d3cima
 * 
 */
interface block_base {

    public function block_info();
}

class blocks implements common_module, m_install, m_database, m_page {

    public function schema() {
        $schema = array();
        $schema['blocks'] = array(
            "fields" => array(
                "block" => "VARCHAR(255) NOT NULL",
                "module" => "VARCHAR(255) NOT NULL",
                "visibility" => "TEXT",
                "position" => "VARCHAR(200)",
                "callback" => "VARCHAR(512) NOT NULL",
                "access" => "VARCHAR(512) NOT NULL",
                "permission" => "VARCHAR(128) NOT NULL",
            ),
            "pk" => array("block")
        );
        return $schema;
    }

    public function menu() {
        $menu = array();
        $menu["/admin/blocks/"] = array(
            "callback" => "blocks::blockList",
            "access" => "access_granted",
            "permission" => "administrer",
        );
        $menu["/admin/blocks/scan"] = array(
            "callback" => "blocks::scanBlocks",
            "access" => "access_granted",
            "permission" => "administrer"
        );
        $menu["/admin/blocks/%/settings"] = array(
            "callback" => "blocks::blocksettings",
            "access" => "access_granted",
            "permission" => "administrer",
        );
        return $menu;
    }

    public static function scanBlocks() {
        $modules = invokable_modules("block_info");
        $real_blocks = array();
        foreach($modules as $module) {
            $tmp = method_invoke($module, "block_info");
            foreach($tmp as $k => $v) {
                $tmp[$k]["module"] = $module;
                $tmp[$k]["block"] = $k;
            }
            $real_blocks = array_merge($real_blocks, $tmp);
        }

        $saved_blocks = self::get_all_blocks();
        foreach($real_blocks as $blockName => $blockData) {
            $exist = false;
            foreach($saved_blocks as $key => $sb) {
                if($blockName == $sb) {
                    $exist = true;
                    unset($saved_blocks[$key]);
                   // self::update_block($blockName, $blockData);
                    break;
                }
            }
            if(!$exist) {
                self::insert_block($blockData);
            }
        }
        foreach($saved_blocks as $b) {
            self::delete_block($b);
        }
        page::redirect("/admin/blocks");
    }

    public static function insert_block($fields = array()) {
        database::insert("blocks", $fields);
    }

    public static function update_block($blockname, $fields) {
        database::update("blocks", $fields, "block='%bn'",
                array("%bn" => $blockname));
    }

    public static function delete_block($blockname) {
        database::delete("blocks", "block='%bn'", array("%bn" => $blockname));
    }

    public static function blockIsRegistred($blockname) {

        database::fetch(database::select("blocks", array(), "block=%bn",
                        array("%bn" => $blockname)));
    }

    public static function blockList() {
        return printer(self::get_all_blocks());
    }

    public static function blocksettings($block) {
        
    }

    public static function get_all_blocks() {
        return database::fetchAll(database::select("blocks", array("block")),
                        PDO::FETCH_COLUMN);
    }

    public static function get_block($blockname) {
        return database::fetch(
                        database::select("blocks", array(), "block=%block",
                                array(
                            "%block" => $blockname
                                )
                        )
        );
    }

    public static function get_blocks_by_path($url, $place = null) {
        $menu_url = page::menu_get_array_key($url);
        $condition = "(visibility like '%%url%' OR visibility like '%%urk%' OR visibility IS NULL)";
        $params = array("%url" => $url,"%urk" => str_replace("%", "\%",
                    $menu_url));
        if($place != null) {
            $params['%place'] = $place;
            $condition .=" AND position='%place'";
        }
        return database::fetchAll(
                        database::select("blocks", array(), $condition, $params
                        )
        );
    }

    public static function invoke_block($block) {
        if(is_object($block)) $bo = $block;
        else $bo = get_block($block);
        if(isset($bo->access) && $bo->access != null) {
            if(callable_exists($bo->access)) {
                if(call_user_func_array($bo->access, array())) {
                    if(callable_exists($bo->callback)) {
                        return call_user_func_array($bo->callback, array());
                    }
                }
            }
        }
        return null;
    }

}

