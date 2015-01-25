<?php
/**
 * @moduleName LightNotification
 * 
 * 
 **/
class LightNotification implements Module {

    static $notifications = array();

    public function info() {
        _Security::version(0, 1);
        return array(
            "name" => "LightNotification",
            "readablename" => "LightNotification"
        );
    }

    public function statusNotifier($message, $type) {
        self::$notifications[] = array("message" => $message, "type" => $type);
    }

    public function getStatusNotifier(){
        return self::$notifications;
    }
}
