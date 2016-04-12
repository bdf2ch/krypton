<?php
    require_once "serverside/libs/xtemplate/xtemplate.class.php";

    class Krypton {
        private $modules = array();
        private $title = "";
        private $description = "";
        private $inDebugMode = false;
        private $inConstructionMode = false;
        private $template;

        function __construct($title, $description) {
            if ($title != null && $title != "")
                $this -> title = $title;
            if ($description != null && $description != "")
                $this -> description = $description;
            $this -> template = new XTemplate("serverside/templates/application.html");
        }

        public function title ($title) {
            if ($title != null && gettype($title) == "string")
                $this -> title = $title;
            return $this -> title;
        }

        public function description ($description) {
            if ($description != null && gettype($description) == "string")
                $this -> description = $description;
            return $this -> description;
        }

        public function debugMode ($flag) {
            if ($flag != null && gettype($flag) == "boolean")
                $this -> inDebugMode = $flag;
        }

        public function constructionMode ($flag) {
            if ($flag != null && gettype($flag) == "boolean")
                $this -> inConstructionMode = $flag;
        }

        public function display () {
            $this -> template -> parse("main");
            $this -> template -> out("main");
        }
    };

?>