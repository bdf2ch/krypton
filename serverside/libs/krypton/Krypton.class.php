<?php
    require_once "serverside/libs/krypton/config.php";
    require_once "serverside/libs/krypton/Error.class.php";
    require_once "serverside/libs/krypton/ErrorManager.class.php";
    require_once "serverside/libs/krypton/Module.class.php";
    require_once "serverside/libs/krypton/ModuleManager.class.php";
    require_once "serverside/libs/krypton/DBManager.class.php";
    require_once "serverside/libs/krypton/SessionManager.class.php";
    require_once "serverside/libs/krypton/PropertiesManager.class.php";
    require_once "serverside/libs/krypton/utils.php";
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
        private $title;
        private $description;
        private $inDebugMode;
        private $inConstructionMode;
        private $template;

        function __construct($title, $description) {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            //$this -> template = new XTemplate("serverside/templates/application.html");

            if ($title != null) {
                if (gettype($title) == "string")
                    $this -> title = $title;
                else {
                    ErrorManager::add (
                        ERROR_TYPE_ENGINE,
                        ERROR_APP_WRONG_TITLE_TYPE,
                        "Указан неверный тип параметра при создании приложения - наименование приложения"
                    );
                    $this -> title = "";
                }
            } else
                $this -> title = "";


            if ($description != null) {
                if (gettype($description) == "string")
                    $this -> title = $title;
                else {
                    ErrorManager::add (
                        ERROR_TYPE_ENGINE,
                        ERROR_APP_WRONG_DESCRIPTION_TYPE,
                        "Указан неверный тип параметра при создании приложения - описание приложения"
                    );
                    $this -> description = "";
                }
            } else
                $this -> description = "";

            DBManager::connect_mysql($db_host, $db_user, $db_password);
            DBManager::create_db_mysql("krypton");
            DBManager::select_db_mysql("krypton");

            SessionManager::init();
            PropertiesManager::init();

            DBManager::disconnect_mysql();

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