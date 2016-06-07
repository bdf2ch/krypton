<?php

    abstract class ExtensionInterface {
        public $id;
        public $description;
        public $clientSideExtensionUrl;
        private static $isModuleInstalled = false;
        private static $isModuleLoaded = false;


        public function __construct() {
        }

        abstract public static function init();
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