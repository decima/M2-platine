<?php

ini_set("display_errors", true);

require_once 'Hooks.php';

class Test1 implements Plugin {

    public function info() {

        return array("Test1", "Test 1");
    }

}

class Test2 implements Plugin {

    public function info() {
        $p = array("Test2", "Test 2");
        $p->add_required_plugins("Test1");
        return $p;
    }

}

class Test3 implements Core_Plugin {

    public function info() {

        return array(
            "name" => "Test3",
            "core_version" => 1.00,
            "version" => 1.00
        );
    }

    public function init() {
        return "I'm a lady initialized!";
    }

}
?>

<?php

echo "<pre>";

$h = new Hooks("admin_muffin_info");

/*
$h->change_plugin_type(Hooks::MUFFINS_ADMIN);

$muffins = $h->invoke_hooks(array(), Hooks::RETURN_MERGE);
print_r($muffins);
*/

$hook = new Hooks("info");
print_r($hook->invoke_hooks());



print_r($hook->invoke_hook("Test1"));

$hook = new Hooks("init");
$hook->change_plugin_type(Hooks::CORE_PLUGINS);
print_r($hook->invoke_hooks());

echo "<------------->";

$hook = new Hooks("info");
print_r($hook->invoke_hook("Test3"));

