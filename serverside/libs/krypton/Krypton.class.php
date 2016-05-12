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
    require_once "serverside/libs/krypton/User.class.php";
    //require_once "serverside/libs/krypton/PropertiesManager.class.php";
    require_once "serverside/libs/krypton/Controller.class.php";
    require_once "serverside/libs/xtemplate/xtemplate.class.php";

    function __autoload($className) {
        //echo("serverside/libs/krypton/modules/".$className.".module.php"."</br>");
        include "serverside/libs/krypton/modules/".$className.".module.php";
    }
} else {

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
        const DB_TYPE_MYSQL = 1;
        const DB_TYPE_ORACLE = 2;

        private static $dbType = self::DB_TYPE_MYSQL;
        private static $title;
        private static $description;
        private $inDebugMode;
        private $inConstructionMode;
        private $template;

        public $modules;
        public $db;

        function __construct($title, $description, $dbType) {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            $this -> modules = new ModuleManager();
            $this -> db = new DBManager();

            //$this -> template = new XTemplate("serverside/templates/application.html");
            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Не задан параметр - наименование приложения");
                return false;
            } else {
                if (gettype($title) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - наименование приложения");
                    return false;
                } else {
                    if ($description != null && gettype($description) != "string") {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - описание приложения");
                        return false;
                    } else {
                        self::$title = $title;
                        self::$description = $description;

                        DBManager::connect($db_host, $db_user, $db_password);
                        DBManager::select_db_mysql("krypton");
                    }
                }
            }
        }



        public static function title ($title) {
            if ($title != null && gettype($title) == "string")
                self::$title = $title;
            return self::$title;
        }


        public static function get_title () {
            return self::$title;
        }



        public static function getDBType () {
            return self::$dbType;
        }


        public function display () {
            //print_r($_SERVER["REQUEST_URI"]);echo("</br>");
            //print_r($_SERVER["SERVER_NAME"]);

            $path = explode("/", $_SERVER["REQUEST_URI"]);
            if ((count($path) == 3 && $path[1] == "admin") || (count($path) == 2 && $path[1] == "admin")) {
                $template_url = "serverside/templates/admin_login.html";
                //header("Content-type: text/css");
            } else
                $template_url = "serverside/templates/application.html";

            //print_r($path);
            //print_r(count($path));
            $this -> template = new XTemplate($template_url);
            $this -> template -> assign("CURRENT_SESSION", json_encode(Session::getCurrentSession()));
            $this -> template -> assign("CURRENT_USER", json_encode(Session::getCurrentUser()));
            $this -> template -> parse("main");
            $this -> template -> out("main");
        }


        public static function install () {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            if (DBManager::connect($db_host, $db_user, $db_password)) {
                if (DBManager::create_db_mysql("krypton")) {
                    if (DBManager::select_db_mysql("krypton")) {
                        //if ( DBManager::set_encoding("utf8")) {
                            echo("Установка Krypton.Core выполнена успешно</br>");
                        //}
                    } else
                        echo("Не удалось выполнить установку Krypton.Core</br>");
                } else
                    echo("Не удалось выполнить установку Krypton.Core</br>");
            }
        }


    };

?>