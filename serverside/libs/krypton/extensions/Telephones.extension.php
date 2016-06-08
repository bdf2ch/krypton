<?php

    class Telephones extends ExtensionInterface {
        public static $id = "kr_telephones";
        public static $description = "Telephones description";
        public static $clientSideExtensionUrl = "modules/app/krypton.app.telephones.js";

        public function __construct () {
            //parent::__construct(self::$id, self::$description, self::$clientSideExtensionUrl);
        }

        public static function install () {}

        public static function isInstalled () {}

        public static function init () {
            if ($clientSideExtensionUrl != null && $clientSideExtensionUrl != "") {
                if (gettype($this -> clientSideExtensionUrl) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Telephones -> init: Неверно задан тип переменной - url модуля клиентской части приложения");
                } else {

                }
            } else
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Telephones -> init: Не задан url модуля клиентской части приложения");
        }
    };

?>