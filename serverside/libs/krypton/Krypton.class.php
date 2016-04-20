<?php
    require_once "serverside/libs/krypton/config.php";
    require_once "serverside/libs/krypton/Module.class.php";
    require_once "serverside/libs/krypton/utils.php"
    require_once "serverside/libs/xtemplate/xtemplate.class.php";



    function __autoload($className) {
        include "serverside/libs/krypton/modules/".$className."module.php";
        throw new Exception("Unable to load $className.");
    }
    //try {
        //$obj = new NonLoadableClass();
    //} catch (Exception $e) {
    //    echo $e -> getMessage(), "\n";
    //}



    class Krypton {
        //private $errors = array();
        private $errors = new ErrorManager();
        private $modules = array();
        private $title = "";
        private $description = "";
        private $inDebugMode = false;
        private $inConstructionMode = false;
        private $template;

        function __construct($title, $description) {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            $this -> title = $title != null && gettype($title) == "string" ? $title : "";
            $this -> description = $description != null && gettype($description) == "string" ? $description : "";
            //$this -> template = new XTemplate("serverside/templates/application.html");

            /* Установка соединения с БД */
            $link = mysql_connect($db_host, $db_user, $db_password);
            if (!$link) {
                $error = new Error(2, mysql_errno(), mysql_error());
                array_push($this -> errors, $error);
                echo json_encode($this -> errors);
            } else {
                echo 'Connected successfully';
                mysql_close($link);
            }

            $link = connect($db_host, $db_user, $db_password);
            if (isError($link))
                array_push($this -> errors, $link);
            else
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

        public function loadModule ($moduleTitle) {
            if ($moduleTitle != null && gettype($moduleTitle) == "string") {
                $module = new $moduleTitle();
                array_push($this -> modules, $module);
            }
        }

        public function display () {
            $this -> template -> parse("main");
            $this -> template -> out("main");
        }
    };

?>