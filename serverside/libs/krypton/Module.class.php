<?php

    abstract class Module {
        private $title;
        private $description;
        private $version;
        private $clientSideModule = "";
        private static $isModuleInstalled = false;
        private static $isModuleLoaded = false;

        //public function __construct($moduleTitle, $moduleDescription) {
        //    if ($moduleTitle != null && gettype($moduleTitle) == "string") {
        //        $this -> title = $moduleTitle;
        //        $this -> clientSideModule = "krypton.".$this -> title.".js";
        //    }
        //    if ($moduleDescription != null && gettype($moduleDescription) == "string")
        //        $this -> description = $moduleDescription;
        //}

        abstract public function init();
        abstract public static function install();


        protected function setInstalled ($flag) {
            if ($flag != null && gettype($flag) == "boolean")
                self::$isModuleInstalled = $flag;
            echo("module is installed = ".$flag."</br>");
        }


        public static function isInstalled () {
            return self::$isModuleInstalled;
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