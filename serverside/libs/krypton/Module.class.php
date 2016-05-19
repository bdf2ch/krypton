<?php

    abstract class Module {
        private $id;
        private $description;
        private static $controller;
        private static $isModuleInstalled = false;
        private static $isModuleLoaded = false;

        public function __construct() {
            self::$controller = new Controller();
        }

        abstract public function init();
        abstract public static function install();
        abstract public static function isInstalled();


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