<?php

    abstract class Module {
        private $id;
        private $description;
        private $version;
        private $errors = array();
        private static $controller;
        private static $isModuleInstalled = false;
        private static $isModuleLoaded = false;

        public function __construct() {
            //if ($moduleTitle != null && gettype($moduleTitle) == "string") {
            //    $this -> title = $moduleTitle;
            //    $this -> clientSideModule = "krypton.".$this -> title.".js";
            //}
            //if ($moduleDescription != null && gettype($moduleDescription) == "string")
            //    $this -> description = $moduleDescription;
            self::$controller = new Controller();
        }

        abstract public function init();
        abstract public static function install();
        abstract public static function isInstalled();


        protected function setInstalled ($flag) {
            if ($flag != null && gettype($flag) == "boolean")
                self::$isModuleInstalled = $flag;
            echo("module is installed = ".$flag."</br>");
        }


        protected function setLoaded ($flag) {
            if ($flag != null && gettype($flag) == "boolean")
                self::$isModuleLoaded = $flag;
            echo("module is loaded = ".$flag."</br>");
        }


        public static function isLoaded () {
            return self::$isModuleLoaded;
        }
    };

?>