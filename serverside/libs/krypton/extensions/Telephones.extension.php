<?php

    class Telephones extends ExtensionInterface {
        public static $id = "kr_telephones";
        public static $description = "Telephones description";
        public static $clientSideExtensionUrl = $_SERVER["DOCUMENT_ROOT"]."/modules/app/krypton.app.telephones.js";

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