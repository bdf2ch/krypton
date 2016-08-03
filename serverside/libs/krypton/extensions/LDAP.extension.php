<?php

    class LDAP implements ExtensionInterface {
           public static $id;
                //public static $description;
                //public static $url;
                public static $installed;
                public static $enabled = true;


        public static $title = "LDAP";
        public static $description = "LDAP description";
        public static $url = "";


        public function __construct () {
                   //parent::__construct();
                   $class = get_called_class();
                   self::$id = $class;
               }

               public function load () {
                           $class = get_called_class();

                           if (!Krypton::$app -> isExtensionLoaded($class)) {
                               switch (Krypton::getDBType()) {
                                   case Krypton::DB_TYPE_MYSQL:

                                       $result = DBManager::insert_row(
                                           "kr_app_extensions",
                                           ["extension_id", "extension_title", "extension_description", "extension_url", "enabled"],
                                           ["'".$class::$id."'", "'".$class::$title."'", "'".$class::$description."'", "'".$class::$url."'", 1]
                                       );
                                       if (!$result) {
                                           Errors::push(Errors::ERROR_TYPE_ENGINE, "Extension -> load: Не удалось подключить расширение '".$class."' к приложению");
                                           return false;
                                       }

                                       break;
                                   case Krypton::DB_TYPE_ORACLE:
                                       $id = DBManager::sequence_next("seq_extensions");
                                       if (!$id) {
                                           Errors::push(Errors::ERROR_TYPE_ENGINE, "ExtensionInterface -> load: Не удалось получить следующее значение последовательности 'seq_extensions'");
                                           return false;
                                       }

                                       $result = DBManager::insert_row(
                                           "kr_app_extensions",
                                           ["id", "extension_id", "extension_title", "extension_description", "extension_url", "enabled"],
                                           [$id, "'".$class::$id."'", "'".$class::$title."'", "'".$class::$description."'", "'".$class::$url."'", 1]
                                       );
                                       if (!$result) {
                                           Errors::push(Errors::ERROR_TYPE_ENGINE, "Extension -> load: Не удалось подключить расширение '".$class."' к приложению");
                                           return false;
                                       }

                                       break;
                               }



                               $extension = Models::load("Extension", false);
                               $extension -> id -> value = $class::$id;
                               $extension -> title -> value = $class::$title;
                               $extension -> description -> value = $class::$description;
                               $extension -> url -> value = $class::$url;
                               array_push(Krypton::$app -> extensions, $extension);
                           } else
                               Extensions::get($class) -> init();

                           return true;
                       }


                       public function getUrl () {
                           return self::$url != "" ? self::$url : false;
                       }


                       public function setEnabled ($flag) {
                           if ($flag == null)
                               Errors::push(Errors::ERROR_TYPE_DEFAULT, "ExtensionInterface -> setEnabled");
                       }




        /**
        * Производит установку модуля
        **/
        public function install () {
            //$result = DBManager::is_table_exists("kr_users");
            //if (!$result)
             //   return Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Таблица с пользователями не найдена");


            //$result = DBManager::create_table(self::$id);
            //if (!$result) {
            //    Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалочь создать таблицу с информацией об LDAP");
            //    return false;
            //}

            $result = Settings::add("'ldap'", "'ldap_server'", "'Адрес сервера LDAP'", "'Сетевой адрес сервера аутентификации LDAP'", Krypton::DATA_TYPE_STRING, "'10.50.0.1'", 1);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалось добавить настройку 'ldap_server'");
                return false;
            }

            $result = Settings::add("'ldap'", "'ldap_enabled'", "'Авторизация LDAP включена'", "'Авторизация пользователей посредством Active Directory включена'", Krypton::DATA_TYPE_BOOLEAN, 1, 0);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалось добавить настройку 'ldap_enabled'");
                return false;
            }


            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:

                    $result = DBManager::add_column("kr_users", "ldap_enabled", "int(11) NOT NULL default 1");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'ldap_enabled' в таблицу ползователей");
                        return false;
                    }


                    //$result = DBManager::add_column(self::$id, "user_id", "int(11) NOT NULL default 0");
                    //if (!$result) {
                    //    Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалось добавить столбец 'user_id' в таблицу с информацией об LDAP");
                    //    return false;
                    //}

                    //$result = DBManager::add_column(self::$id, "enabled", "int(11) NOT NULL default 1");
                    //if (!$result) {
                    //    Errors::push(Errors::ERROR_TYPE_ENGINE, "LDAP -> install: Не удалось добавить столбец 'enabled' в таблицу с информацией об LDAP");
                    //    return false;
                    //}



                    break;
                case Krypton::DB_TYPE_ORACLE:

                    $result = DBManager::add_column("kr_users", "ldap_enabled", "INT DEFAULT 1 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Kolenergo -> install: Не удалось добавить столбец 'ldap_enabled' в таблицу ползователей");
                        return false;
                    }

                    break;
             }



            return true;
        }



        /**
        * Проверяет, установлен ли модуль в системе
        **/
        public function isInstalled () {
            //return DBManager::is_table_exists(self::$id);
            return true;
        }



        /**
        * Производит инициализацию модуля
        **/
        public function init () {
            //parent::_init_();
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
            }

            if (gettype($login) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Неверно задан тип параметра - логин пользователя");
                return false;
            }

            if ($password == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Не задан параметр - пароль пользователя");
                return false;
            }

            if (gettype($password) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "LDAP -> login: Неверно задан тип параметра  пароль пользователя");
                return false;
            }

            $link = ldap_connect($ldap_host);
            if (!$link) {
                Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: Не удалось подключиться к серверу LDAP");
                return false;
            }

            $result = ldap_set_option ($link, LDAP_OPT_PROTOCOL_VERSION, 3);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));
                return false;
            }

            $result = ldap_bind($link, "NW\\".$login, $password);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));
                return false;
            }

            $attributes = array("name", "mail", "samaccountname", "cn", "telephonenumber", "mobile");
            $filter = "(&(objectCategory=person)(sAMAccountName=$login))";

            $search = ldap_search($link, ('OU=02_USERS,OU=Kolenergo,DC=nw,DC=mrsksevzap,DC=ru'), $filter, $attributes);
            if (!$search) {
                Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));
                return false;
            }

            $result = ldap_get_entries($link, $search);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_LDAP, "LDAP -> login: ".ldap_errno($link)." - ".ldap_error($link));
                return false;
            }

            //var_dump($result);

            $fio = explode(" ", $result[0]["name"][0]);
            $surname = $fio[0];
            $name = $fio[1];
            $fname = $fio[2];
            $phone = "";
            $mobile = $result[0]["mobile"][0];
            $email = $result[0]["mail"][0];

            if ($result[0]["telephonenumber"]["count"] > 0) {
                for ($i = 0; $i < $result[0]["telephonenumber"]["count"]; $i++) {
                    $phone += strval($result[0]["telephonenumber"][$i]);
                    $phone += $i < $result[0]["telephonenumber"]["count"] ? ";" : "";
                }
            }

            $parameters = array(
                "id" => 0,
                "surname" => $surname,
                "name" => $name,
                "fname" => $fname,
                "email" => $email,
                "phone" => $phone,
                "mobile" => $mobile
            );

            $user = Models::construct("User1", false);
            $user -> id -> value = 0;
            $user -> surname -> value = $surname;
            $user -> name -> value = $name;
            $user -> fname -> value = $fname;
            $user -> email -> value = $email;
            $user -> phone -> value = strval($phone);
            $user -> mobile -> value = strval($mobile);

            //var_dump($user);

            return $user;
        }


    };

?>