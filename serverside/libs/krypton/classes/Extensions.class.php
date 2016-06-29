<?php

    class Extensions {
        public static $items = array();



        public static function getAll () {
            return self::$items;
        }


        public static function init () {
            foreach (Krypton::$extensions as $key => $ext) {
                $extension = new $ext();
                $extension::init();
            }
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
                    $extensionItem = new Extension($extension::$id, $extension::$title, $extension::$url, $extension::$description);
                    array_push(self::$items, $extensionItem);
                    //echo("from extensions</br>");
                    $extension::init();
                    return true;
                }
            }
        }



        public static function getExtensionsUrls () {
            $extensions = "";
            for ($i = 0; $i < sizeof(self::$items); $i++) {
                if (self::$items[$i] -> url != null && self::$items[$i]  -> url != "")
                    $extensions .= "<script src='".self::$items[$i] -> getUrl()."'></script>\n";
            }
            return $extensions;
        }

    };

?>