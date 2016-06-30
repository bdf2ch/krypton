<?php

    class Extensions extends Service {
        public static $items = array();

        public function __construct () {
            parent::__construct();
        }


        /**
        * Возвращает расширение с заданным идентификатором
        * @id - идентификатор расширения
        **/
        public static function get ($id) {
            if ($id == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extensions -> get: Не задан параметр - идентификатор расширения");

            if (gettype($id) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extensions -> get: Неверно задан тип параметра - идентификатор расширения");

            foreach (self::$items as $key => $ext) {
                if ($ext -> id == $id)
                    return $ext;
            }

            return false;
        }



        public static function getAll () {
            return self::$items;
        }


        public static function install () {}

        public static function init () {
            Services::register(get_called_class());
            foreach (Krypton::$extensions as $key => $ext) {
                $extension = new $ext();
                $extension::init();
            }
        }



        public static function load ($extension) {
            if ($extension == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extensions -> load: Не задан парметр - наименование расширения");

            if (gettype($extension) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extensions -> load: Неверно задан тип парметра - наименование расширения");

            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$extension.".extension.php")) {
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$extension.".extension.php";
                $ext = new $extension();
                if (!$ext::isInstalled())
                    $ext::install();
                $ext::init();
                $item = new Extension($ext::$id, $ext::$title, $ext::$url, $ext::$description);
                array_push(self::$items, $item);
                return true;
            } else
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Extensions -> load: Расширение '".$extensionTitle."' не найдено");
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