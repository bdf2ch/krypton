<?php

    interface ExtensionInterface {


        //private static $isModuleInstalled = false;
        //private static $isModuleLoaded = false;
        //private static $isLoaded = false;



        public function init();
        public function install();
        public function isInstalled();


        public function __construct (); //{
            //$class = get_called_class();
            //self::$id = $class;
        //}


        //public static function _init_ () {
        //    $class = get_called_class();
        //    $class::$id = $class;
        //}


        /*
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
        */


        //protected function setInstalled ($flag) {
        //    if ($flag != null && gettype($flag) == "boolean")
        //        self::$isModuleInstalled = $flag;
            //echo("module is installed = ".$flag."</br>");
        //}


        //protected function setLoaded ($flag) {
        //    if ($flag != null && gettype($flag) == "boolean")
        //        self::$isModuleLoaded = $flag;
            //echo("module is loaded = ".$flag."</br>");
        //}


        //public static function isLoaded () {
        //    return self::$isModuleLoaded;
       // }





    };

?>