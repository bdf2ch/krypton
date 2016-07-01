<?php

    class LDAP extends ExtensionInterface {

        //public static $id = "";
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
                                Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: Не удалось подключиться к серверу LDAP");
                                return false;
                            } else {
                                $bind = ldap_bind($link, "NW\\".$login, $password);
                                if (!$bind) {
                                    Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));
                                    return false;
                                } else {
                                    $attributes = array("name", "mail", "samaccountname", "cn", "telephonenumber", "mobile");
                                    $filter = "(&(objectCategory=person)(sAMAccountName=$login))";
                                    $userInfo = ldap_search($link, ('OU=02_USERS,OU=Kolenergo,DC=nw,DC=mrsksevzap,DC=ru'), $filter, $attributes);
                                    if (!userInfo) {
                                        Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));
                                        return false;
                                    } else {
                                        $info = ldap_get_entries($link, $userInfo);
                                        if (!$info) {
                                            Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));
                                            return false;
                                        } else {
                                            //var_dump($info);
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

                                            /*
                                            if (!Users::getByEmail($email)) {
                                                echo("пользовател ь с email = ".$email." не найден</br>");

                                                $newUserId = Users::add($name, $fname, $surname, " ", $email, strval($phone), $password, false);
                                                if ($newUserId != false) {
                                                    echo("Пользователь добавлен</br>");
                                                    self::enableLDAP($newUserId);
                                                        if (!Session::assignCurrentSessionToUser($newUserId)) {
                                                            echo("не удалось привязать текущкю сессию к пользователю ".$newUserId."</br>");
                                                        } else {
                                                            echo("текущая сессия привязана к пользователю</br>");
                                                            if(!Session::setCurrentUserById($newUserId)) {
                                                                echo("не удалось установить текущего пользователя сессии</br>");
                                                            }
                                                        }

                                                } else {

                                                }
                                            } else {
                                                echo("Пользователь с email = ".$email." уже сущетсвует</br>");
                                                if (!self::isLDAPEnabled(Session::getCurrentUser() -> id)) {
                                                    echo("для пользователя id = ".Session::getCurrentUser() -> id." запрещена LDAP-аутентификация</br>");
                                                } else {
                                                    if (!Session::assignCurrentSessionToUser(Session::getCurrentUser() -> id)) {
                                                        echo("не удалось привязать текущую сессию к пользователю ".$newUserId."</br>");
                                                    } else {
                                                        echo("текущая сессия привязана к пользователю</br>");
                                                        if(!Session::setCurrentUserById(Session::getCurrentUser() -> id)) {
                                                            echo("не удалось установить текущего пользователя сессии</br>");
                                                        }
                                                    }
                                                }
                                            }
                                            var_dump($user);echo("</br>");
                                            */
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


    };

?>