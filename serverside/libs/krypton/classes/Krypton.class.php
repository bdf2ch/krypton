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



        function __construct($parameters) {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            session_start();
            DBManager::connect($db_host, $db_user, $db_password);
            DBManager::select_db("krypton");


            self::$app = new Application();
            self::$app -> init();

            if (is_null($parameters))
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Не задан параметр - массив параметров инициализации");
            else {
                if (gettype($parameters) != "array")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - массив параметров инициализации");
            }


            if (is_null($parameters["title"]))
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Не задлан параметр - наименование приложения");
            else {
                if (gettype($parameters["title"]) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - наименование приложения");
                else {
                    if (self::$app -> title != $parameters["title"])
                        if ($parameters["title"] != "")
                            self::$app -> set("title", $parameters["title"]);
                        else
                            self::$app -> set("title", " ");
                }
            }


            if (!is_null($parameters["description"])) {
                if (gettype($parameters["description"]) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - описание приложения");
                else {
                    if (self::$app -> description != $parameters["description"])
                        if ($parameters["description"] != "")
                            self::$app -> set("description", $parameters["description"]);
                        else
                            self::$app -> set("description", " ");
                }
            } else
                self::$app -> set("description", " ");


            if (!is_null($parameters["extensions"])) {
                if (gettype($parameters["extensions"]) != "array")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - список используемых расширений");
                else {
                    if (sizeof($parameters["extensions"]) > 0) {
                        foreach ($parameters["extensions"] as $key => $extension) {
                            Extensions::load($extension);
                        /*
                            if (file_exists( $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$extension.".extension.php")) {
                                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/extensions/".$extension.".extension.php";
                                //$ext = new $extension($parameters);
                                $ext = new Extension($extension::$id, $extension::$title, $extension::$url, $extension::$description);
                                array_push(Extensions::$items, $ext);
                                $extension::init();
                            } else
                                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Расширение '".$extension."' не найдено");
                        */
                        }
                    }
                }
            }

            Settings::init();
            Users::init();
            Sessions::init();


            /*
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
            */


        }



        public static function getDBType () {
            return self::$dbType;
        }



        public function start () {
        /*
            if (defined("ENGINE_ADMIN_MODE")) {
                echo("ADMIN MODE</br>");
                if (Sessions::getCurrentUser() != false && Sessions::getCurrentUser() -> isAdmin -> value == true)
                    $template_url = $_SERVER["DOCUMENT_ROOT"]."/serverside/templates/admin.html";
                else
                    $template_url = $_SERVER["DOCUMENT_ROOT"]."/serverside/templates/admin_login.html";

            }
            */

            $path = explode("/", $_SERVER["REQUEST_URI"]);
            if (isset($path[1]) && $path[1] != "") {
                switch ($path[1]) {
                    case "admin":
                        //if (isset($path[2]))
                        //    header("Location: /admin/");
                        //else {
                            define("ENGINE_ADMIN_MODE", 1);
                            if (Sessions::getCurrentUser() != false && Sessions::getCurrentUser() -> isAdmin -> value == true)
                                $template_url = $_SERVER["DOCUMENT_ROOT"]."/serverside/templates/admin.html";
                            else
                                $template_url = $_SERVER["DOCUMENT_ROOT"]."/serverside/templates/admin_login.html";
                        //}
                        break;
                    default:
                        if (defined("ENGINE_ADMIN_MODE"))
                            runkit_constant_remove("ENGINE_ADMIN_MODE");
                        header("Location: /");
                        break;
                }
            } else
                $template_url = $_SERVER["DOCUMENT_ROOT"]."/serverside/templates/application.html";



            Sessions::login("kolu0897", "zx12!@#$");

            //var_dump(Models::load("Department", false));
            //$temp = new Department(false);
            //var_dump($temp::$fields);


            $this -> template = new XTemplate($template_url);
            //$this -> template -> assign("ADMIN_TEMPLATE", self::getAdminTemplate());
            $this -> template -> assign("APPLICATION_TITLE", self::$app -> get("title"));
            $this -> template -> assign("APPLICATION", json_encode(self::$app));
            $this -> template -> assign("EXTENSIONS", json_encode(Extensions::getAll()));
            $this -> template -> assign("CURRENT_SESSION", json_encode(Sessions::getCurrentSession()));
            if (Sessions::getCurrentUser() != false)
                $this -> template -> assign("CURRENT_USER", Sessions::getCurrentUser() -> toJSON());
            $this -> template -> assign("SETTINGS", json_encode(Settings::getAll()));
            $this -> template -> assign("ERRORS", json_encode(Errors::getAll()));
            $this -> template -> assign("USERS", json_encode(Users::getAll()));
            $this -> template -> assign("DEPARTMENTS", json_encode(Kolenergo::getDepartments()));
            $this -> template -> assign("DIVISIONS", json_encode(Kolenergo::getDivisions()));
            $this -> template -> assign("CLIENT_SIDE_EXTENSIONS", Extensions::getExtensionsUrls());
            $this -> template -> parse("main");
            $this -> template -> out("main");
        }



        public static function install () {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;


            $result = DBManager::connect($db_host, $db_user, $db_password);
            if (!$result)
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось подключиться к БД");

            $result = DBManager::create_db("krypton");
            if (!$result)
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось создать БД");

            $result = DBManager::select_db("krypton");
            if (!$result)
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось выбрать БД");

            $result = DBManager::create_table("kr_app_info");
            if (!$result)
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось создать таблицу с информацией о приложении");

            $result = DBManager::add_column("kr_app_info", "title", "varchar(200) NOT NULL default ''");
            if (!$result)
                 Errors::push(Errors::Error_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'title' в таблшицу с информацией о приложении");

            $result = DBManager::add_column("kr_app_info", "description", "varchar(200) default ''");
            if (!$result)
                Errors::push(Errors::Error_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'description' в таблшицу с информацией о приложении");

            $result = DBManager::add_column("kr_app_info", "is_in_debug_mode", "int(11) NOT NULL default 0");
            if (!$result)
                Errors::push(Errors::Error_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'is_in_debug_mode' в таблшицу с информацией о приложении");

            $result = DBManager::add_column("kr_app_info", "is_in_construction_mode", "int(11) NOT NULL default 0");
            if (!$result)
                Errors::push(Errors::Error_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'is_in_construction_mode' в таблшицу с информацией о приложении");

            $result = DBManager::create_table("kr_app_extensions");
            if (!$result)
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось создать таблицу с информацией об используемых расширениях");

            $result = DBManager::add_column("kr_app_extensions", "extension_id", "varchar(200) NOT NULL default ''");
            if (!$result)
                Errors::push(Errors::Error_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'extension_id' в таблшицу с информацией об используемых расширениях");

            $result = DBManager::add_column("kr_app_extensions", "enabled", "int(11) NOT NULL default '1'");
            if (!$result)
                Errors::push(Errors::Error_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'enabled' в таблшицу с информацией об используемых расширениях");

            return true;


            /*
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
            */
        }



        public static function getAdminTemplate () {
            if (Sessions::getCurrentUser() != false) {
                //echo ("user = ".json_encode(Sessions::getCurrentUser()));
                if (Sessions::getCurrentUser() -> isAdmin -> value === true) {
                    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/templates/admin.html")) {
                        return json_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/serverside/templates/admin.html"));
                    } else {
                        echo(json_encode(Errors::push(Errors::ERROR_TYPE_ENGINE, "Файл с шаблоном панели администрирования не найден")));
                        return false;
                    }
                } else {
                    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/serverside/templates/admin_login.html")) {
                        echo(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/serverside/templates/admin_login.html"));
                        return true;
                    } else {
                        echo(json_encode(Errors::push(Errors::ERROR_TYPE_ENGINE, "Файл с шаблоном авторизации в панели администрирования не найден не найден")));
                        return false;
                    }
                }
            }
        }


    };

?>