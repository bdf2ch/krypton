<?php

    function __autoload ($className) {

    }

    abstract class Module {
        private $title;
        private $description;
        private $version;
        private $clientSideModule = "";

        public function __construct($moduleTitle, $moduleDescription) {
            if ($moduleTitle != null && gettype($moduleTitle) == "string") {
                $this -> title = $moduleTitle;
                $this -> clientSideModule = "krypton."$this -> title.".js"
            }
            if ($moduleDescription != null && gettype($moduleDescription) == "string")
                $this -> description = $moduleDescription;
        }

        abstract protected function init();
        public function install();
    };

?>