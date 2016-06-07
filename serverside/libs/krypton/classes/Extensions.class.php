<?php

    class Extensions {
        private static $items = array();


        public static function getAll () {
            return self::$items;
        }


        public static function load ($extensionTitle) {
            if ($extensionTitle == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extensions -> load: Не задан парметр - наименование расширения");
                return false;
            } else {
                if (gettype($extensionTitle) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extensions -> load: Неверно задан тип парметра - наименование расширения");
                    return false;
                } else {
                    array_push(Krypton::$extensions, $extensionTitle);
                    $extension = new $extensionTitle();
                    var_dump($extension);
                    $extensionItem = new Extension($extension -> $id, $extension -> $description, $extension -> $clientSideExtensionUrl);
                    var_dump($extensionItem);
                    array_push(self::$items, $extensionItem);
                    $extension::init();
                    return true;
                }
            }
        }

    };

?>