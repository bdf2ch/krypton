<?php

//function __autoload($className) {
//    echo("serverside/libs/krypton/modules/".$className.".module.php"."</br>");
//    include "serverside/libs/krypton/modules/".$className.".module.php";
            //throw new Exception("Unable to load $className.");
//}

if (!defined("ENGINE_INSTALL_MODE")) {
    require_once "serverside/libs/krypton/config.php";
    require_once "serverside/libs/krypton/Error.class.php";
    require_once "serverside/libs/krypton/ErrorManager.class.php";
    require_once "serverside/libs/krypton/Module.class.php";
    require_once "serverside/libs/krypton/ModuleManager.class.php";
    require_once "serverside/libs/krypton/DBManager.class.php";
    //require_once "serverside/libs/krypton/SessionManager.class.php";
    require_once "serverside/libs/krypton/Session.class.php";
    //require_once "serverside/libs/krypton/PropertiesManager.class.php";
    require_once "serverside/libs/krypton/Controller.class.php";
    require_once "serverside/libs/xtemplate/xtemplate.class.php";

    function __autoload($className) {
        //echo("serverside/libs/krypton/modules/".$className.".module.php"."</br>");
        include "serverside/libs/krypton/modules/".$className.".module.php";
    }
}







    //function __autoload($className) {
    //    include "serverside/libs/krypton/modules/".$className."module.php";
    //    throw new Exception("Unable to load $className.");
    //}
    //try {
        //$obj = new NonLoadableClass();
    //} catch (Exception $e) {
    //    echo $e -> getMessage(), "\n";
    //}



    class Krypton {
        private static $title;
        private static $description;
        private $inDebugMode;
        private $inConstructionMode;
        private $template;

        public $modules;

        function __construct($title, $description) {
            $this -> modules = new ModuleManager();

            $this -> modules -> load = function ($moduleName) {
                if ($moduleName != null) {
                    if (gettype($moduleName) == "string") {
                        $module = new $moduleName();
                        $module -> init();
                    } else {
                        ErrorManager::add (
                            ERROR_TYPE_ENGINE,
                            ERROR_MODULE_LOAD_WRONG_TITLE_TYPE,
                            "Указан неверный тип параметра при загрузке модуля"
                        ) -> send();
                        return false;
                    }
                } else {
                    ErrorManager::add (
                        ERROR_TYPE_ENGINE,
                        ERROR_MODULE_LOAD_NO_TITLE,
                        "Не указано наименование загружаемого модуля"
                    );
                }
            };

            $this -> modules -> isLoaded = function ($moduleName) {
                if ($moduleName != null) {
                    if (gettype($moduleName) == "string") {

                    } else {
                        ErrorManager::add (
                            ERROR_TYPE_ENGINE,
                            ERROR_MODULE_LOAD_WRONG_TITLE_TYPE,
                            "Указан неверный тип параметра при прорверке загруженного модуля"
                        ) -> send();
                        return false;
                    }
                } else {
                    ErrorManager::add (
                        ERROR_TYPE_ENGINE,
                        ERROR_MODULE_LOAD_NO_TITLE,
                        "Не задан параметр при проверке загруженного модуля"
                    );
                    return false;
                }
            };

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
                    $this -> title = "krypron application";
                }
            } else
                $this -> title = "krypton application";


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

            //DBManager::connect_mysql($db_host, $db_user, $db_password);
            //DBManager::create_db_mysql("krypton");
            //DBManager::select_db_mysql("krypton");
        }


        public function init () {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            DBManager::connect_mysql($db_host, $db_user, $db_password);
            DBManager::select_db_mysql("krypton");
        }


        public static function title ($title) {
            if ($title != null && gettype($title) == "string")
                self::$title = $title;
            return self::$title;
        }


        public static function get_title () {
            return self::$title;
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
            $this -> template = new XTemplate("serverside/templates/application.html");
            $this -> template -> assign("USER_SESSION", json_encode(Session::getCurrent()));
            $this -> template -> parse("main");
            $this -> template -> out("main");
        }


        public static function install () {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            if (DBManager::connect_mysql($db_host, $db_user, $db_password)) {
                if (DBManager::create_db_mysql("krypton")) {
                    if (DBManager::select_db_mysql("krypton")) {
                        if ( DBManager::set_encoding_mysql("utf8")) {
                            echo("Установка Krypton.Core выполнена успешно</br>");
                        }
                    } else
                        echo("Не удалось выполнить установку Krypton.Core</br>");
                } else
                    echo("Не удалось выполнить установку Krypton.Core</br>");
            }

        }
    };

?>