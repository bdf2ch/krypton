<?php

    abstract class ExtensionInterface {
        public static $id;
        public static $description;
        public static $clientSideExtensionUrl;
        private static $isModuleInstalled = false;
        private static $isModuleLoaded = false;


        public function __construct() {
        }

        abstract public static function init();
        abstract public static function install();
        abstract public static function isInstalled();


        private static function getClassName () {
            return get_class($this);
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
    };

?>