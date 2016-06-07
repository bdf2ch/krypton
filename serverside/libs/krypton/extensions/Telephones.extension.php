<?php

    class Telephones extends ExtensionInterface {
        public $id = "kr_telephones";
        public $description = "Telephones description";
        public $clientSideExtensionUrl = "modules/app/krypton.app.telephones.js";
        //private static $clientSideModuleUrl = "";

        public function __construct () {
            //parent::__construct(self::$id, self::$description, self::$clientSideExtensionUrl);
        }

        public static function install () {}

        public static function isInstalled () {}

        public static function init () {
            if (self::$clientSideExtensionUrl != null && self::$clientSideExtensionUrl != "") {
                if (gettype(self::$clientSideExtensionUrl) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Telephones -> init: Неверно задан тип переменной - url модуля клиентской части приложения");
                } else {

                }
            } else
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Telephones -> init: Не задан url модуля клиентской части приложения");
        }
    };

?>