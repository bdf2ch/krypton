<?php

    abstract class ExtensionInterface {
        public static $id;
        public static $description;
        public static $url;
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


        public static function get ($property) {
            if ($property == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, get_called_class()." -> get: Не задан параметр - наименование свойства");
            else {
                if (gettype($property) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, get_called_class()." -> get: Невернор задан тип параметра - наименование свойства");
                else {
                    if (property_exists(get_called_class(), $property) == false)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, get_called_class()." -> get: Свойство '".$property."' не найдено");
                    else {
                        $result = false;
                        $class = get_called_class();
                        switch ($property) {
                            case "id":
                                $result = $class::$id;
                                break;
                            case "description":
                                $result = $class::$description;
                                break;
                            case "clientSideExtensionUrl":
                                $result = $class::$clientSideExtensionUrl;
                                break;
                        }
                        return $result;
                    }
                }
            }
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
            //var_dump($class);
            $result = DBManager::select("kr_app_extensions", ["*"], "''");
            if ($result != false)
                foreach ($result as $key => $ext) {
                    $extension = Models::construct("Extension", false);
                    $extension -> fromSource($ext);
                    if ($extension -> id -> value == $class)
                        return true;
                }
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

            return true;
        }


        public function getUrl () {
            return self::$url != "" ? self::$url : false;
        }

    };

?>