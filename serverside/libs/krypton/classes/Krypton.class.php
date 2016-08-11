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

        private static $dbType = self::DB_TYPE_ORACLE;
        private $template;
        public static $app;



        function __construct($extensions) {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            session_start();

            switch (Krypton::$dbType) {
                case Krypton::DB_TYPE_MYSQL:
                    DBManager::connect($db_host, $db_user, $db_password);
                    DBManager::select_db("krypton");
                    break;
                case Krypton::DB_TYPE_ORACLE:
                    DBManager::connect($db_host, $db_user, $db_password);
                    break;
            }


            self::$app = new Application();
            self::$app -> init();


              self::$app -> run();

                        Settings::init();
                        Permissions::init();
                        Users::init();
                        Sessions::init();
                        Files::init();


            $template = "/serverside/templates/application.html";
            if (defined("ENGINE_ADMIN_MODE")) {
                if (Sessions::getCurrentUser() != false)
                    $template = Sessions::getCurrentUser() -> isAdmin -> value == true ? "/serverside/templates/admin.html" : "/serverside/templates/admin_login.html";
                else
                    $template = "/serverside/templates/admin_login.html";
            }
            $this -> template = new XTemplate($_SERVER["DOCUMENT_ROOT"].$template);


            if (is_null($extensions))
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Не задан параметр - массив подключаемых модулей");

            if (gettype($extensions) != "array")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Krypton -> __construct: Неверно задан тип параметра - массив подключаемых модулей");

            if (sizeof($extensions) > 0) {
                foreach ($extensions as $key => $extension) {
                    Extensions::get($extension) -> load();
                }
            }



            //Kolenergo::init();
            //LDAP::init();

            //$this -> start();

        }



        public static function getDBType () {
            return self::$dbType;
        }



        public function start () {
            if (sizeof(Krypton::$app -> get("initials")) > 0) {
                $index = 0;
                $assignment = "";
                foreach (Krypton::$app -> get("initials") as $key => $data) {
                    $assignment .= "\n".$key.": ".$data;
                    $assignment .= $index < sizeof(Krypton::$app -> get("initials")) - 1 ? "," : "";
                    $this -> template -> assign("INITIAL_DATA", $assignment);
                    $index++;
                }
            }

            if (sizeof(Krypton::$app -> get("scripts")) > 0) {
                $scripts = "";
                foreach (Krypton::$app -> get("scripts") as $key => $script) {
                    $url = defined("ENGINE_ADMIN_MODE") ? "../../" : "";
                    $scripts .= "<script src='".$url.$script."'></script>";
                    $scripts .= $key < sizeof(Krypton::$app -> get("scripts")) - 1 ? ",\n" : "";
                }
                $this -> template -> assign("ATTACHED_SCRIPTS", $scripts);
            }

            //if (Sessions::getCurrentUser() != false && Sessions::getCurrentUser() -> isAdmin -> value == true)
                $this -> template -> parse("main.DEBUG_CONSOLE");
            $this -> template -> parse("main");
            $this -> template -> out("main");
        }



        public static function install () {
            global $db_host;
            global $db_name;
            global $db_user;
            global $db_password;

            $result = DBManager::connect($db_host, $db_user, $db_password);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось подключиться к БД");
                return false;
            }

            $result = DBManager::create_db("krypton");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось создать БД");
                return false;
            }

            $result = DBManager::select_db("krypton");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось выбрать БД");
                return false;
            }

            $result = DBManager::create_table("kr_app_info");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось создать таблицу 'kr_app_info'");
                return false;
            }

            $result = DBManager::create_table("kr_app_extensions");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось создать таблицу 'kr_app_extensions'");
                return false;
            }

            switch (self::getDBType()) {
                case self::DB_TYPE_MYSQL:

                    $result = DBManager::add_column("kr_app_info", "TITLE", "varchar(200) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'title' в таблицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_info", "DESCRIPTION", "varchar(200) default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'description' в таблиицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_info", "IS_IN_DEBUG_MODE", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'is_in_debug_mode' в таблиицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_info", "IS_IN_CONSTRUCTION_MODE", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'is_in_construction_mode' в таблицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_app_info", ["TITLE", "DESCRIPTION", "IS_IN_DEBUG_MODE", "IS_IN_CONSTRUCTION_MODE"], [ "''", "''", 0, 0]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить данные в таблицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "EXTENSION_ID", "varchar(200) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'extension_id' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "EXTENSION_TITLE", "varchar(200) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'extension_title' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "EXTENSION_DESCRIPTION", "varchar(200) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'extension_description' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "EXTENSION_URL", "varchar(200) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'extension_url' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "ENABLED", "int(11) NOT NULL default '1'");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'enabled' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    break;

                case self::DB_TYPE_ORACLE:

                    $result = DBManager::add_sequence("seq_extensions", 1, 1);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить последовательность 'seq_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_info", "TITLE", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'title' в таблицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_info", "DESCRIPTION", "varchar(200) default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'description' в таблиицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_info", "IS_IN_DEBUG_MODE", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'is_in_debug_mode' в таблиицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_info", "IS_IN_CONSTRUCTION_MODE", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось Добавить столбец 'is_in_construction_mode' в таблицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_app_info", ["ID", "TITLE", "DESCRIPTION", "IS_IN_DEBUG_MODE", "IS_IN_CONSTRUCTION_MODE"], [1, "' '", "' '", 0, 0]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить данные в таблицу 'kr_app_info'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "EXTENSION_ID", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'extension_id' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "EXTENSION_TITLE", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'extension_title' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "EXTENSION_DESCRIPTION", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'extension_description' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "EXTENSION_URL", "VARCHAR2(200)");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'extension_url' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_app_extensions", "ENABLED", "INT DEFAULT 1 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Krypton -> install: Не удалось добавить столбец 'enabled' в таблицу 'kr_app_extensions'");
                        return false;
                    }

                    break;
            }

            return true;
        }

    };

?>