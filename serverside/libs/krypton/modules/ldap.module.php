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
            if (DBManager::is_table_exists_mysql("kr_ldap"))
                return true;
            else
                return false;
        }



        /**
        * Производит инициализацию модуля
        **/
        public function init () {
            //echo("LDAP module init");
            self::login("kolu0897", "zx12!@#$");
        }



        public static function isLDAPEnabled ($userId) {
            if ($userId == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> isLDAPEnabled: Не задан параметр - идентификатор пользователя");
                return false;
            } else {
                if (gettype($userId) != "integer") {
                    Errors::push(Errors::DB_ERROR_TYPE_DEFAULT, "LDAP -> isLDAPEnabled: Неверно задан тип параметра - идентификатор пользователя");
                    return false;
                } else {
                    $ldap = DBManager::select_mysql(self::$id, ["*"], "'user_id = $userId LIMIT 1'");
                    return $ldap != false ? boolval($ldap) : false;
                }
            }
        }



        public static function login ($login, $password) {
            global $ldap_host;

            if ($login == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Не задан прараметр - логин пользователя");
                return false;
            } else {
                if (gettype($login) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Неверно задан тип параметра - логин пользователя");
                    return false;
                } else {
                    if ($password == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Не задан параметр - пароль пользователя");
                        return false;
                    } else {
                        if (gettype($password) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Неверно задан тип параметра  пароль пользователя");
                            return false;
                        } else {
                            $link = ldap_connect($ldap_host);
                            ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, 3);
                            if (!$link) {
                                echo("LDAP connect to host false</br>");
                            } else {
                                echo("LDAP connect to host success</br>");

                                $bind = ldap_bind($link, "NW\\".$login, $password);
                                if (!$bind) {
                                    echo("no such user in AD</br>");
                                } else {
                                    echo("AD user found</br>");
                                    $attributes = array("displayname", "mail", "samaccountname", "cn");
                                    $filter = "(&(objectCategory=person)(sAMAccountName=$login))";
                                    $userInfo = ldap_search($link, ('OU=02_USERS,OU=Kolenergo,DC=nw,DC=mrsksevzap,DC=ru'), $filter, $attributes);
                                    echo "Результат поиска: " . $userInfo . "<br />";

                                    echo "Получено количество записей " . ldap_count_entries($link, $userInfo) . "<br />";

                                    $info = ldap_get_entries($link, $userInfo);
                                    var_dump($info);
                                }

                            }
                        }
                    }
                }
            }
        }
    };

?>