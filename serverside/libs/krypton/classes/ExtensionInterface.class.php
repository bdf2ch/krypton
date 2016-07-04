<?php

    abstract class ExtensionInterface {
        public static $id;
        public static $description;
        public static $url;
        public static $enabled = true;

        private static $isModuleInstalled = false;
        private static $isModuleLoaded = false;
        private static $isLoaded = false;



        abstract public function init();
        abstract public function install();
        abstract public function isInstalled();


        public function __construct () {
            $class = get_called_class();
            self::$id = $class;
        }


        public static function _init_ () {
            $class = get_called_class();
            $class::$id = $class;
        }


        public function get ($property) {
            $class = get_called_class();

            if ($property == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, $class." -> get: Не задан параметр - наименование свойства");

            if (gettype($property) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, $class." -> get: Невернор задан тип параметра - наименование свойства");

            if (!property_exists($class, $property))
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, $class." -> get: Свойство '".$property."' не найдено");

            return $class::${$property};
        }


        protected function setInstalled ($flag) {
            if ($flag != null && gettype($flag) == "boolean")
                self::$isModuleInstalled = $flag;
            //echo("module is installed = ".$flag."</br>");
        }


        protected function setLoaded ($flag) {
            if ($flag != null && gettype($flag) == "boolean")
                self::$isModuleLoaded = $flag;
            //echo("module is loaded = ".$flag."</br>");
        }


        public static function isLoaded () {
            return self::$isModuleLoaded;
        }



        public function load () {
            $class = get_called_class();

            if (!Krypton::$app -> isExtensionLoaded($class)) {
                $result = DBManager::insert_row(
                    "kr_app_extensions",
                    ["extension_id", "extension_title", "extension_description", "extension_url", "enabled"],
                    ["'".$class::$id."'", "'".$class::$title."'", "'".$class::$description."'", "'".$class::$url."'", 1]
                );
                if (!$result)
                    return Errors::push(Errors::ERROR_TYPE_ENGINE, "Extension -> load: Не удалось подключить расширение '".$class."' к приложению");

                $extension = Models::load("Extension", false);
                $extension -> id -> value = $class::$id;
                $extension -> title -> value = $class::$title;
                $extension -> description -> value = $class::$description;
                $extension -> url -> value = $class::$url;
                array_push(Krypton::$app -> extensions, $extension);
            } else
                Extensions::get($class) -> init();

            return true;
        }


        public function getUrl () {
            return self::$url != "" ? self::$url : false;
        }


        public function setEnabled ($flag) {
            if ($flag == null)
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "ExtensionInterface -> setEnabled");
        }

    };

?>