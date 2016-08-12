<?php

    class Users implements Service {

        public static $description = "Users description";
        public static $clientSideExtensionUrl = "modules/app/krypton.app.users.js";
        private static $items = array();
        private static $groups = array();
        private static $itemsOnPage = 25;





        /**
        * Производит установку сервиса
        **/
        public static function install () {
            $result = DBManager::create_table("kr_user_groups");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось создать таблицу 'kr_user_groups'");
                return false;
            }

            $result = DBManager::create_table("kr_users");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось создать таблицу 'kr_users'");
                return false;
            }

            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:

                    $result = DBManager::add_column("kr_user_groups", "TITLE", "varchar(500) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'title' в таблицу 'kr_user_groups'");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_user_groups", ["TITLE"], ["'Администраторы'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу 'kr_user_groups'");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_user_groups", ["TITLE"], ["'Редакторы'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу 'kr_user_groups'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "USER_GROUP_ID", "int(11) NOT NULL default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'user_group_id' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "NAME", "varchar(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'name' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "SURNAME", "varchar(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'surname' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "FNAME", "varchar(200) default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'fname' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "EMAIL", "varchar(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'email' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "PHONE", "varchar(100) default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'phone' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "MOBILE_PHONE", "varchar(200) default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'mobile_phone' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "POSITION", "varchar(500) default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'position' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "PHOTO_URL", "varchar(500) default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'photo_url' в таблицу 'kr_users'");
                        return false;
                    }

                    //$result = DBManager::add_column("kr_users", "photo", "mediumblob");
                    //if (!$result) {
                    //    Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'photo' в таблицу 'kr_users'");
                    //    return false;
                    //}

                    $result = DBManager::add_column("kr_users", "PASSWORD", "varchar(60) NOT NULL default ''");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'password' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "IS_ADMIN", "int(11) default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'is_admin' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "IS_DELETED", "int(11) default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'is_deleted' в таблицу 'kr_users'х");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "IS_DISPLAYABLE", "int(11) default 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'is_displayable' в таблицу 'kr_users'х");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_users", ["USER_GROUP_ID", "SURNAME", "NAME", "FNAME", "EMAIL", "PASSWORD", "IS_ADMIN", "DISPLAYABLE"], [1, "''", "'Администратор'", "''", "'bdf2ch@gmail.com'", "'".md5("zx12!@#$")."'", 1, 0]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу 'kr_users'");
                        return false;
                    }

                    break;

                case Krypton::DB_TYPE_ORACLE:

                    $result = DBManager::add_sequence("seq_users", 1, 1);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить последовательность 'seq_users'");
                        return false;
                    }

                    $result = DBManager::add_sequence("seq_user_groups", 1, 1);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить последовательность 'seq_user_groups'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_user_groups", "TITLE", "VARCHAR2(500) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'title' в таблицу 'kr_user_groups'");
                        return false;
                    }

                    $id = DBManager::sequence_next("seq_user_groups");
                    if (!$id) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось получить следующее значение последовательности 'seq_user_groups'");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_user_groups", ["ID", "TITLE"], [$id, "'Администраторы'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу 'kr_user_groups'");
                        return false;
                    }

                    $id = DBManager::sequence_next("seq_user_groups");
                    if (!$id) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось получить следующее значение последовательности 'seq_user_groups'");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_user_groups", ["ID", "TITLE"], [$id, "'Редакторы'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу 'kr_user_groups'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "USER_GROUP_ID", "INT DEFAULT 0 NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'user_group_id' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "NAME", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'name' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "SURNAME", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'surname' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "FNAME", "VARCHAR2(200)");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'fname' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "EMAIL", "VARCHAR2(200) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'email' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "PHONE", "VARCHAR2(100)");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'phone' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "MOBILE_PHONE", "VARCHAR2(200)");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'mobile_phone' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "POSITION", "VARCHAR2(500)");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'position' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "PHOTO_URL", "VARCHAR2(500)");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'photo_url' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "PASSWORD", "VARCHAR2(60) NOT NULL");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'password' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "IS_ADMIN", "INT DEFAULT 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'is_admin' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "IS_DELETED", "INT DEFAULT 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'is_deleted' в таблицу 'kr_users'");
                        return false;
                    }

                    $result = DBManager::add_column("kr_users", "IS_DISPLAYABLE", "INT DEFAULT 0");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить столбец 'is_displayable' в таблицу 'kr_users'х");
                        return false;
                    }

                    $id = DBManager::sequence_next("seq_users");
                    if (!$id) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось получить следующее значение последовательности 'seq_users'");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_users", ["ID", "USER_GROUP_ID", "SURNAME", "NAME", "FNAME", "EMAIL", "PASSWORD", "IS_ADMIN"], [$id, 1, "' '", "'Администратор'", "' '", "'bdf2ch@gmail.com'", "'".md5("zx12!@#$")."'", 1]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_DATABASE, "Users -> install: Не удалось добавить данные в таблицу 'kr_users'");
                        return false;
                    }

                    break;
            }


            $rule = new stdClass();
            $rule -> code = "add-user-group";
            $rule -> title = "Добавление групп пользователей";

            $result = Permissions::addPermissionRule($rule);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить правило доступа 'Добавление групп пользователей'");
                return false;
            }

            $rule -> code = "edit-user-group";
            $rule -> title = "Редактирование групп пользователей";
            $result = Permissions::addPermissionRule($rule);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить правило доступа 'Редактирование групп пользователей'");
                return false;
            }

            $rule -> code = "delete-user-group";
            $rule -> title = "Удаление групп пользователей";
            $result = Permissions::addPermissionRule($rule);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить правило доступа 'Удаление групп пользователей'");
                return false;
            }

            $rule -> code = "add-user";
            $rule -> title = "Добавление пользователей";
            $result = Permissions::addPermissionRule($rule);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить правило доступа 'Добавление пользователей'");
                return false;
            }

            $rule -> code = "edit-user";
            $rule -> title = "Редактирование пользователей";
            $result = Permissions::addPermissionRule($rule);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить правило доступа 'Редактирование пользователей'");
                return false;
            }

            $rule -> code = "delete-user";
            $rule -> title = "Удаление пользователей";
            $result = Permissions::addPermissionRule($rule);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось добавить правило доступа 'Удаление пользователей'");
                return false;
            }

            return true;
        }



        /**
        * Проверяет, установлен ли модуль в системе
        **/
        /*
        public static function isInstalled () {
            if (DBManager::is_table_exists("kr_users"))
                return true;
            else
                return false;
        }
        */



        /**
        * Выполняет инициализацию модуля
        **/
        public static function init () {

            /*
            $users = DBManager::select("kr_users", ["*"], "'LIMIT 20'");
            if ($users != false) {
                foreach ($users as $key => $item) {
                    $user = Models::load("User1", false);
                    $user -> fromSource($item);
                    array_push(self::$items, $user);
                }
            }
            */


            $groups = DBManager::select("kr_user_groups", ["*"], "''");
            if ($groups != false) {
                foreach ($groups as $key => $item) {
                    $group = Models::load("UserGroup", false);
                    $group -> fromSource($item);
                    array_push(self::$groups, $group);
                }
            }

            API::add("addUserGroup", "Users", "addGroup");
            API::add("editUserGroup", "Users", "editGroup");
            API::add("deleteUserGroup", "Users", "deleteGroup");
            API::add("editUser", "Users", "editUser");
            API::add("searchUser", "Users", "search");
            API::add("getPageOfUsers", "Users", "getPageOfUsers");
        }



        public static function getInitialData () {
            $result = new stdClass();
            $result -> users = array();
            $result -> groups = array();
            $result -> itemsOnPage = self::$itemsOnPage;

            $total = DBManager::count("kr_users");
            if (!$total) {
                Errors:;push(Errors::ERROR_TYPE_ENGINE, "Users -> getInitialData: Не удалось получить общее количество пользователей");
                return false;
            }
            $result -> total = $total;

            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:
                    $users = DBManager::select("kr_users", ["*"], "LIMIT 25");
                    if ($users != false) {
                        foreach ($users as $key => $item) {
                            $user = Models::construct("User1", false);
                            $user -> fromSource($item);
                            array_push($result -> users, $user);
                        }
                    }
                    break;
                case Krypton::DB_TYPE_ORACLE:
                    $users = DBManager::select("kr_users", ["*"], "!NOWHERE! ORDER BY SURNAME OFFSET 0 ROWS FETCH NEXT ".self::$itemsOnPage." ROWS ONLY");
                    if ($users != false) {
                        foreach ($users as $key => $item) {
                            $user = Models::construct("User1", false);
                            $user -> fromSource($item);
                            array_push($result -> users, $user);
                        }
                    }
                    break;
            }

            $groups = DBManager::select("kr_user_groups", ["*"], "''");
            if ($groups != false) {
                foreach ($groups as $key => $item) {
                    $group = Models::construct("UserGroup", false);
                    $group -> fromSource($item);
                    array_push($result -> groups, $group);
                }
            }

            return $result;
        }





        /**
        * Возвращает массив всех групп пользователей
        **/
        public static function getGroups () {
            return self::$groups;
        }





        /**
        * Добавляет новую группу пользователей
        * @data {object} - объект с информацией о добавляемой группе пользователей
        **/
        public static function addGroup ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> addGroup: Не задан параметр - объект с информацией о добавляемой группе пользователей");
                return false;
            }

            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:

                    $result = DBManager::insert_row("kr_user_groups", ["TITLE"], ["'".$data -> title."'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> addGroup: Не удалось добавить группу пользователей");
                        return false;
                    }

                    $id = mysql_insert_id();
                    $result = DBManager::select("kr_user_groups", ["*"], "ID = $id");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> adGroup: Не удалось выбрать добавленную группу пользователей");
                        return false;
                    }

                    $group = Models::construct("UserGroup", false);
                    $group -> fromSource($result[0]);

                    return $group;
                    break;

                case Krypton::DB_TYPE_ORACLE:

                    $id = DBManager::sequence_next("seq_user_groups");
                    if (!$id) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> install: Не удалось получить следующее значение последовательности 'seq_user_groups'");
                        return false;
                    }

                    $result = DBManager::insert_row("kr_user_groups", ["ID", "TITLE"], [$id, "'".$data -> title."'"]);
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> addGroup: Не удалось добавить группу пользователей");
                        return false;
                    }

                    $result = DBManager::select("kr_user_groups", ["*"], "ID = $id");
                    if (!$result) {
                        Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> adGroup: Не удалось выбрать добавленную группу пользователей");
                        return false;
                    }

                    $group = Models::construct("UserGroup", false);
                    $group -> fromSource($result[0]);

                    return $group;
                    break;
            }


        }





        /**
        * Сохраняет изменения в измененной группе пользователей
        * @data {object} - объект с информацией об изменяемой группе пользователей
        **/
        public function editGroup ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> editGroup: Не задан параметр - объект с информацией об изменяемой группе пользователей");
                return false;
            }

            $result = DBManager::update("kr_user_groups", ["TITLE"], ["'".$data -> title."'"], "ID = ".$data -> id);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> editGroup: Не удалось сохранить изменения измененной группы пользователей");
                return false;
            }

            $result = DBManager::select("kr_user_groups", ["*"], "ID = ".$data -> id);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> editGroup: Не удалось выбрать измененную группу пользователей");
                return false;
            }

            $group = Models::construct("UserGroup", false);
            $group -> fromSource($result[0]);

            return $group;
        }





        /**
        * Удаляет группу пользователей
        * @data {object} - объект с информацией об удаляемой группе пользователей
        **/
        public function deleteGroup ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> deleteGroup: Не задан параметр - объект с информацией об удаляемой группе пользователей");
                return false;
            }

            $id = $data -> id;
            $result = DBManager::delete("kr_user_groups", "ID = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> deleteGroup: Не удалось удалить группу пользователей");
                return false;
            }

            $result = DBManager::update("kr_users", ["USER_GROUP_ID"], [0], "USER_GROUP_ID = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> deleteGroup: Не удалось обновить информацию о пользователях");
                return false;
            }

            return true;
        }



        public static function getAll () {
            return self::$items;
        }



        /**
        * Добавляет нового пользователя
        **/
        public static function add ($user) {
            if ($user == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Не задан параметр - экземпляр класса User");
                return false;
            }

            if (get_class($user) != "User1") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> add: Неверно задан тип парметра - экземпляр класса User");
                    return false;
            }

            $result = DBManager::insert_row(
                "kr_users",
                ["surname", "name", "fname", "position", "email", "phone", "mobile_phone", "password", "is_admin"],
                ["'".$user -> surname -> value."'", "'".$user -> name -> value."'", "'".$user -> fname -> value."'", "'".$user -> position -> value."'", "'".$user -> email -> value."'", "'".$user -> phone -> value."'", "'".$user -> mobile -> value."'", "'".md5($password)."'", intval($user -> isAdmin -> value)]
            );
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> add: Не удалось добавить пользователя");
                return false;
            }

            $id = mysql_insert_id();
            $result = DBManager::select("kr_users", ["*"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> add: Не удалось выбрать добавленного пользователя");
                return false;
            }

            $newUser = Models::construct("User1", false);
            $newUser -> fromSource($result[0]);
            array_push(self::$items, $newUser);
            return $newUser;
        }





        public static function editUser ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "users -> editUser: Не задана параметр - объект с данными редактируемого пользователя");
                return false;
            }

            $id = $data -> id;
            $userGroupId = $data -> userGroupId;
            $organizationId = $data -> organizationId;
            $divisionId = $data -> divisionId;
            $name = $data -> name;
            $fname = $data -> fname;
            $surname = $data -> surname;
            $position = $data -> position;
            $email = $data -> email;
            $phone = $data -> phone;
            $mobile = $data -> mobile;

            $result = DBManager::update(
                "kr_users",
                ["ORGANIZATION_ID", "DIVISION_ID", "NAME", "FNAME", "SURNAME", "POSITION", "EMAIL", "PHONE", "MOBILE_PHONE"],
                [$organizationId, $divisionId, "'".$name."'", "'".$fname."'", "'".$surname."'", "'".$position."'", "'".$email."'", "'".$phone."'", "'".$mobile."'"],
                "ID = $id"
            );
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Users -> edit: Не удалось обновить информацию о пользователе");
                return false;
            }

            $result = DBManager::select("kr_users", ["*"], "ID = $id");
            if (!$result) {
                Errors:;push(Errors::ERROR_TYPE_ENGINE, "Users -> edit: Не удалось выбрать информацию обновленного пользователя");
                return false;
            }

            $user = Models::construct("User1", false);
            $user -> fromSource($result[0]);

            return $user;
        }



        /**
        * Возвращает информацию о пользователе по идентификатору пользователя
        * @userId - Идентификатор пользователя
        **/
        public static function getById ($id) {
            if ($id == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getById: Не задан параметр - идентификатор пользователя");
                return false;
            }

            if (gettype($id) != "integer") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getById: Неверно задан тип параметра - идентификатор пользователя");
                return false;
            }

            $result = DBManager::select("kr_users", ["*"], "ID = $id");
            if (!$result)
                return $result;

            $user = Models::construct("User1", false);
            $user -> fromSource($result[0]);
            return $user;
        }





        /**
        * Возвращает информацию о пользователе по email пользователя
        * @email {string} - email пользователя
        **/
        public static function getByEmail ($email) {
            if ($email == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getByEmail: Не задан параметр - email пользователя");
                return false;
            }

            if (gettype($email) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getByEmail: Неверно задан типа параметра - email пользователя");
                return false;
            }

            $result = DBManager::select("kr_users", ["*"], "email = '$email' LIMIT 1");
            if (!$result)
                return $result;

            $user = Models::construct("User1", false);
            $user -> fromSource($result[0]);
            return $user;
        }


        public static function uploadPhoto () {}


        public static function search ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> search: Не задан параметр - объект с условиями поиска");
                return false;
            }

            $userGroupId = $data -> userGroupId;
            $search = $data -> search;
            $answer = new stdClass();
            $answer -> users = array();
            $answer -> phones = array();

            $result = DBManager::select("kr_users", ["*"], "LOWER(SURNAME) || ' ' || LOWER(NAME) || ' ' || LOWER(FNAME) || ' ' || LOWER(EMAIL) LIKE '%' || LOWER('$search') || '%' AND IS_DISPLAYABLE = 1 ORDER BY SURNAME");
            if ($result != false) {
                //$users = array();
                $length = sizeof($result);
                for ($i = 0; $i < $length; $i++) {
                    $user = Models::construct("User1", false);
                    $user -> fromSource($result[$i]);
                    array_push($answer -> users, $user);
                    $phones = DBManager::select("phones", ["*"], "USER_ID = ".$user -> id -> value);
                    if ($phones != false) {
                        foreach ($phones as $key => $item) {
                            $phone = Models::construct("Phone", false);
                            $phone -> fromSource($item);
                            array_push($answer -> phones, $phone);
                        }
                    }
                }
                return $answer;
            }
        }


        public static function getPageOfUsers ($data) {
            if ($data == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Users -> getPageOfUsers: Не задан аргумент - объект с параметрами");
                return false;
            }

            $start = $data -> start;
            $size = $data -> size;
            $end = $start + $size;
            $result = array();

            switch (Krypton::getDBType()) {
                case Krypton::DB_TYPE_MYSQL:
                    $users = DBManager::select("kr_users", ["*"], "!NOWHERE! LIMIT $start, $size");
                    if ($users != false) {
                        foreach ($users as $key => $item) {
                            $user = Models::construct("User1", false);
                            $user -> fromSource($item);
                            array_push($result, $user);
                        }
                    }
                    break;
                case Krypton::DB_TYPE_ORACLE:
                    $users = DBManager::select("kr_users", ["*"], "!NOWHERE! OFFSET $start ROWS FETCH NEXT $size ROWS ONLY");
                    if ($users != false) {
                        foreach ($users as $key => $item) {
                            $user = Models::construct("User1", false);
                            $user -> fromSource($item);
                            array_push($result, $user);
                        }
                    }
                    break;
            }

            return $result;
        }

    };

?>