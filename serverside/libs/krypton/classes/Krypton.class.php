<?php

//function __autoload($className) {
//    echo("serverside/libs/krypton/modules/".$className.".module.php"."</br>");
//    include "serverside/libs/krypton/modules/".$className.".module.php";
            //throw new Exception("Unable to load $className.");
//}
require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/xtemplate/xtemplate.class.php";
if (!defined("ENGINE_INSTALL_MODE")) {
    /*
    require_once "serverside/libs/krypton/config.php";
    require_once "serverside/libs/krypton/Error.class.php";
    require_once "serverside/libs/krypton/Errors.class.php";
    require_once "serverside/libs/krypton/Module.class.php";
    require_once "serverside/libs/krypton/ModuleManager.class.php";

    require_once "serverside/libs/krypton/ControllerAction.class.php";
    require_once "serverside/libs/krypton/Controller.class.php";

    require_once "serverside/libs/krypton/DBManager.class.php";
    //require_once "serverside/libs/krypton/SessionManager.class.php";
    require_once "serverside/libs/krypton/Session.class.php";
    require_once "serverside/libs/krypton/Sessions.class.php";
    require_once "serverside/libs/krypton/User.class.php";
    require_once "serverside/libs/krypton/Setting.class.php";
    require_once "serverside/libs/krypton/Settings.class.php";
    //require_once "serverside/libs/krypton/SettingManager.class.php";
    require_once "serverside/libs/krypton/Controller.class.php";
    require_once "serverside/libs/xtemplate/xtemplate.class.php";
    */

    /*
    function __autoload($className) {
        $modulePos = strpos($className, "Module");
        if ($modulePos != false) {
            $moduleTitle = substr($className, 0, $modulePos);
            require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/modules/".$moduleTitle.".module.php";
        } else {
             require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/".$className.".class.php";
        }

        //echo("serverside/libs/krypton/modules/".$className.".module.php"."</br>");
        //include "serverside/libs/krypton/modules/".$className.".module.php";
    }
    */
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
        const DATA_TYPE_INTEGER = 1;
        const DATA_TYPE_STRING = 2;
        const DATA_TYPE_BOOLEAN = 3;
        const DATA_TYPE_FLOAT = 4;

        private static $dbType = self::DB_TYPE_MYSQL;
        public static $title;
        public static $description;
        public static $inDebugMode;
        public static $inConstructionMode;
        public static $info;
        private $template;

        public static $settings;
        public $db;

        public static $extensions = array();


        function __construct($title, $description, $dbType) {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            session_start();
            DBManager::connect($db_host, $db_user, $db_password);
            DBManager::select_db("krypton");

            Settings::init();
            Sessions::init();

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
                        self::$info = self::getAppInfo();
                        if (self::$info != false) {
                            if (self::$info["title"] != $title) {
                                Settings::setByCode("app_title", $title);
                                self::setTitle($title);
                            }
                            if (self::$info["description"] != $description) {
                                Settings::setByCode("app_description", $description);
                            }
                        }
                        self::$title = $title;
                        self::$description = $description;
                    }
                }
            }
        }


        public static function getAppInfo () {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            //if (!DBManager::is_connected()) {
                //if (DBManager::connect($db_host, $db_user, $db_password)) {
                    //if(DBManager::select_db("krypton")) {
                        $appInfo = DBManager::select("kr_app_info", ["*"], "''");
                        var_dump($appInfo);
                        if ($appInfo != false) {
                            self::$title = $appInfo[0]["title"];
                            self::$description = $appInfo[0]["description"];
                            self::$inDebugMode = boolval($appInfo[0]["is_in_debug_mode"]);
                            self::$inConstructionMode = boolval($appInfo[0]["is_in_construction_mode"]);
                        }
                        return $appInfo[0];
                    //}
                //}
            //}
        }



        public static function title ($title) {
            if ($title != null && gettype($title) == "string")
                self::$title = $title;
            return self::$title;
        }



        public static function get_title () {
            return self::$title;
        }


        public static function setTitle ($title) {
            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> setTitle: Не задан параметр - наименование приложения");
                return false;
            } else {
                if (gettype($title) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> setTitle: Неверно задан тип параметра - наименование приложения");
                    return false;
                } else {
                    DBManager::update("kr_app_info", ["title"], ["'".$title."'"], "");
                }
            }
        }


        public static function setDescription ($description) {

        }



        public static function getDBType () {
            return self::$dbType;
        }



        public function start () {
            $path = explode("/", $_SERVER["REQUEST_URI"]);
            if ((count($path) == 3 && $path[1] == "admin") || (count($path) == 2 && $path[1] == "admin")) {
                $template_url = "serverside/templates/admin_login.html";
            } else
                $template_url = "serverside/templates/application.html";



            //echo("libs = ".Extensions::getClientSideExtensions()."</br>");

            $this -> template = new XTemplate($template_url);
            $this -> template -> assign("APPLICATION_TITLE", Krypton::$title);
            $this -> template -> assign("APPLICATION", json_encode(self::getAppInfo()));
            $this -> template -> assign("EXTENSIONS", json_encode(Extensions::getAll()));
            $this -> template -> assign("CURRENT_SESSION", json_encode(Sessions::getCurrentSession()));
            $this -> template -> assign("CURRENT_USER", json_encode(Sessions::getCurrentUser()));
            $this -> template -> assign("SETTINGS", json_encode(Settings::getAll()));
            $this -> template -> assign("ERRORS", json_encode(Errors::getAll()));
            $this -> template -> assign("CLIENT_SIDE_EXTENSIONS", Extensions::getClientSideExtensions());
            $this -> template -> parse("main");
            $this -> template -> out("main");
        }



        public static function install () {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            if (DBManager::connect($db_host, $db_user, $db_password)) {
                if (DBManager::create_db("krypton")) {
                    if (DBManager::select_db("krypton")) {
                        if (!DBManager::is_table_exists("kr_app_info")) {
                            if (DBManager::create_table("kr_app_info")) {
                                if (DBManager::add_column("kr_app_info", "title", "varchar(200) NOT NULL default ''") &&
                                    DBManager::add_column("kr_app_info", "description", "varchar(200) default ''") &&
                                    DBManager::add_column("kr_app_info", "is_in_debug_mode", "int(11) NOT NULL default 0") &&
                                    DBManager::add_column("kr_app_info", "is_in_construction_mode", "int(11) NOT NULL default 0")
                                ) {
                                    if (DBManager::insert_row("kr_app_info", ["title", "description", "is_in_debug_mode", "is_in_construction_mode"], ["''", "''", 0, 0]) != false)
                                        return true;
                                    else {
                                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить строку в таблицу с информацией о приложении");
                                        return false;
                                    }
                                } else {
                                    Errors::push(Errors::Error_TYPE_ENGINE, "Krypton -> install: Не удалось создать структуру таблицы с информацией о приложении");
                                    return false;
                                }
                            } else {
                                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось создать таблицу с информацией о приложении");
                                return false;
                            }
                        }

                        if (Settings::install() == true) {
                            echo("Установка подсистемы настроек выполнена успешно</br>");
                            return true;
                        }
                        if (Sessions::install() == true) {
                            echo("Установка подсистемы учета сессий выполнена успешно</br>");
                            return true;
                        }
                    } else {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось выбрать БД");
                        return false;
                    }
                } else {
                    Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось создать БД");
                    return false;
                }
            } else {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось подключиться к БД");
                return false;
            }
        }


    };

?>