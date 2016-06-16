<?php

    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";
    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/xtemplate/xtemplate.class.php";



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

        public static $app;
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
            Users::init();
            Sessions::init();

            if ($title == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Не задан параметр - наименование приложения");
            else {
                if (gettype($title) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - наименование приложения");
                else {
                    if ($description != null && gettype($description) != "string")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - описание приложения");
                    else {
                        self::$app = new Application();
                        self::$app -> init();

                        if (self::$app -> title != $title) {
                            Settings::setByCode("app_title", $title);
                            self::$app -> set("title", $title);
                        }

                        if (self::$app -> description != $description) {
                            Settings::setByCode("app_description", $description);
                            self::$app -> set("description", $description);
                        }
                    }
                }
            }
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

            Sessions::login("kolu0897", "zx12!@#$");


            $this -> template = new XTemplate($template_url);
            $this -> template -> assign("APPLICATION_TITLE", self::$app -> get("title"));
            $this -> template -> assign("APPLICATION", json_encode(self::$app));
            $this -> template -> assign("EXTENSIONS", json_encode(Extensions::getAll()));
            $this -> template -> assign("CURRENT_SESSION", json_encode(Sessions::getCurrentSession()));
            $this -> template -> assign("CURRENT_USER", json_encode(Sessions::getCurrentUser()));
            $this -> template -> assign("SETTINGS", json_encode(Settings::getAll()));
            $this -> template -> assign("ERRORS", json_encode(Errors::getAll()));
            $this -> template -> assign("USERS", json_encode(Users::getAll()));
            $this -> template -> assign("DEPARTMENTS", json_encode(Kolenergo::getDepartments()));
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

                        /*
                        if (Settings::install() == true) {
                            echo("Установка подсистемы настроек выполнена успешно</br>");
                            return true;
                        }
                        if (Sessions::install() == true) {
                            echo("Установка подсистемы управыления сессиями выполнена успешно</br>");
                            return true;
                        }
                        if (Users::install() == true) {
                            echo("Установка подсистемы пользователей успешно</br>");
                            return true;
                        }
                        */
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