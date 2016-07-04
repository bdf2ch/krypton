<?php

    class LDAP extends ExtensionInterface {
        public static $title = "LDAP";
        public static $description = "LDAP description";
        public static $url = "";


        public function __construct () {
            parent::__construct();
        }


        /**
        * Производит установку модуля
        **/
        public function install () {
            $result = DBManager::create_table(self::$id);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалочь создать таблицу с информацией об LDAP");
                return false;
            }

            $result = DBManager::add_column(self::$id, "user_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалось добавить столбец 'user_id' в таблицу с информацией об LDAP");
                return false;
            }

            $result = DBManager::add_column(self::$id, "enabled", "int(11) NOT NULL default 1");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалось добавить столбец 'enabled' в таблицу с информацией об LDAP");
                return false;
            }

            $result = Settings::add("'".self::$id."'", "'ldap_server'", "'Адрес сервера LDAP'", "'Сетевой адрес сервера аутентификации LDAP'", Krypton::DATA_TYPE_STRING, "''", 1);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалось добавить настройку 'ldap_server'");
                return false;
            }

            $result = Settings::add("'".self::$id."'", "'ldap_enabled'", "'Авторизация LDAP включена'", "'Авторизация пользователей посредством Active Directory включена'", Krypton::DATA_TYPE_BOOLEAN, 1, 0);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалось добавить настройку 'ldap_enabled'");
                return false;
            }

            return true;
        }



        /**
        * Проверяет, установлен ли модуль в системе
        **/
        public function isInstalled () {
            return DBManager::is_table_exists(self::$id);
        }



        /**
        * Производит инициализацию модуля
        **/
        public function init () {
            parent::_init_();
            //var_dump(self::login("kolu0897", "zx12!@#$"));
            if (self::isInstalled() == true) {

            } else
                self::install();
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
                    $ldap = DBManager::select(self::$id, ["*"], "user_id = $userId LIMIT 1");
                    return $ldap != false ? boolval($ldap[0]["enabled"]) : false;
                }
            }
        }



        public static function enableLDAP ($userId) {
            if ($userId == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> setLDAPEnabled: Не задан параметр - идентификатор пользователя");
                return false;
            } else {
                if (gettype($userId) != "integer") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> setLDAPEnabled: Неверно задан тип параметра - идентификатор пользователя");
                    return false;
                } else {
                    if (!DBManager::select(self::$id, ["*"], "user_id = $userId LIMIT 1")) {
                        if (!DBManager::insert_row(self::$id, ["user_id", "enabled"], [$userId, 1])) {
                            return false;
                        } else
                            return true;
                    } else {
                        if(!DBManager::update_row(self::$id, ["enabled"], [1], "user_id = $userId"))
                            return false;
                         else
                            return true;
                    }
                }
            }
        }



        public static function login ($login, $password) {
            global $ldap_host;

            if ($login == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Не задан прараметр - логин пользователя");

            if (gettype($login) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Неверно задан тип параметра - логин пользователя");

            if ($password == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Не задан параметр - пароль пользователя");

            if (gettype($password) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Неверно задан тип параметра  пароль пользователя");

            $link = ldap_connect($ldap_host);
            if (!$link)
                return Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: Не удалось подключиться к серверу LDAP");

            $result = ldap_set_option ($link, LDAP_OPT_PROTOCOL_VERSION, 3);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));

            $result = ldap_bind($link, "NW\\".$login, $password);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));

            $attributes = array("name", "mail", "samaccountname", "cn", "telephonenumber", "mobile");
            $filter = "(&(objectCategory=person)(sAMAccountName=$login))";
            $search = ldap_search($link, ('OU=02_USERS,OU=Kolenergo,DC=nw,DC=mrsksevzap,DC=ru'), $filter, $attributes);
            if (!$search)
                return Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));

            $result = ldap_get_entries($link, $search);
            if (!$result)
                return Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));

            $fio = explode(" ", $info[0]["name"][0]);
            $surname = $fio[0];
            $name = $fio[1];
            $fname = $fio[2];
            $phone = "";
            $mobile = $info[0]["mobile"][0];
            $email = $info[0]["mail"][0];

            if ($info[0]["telephonenumber"]["count"] > 0) {
                for ($i = 0; $i < $info[0]["telephonenumber"]["count"]; $i++) {
                    $phone += strval($info[0]["telephonenumber"][$i]);
                    $phone += $i < $info[0]["telephonenumber"]["count"] ? ";" : "";
                }
            }

            $user = Models::construct("User1", false);
            $user -> id -> value = -1;
            $user -> surname -> value = $surname;
            $user -> name -> value = $name;
            $user -> fname -> value  = $fname;
            $user -> email -> value = $email;
            $user -> phone -> value = strval($phone);
            $user -> mobile -> value = strval($mobile);

            return $user;
        }


    };

?>