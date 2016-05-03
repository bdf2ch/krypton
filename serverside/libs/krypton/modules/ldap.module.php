<?php

    class LDAP extends Module {

        private static $id = "kr_ldap";

        /**
        * Производит установку модуля в системе
        **/
        public static function install () {
            if (!DBManager::is_table_exists_mysql(self::$id)) {
                if (DBManager::create_table_mysql(self::$id)) {
                    if (DBManager::add_column_mysql(self::$id, "user_id", "int(11) NOT NULL default 0") &&
                        DBManager::add_column_mysql(self::$id, "enabled", "int(11) NOT NULL default 1")
                    ) {
                        if (Settings::isInstalled()) {
                            if (Settings::add("'".self::$id."'", "'ldap_server'", "'Адрес сервера LDAP'", "'Сетевой адрес сервера аутентификации LDAP'", "'string'", "''", 1))
                                echo("Модуль Krypton.LDAP успешно установлен</br>");
                        }
                    }
                        //echo("Модуль Krypton.LDAP успешно установлен</br>");
                    else
                        echo("Не удалось выполнить установку модуля Krypton.LDAP</br>");
                } else
                    echo("Не удалось выполнить установку модуля Krypton.LDAP</br>");
            }
        }


        /**
        * Проверяет, установлен ли модуль в системе
        **/
        public static function isInstalled () {
            if (DBManager::is_table_exists_mysql("ldap"))
                return true;
            else
                return false;
        }


        /**
        * Производит инициализацию модуля
        **/
        public function init () {
            //echo("LDAP module init");
        }


        public static function login () {
            global $ldap_host;
            $connection = ldap_connect($ldap_host);
            return $connection;
        }
    };

?>