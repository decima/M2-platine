<?php

class Notification implements SystemModule {

    static $notifications = array();

    public function info() {
        return array(
            "name" => "Notification",
            "readablename" => "Notification"
        );
    }

    public function priority() {
        return 100;
    }

    public function system_init() {
        
    }

    const STATUS_INFO = 1;
    const STATUS_WARNING = 2;
    const STATUS_ERROR = 4;
    const STATUS_SUCCESS = 8;

    public static function statusNotify($message, $type) {
        $t = new stdClass();
        $t->message = $message;
        $t->type = $type;

        self::$notifications[] = $t;
        method_invoke_all("statusNotifier", func_get_args());
    }

    public static function getStatusNotifications() {
        method_invoke_all("getStatusNotifier", array(), true);
        return self::$notifications;
    }

}
