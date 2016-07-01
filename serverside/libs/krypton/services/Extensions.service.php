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


            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$id.".extension.php")) {
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$id.".extension.php";
                $extension = new $id();
                return $extension;
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
                //var_dump($ext);
                $result = DBManager::insert_row(
                    "kr_app_extensions",
                    ["extension_id", "extension_title", "extension_description", "extension_url", "enabled"],
                    ["'".$ext::$id."'", "'".$ext::$title."'", "'".$ext::$description."'", "'".$ext::$url."'", 1]);

                if (!result)
                    return Errors::push(Errors::ERROR_TYPE_ENGINE, "Extensions -> load: не удалось добавить информацию о загружаемом расширении в БД");

                if (!$ext::isInstalled())
                    $ext::install();
                $ext::init();
                $item = new Extension($ext::$id, $ext::$title, $ext::$url, $ext::$description);
                array_push(self::$items, $item);
                return true;
            } else
                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Extensions -> load: Расширение '".$extensionTitle."' не найдено");
        }


        public static function isLoaded ($extension) {
            if ($extension == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extensions -> isLoaded: Не задан параметр - наименование расширения");

            if (gettype($extension) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Extensions -> isLoaded: Неверно задан тип параметра - наименование расширения");

            /*
            foreach (Krypton::$app -> extensions as $key => $ext) {
                if ($ext -> id -> value === $extension)
                    return true;
            }
            */

            return $extension::isLoaded === true ? true : false;

        }



        public static function getExtensionsUrls () {
            $extensions = "";
            foreach (Krypton::$app -> extensions as $key => $extension) {
                if ($extension -> url -> value != "")
                    $extensions .= "<script src='".$extension -> getUrl()."'></script>\n";
            }
            return $extensions;
        }

    };


?>