<?php

    class LDAP extends Module {

        /**
        *
        **/
        public static function install () {
            if (!DBManager::is_table_exists_mysql("ldap")) {
                if (DBManager::create_table_mysql("ldap")) {
                    if (DBManager::add_column_mysql("ldap", "user_id", "int(11) NOT NULL default 0") &&
                        DBManager::add_column_mysql("ldap", "enabled", "int(11) NOT NULL default 1")
                    )
                        echo("Модуль Krypton.LDAP успешно установлен</br>");
                    else
                        echo("Не удалось выполнить установку модуля Krypton.LDAP</br>");
                } else
                    echo("Не удалось выполнить установку модуля Krypton.LDAP</br>");
            }
        }


        /**
        * проверяет, установлен ли модуль в системе
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
    };

?>