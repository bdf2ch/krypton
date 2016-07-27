<?php

    class Permissions extends Service {
        public static $rules = array();
        public static $permissions = array();





        public function __construct () {
            parent::__construct();
        }




        /**
        * Производит установку сервиса
        **/
        public static function install () {
            $result = DBManager::create_table("kr_permission_rules");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Permissions -> install: Не удалось создать таблицу правил доступа");
                return false;
            }

            $result = DBManager::add_column("kr_permission_rules", "code", "varchar(200) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Permissions -> install: Не удалось добавить столбец 'code' таблицу правил доступа");
                return false;
            }

            $result = DBManager::add_column("kr_permission_rules", "title", "varchar(500) NOT NULL default ''");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Permissions -> install: Не удалось добавить столбец 'title' таблицу правил доступа");
                return false;
            }

            $result = DBManager::create_table("kr_permissions");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Permissions -> install: Не удалось создать таблицу прав доступа");
                return false;
            }

            $result = DBManager::add_column("kr_permissions", "rule_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Permissions -> install: Не удалось добавить столбец 'rule_id' таблицу прав доступа");
                return false;
            }

            $result = DBManager::add_column("kr_permissions", "user_id", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Permissions -> install: Не удалось добавить столбец 'user_id' таблицу прав доступа");
                return false;
            }

            $result = DBManager::add_column("kr_permissions", "allowed", "int(11) NOT NULL default 0");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_DATABASE, "Permissions -> install: Не удалось добавить столбец 'allowed' таблицу прав доступа");
                return false;
            }

            return true;
        }





        /**
        * Производит инициализацию сервиса
        **/
        public static function init () {
            Services::register(get_called_class());

            $rules = DBManager::select("kr_permission_rules", ["*"], "");
            if ($rules != false) {
                foreach ($rules as $key => $item) {
                    $rule = Models::construct("PermissionRule", false);
                    $rule -> fromSource($item);
                    array_push(self::$rules, $rule);
                }
            }

            $permissions = DBManager::select("kr_permissions", ["*"], "");
            if ($permissions != false) {
                foreach ($permissions as $key => $item) {
                    $permission = Models::construct("Permission", false);
                    $permission -> fromSource($item);
                    array_push(self::$permissions, $permission);
                }
            }

            return true;
        }





        /**
        * Возвращает массив всех прав доступа
        **/
        public static function getAllPermissions () {
            return self::$permissions;
        }




        /**
        * Добавляет новое разрешение
        * @code {string} - код правила доступа
        * @userId {integer} - идентификатор пользователя
        * @allow {boolean} - разрешение доступа
        **/
        public static function addPermission ($code, $userId, $allow) {
            if ($code == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermission: Не задан парметр - код правила");
                return false;
            }

            if (gettype($code) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermission: Неверно задан тип параметра - код правила");
                return false;
            }

            if ($userId == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermission: Не задан парметр - идентификатор пользователя");
                return false;
            }

            if (gettype($userId) != "integer") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermission: Неверно задан тип параметра - идентификатор пользователя");
                return false;
            }

            if ($allow == null && $allow != false) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermission: Не задан парметр - разрешение доступа");
                return false;
            }

            if (gettype($allow) != "boolean") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermission: Неверно задан тип параметра - код правила");
                return false;
            }

            $rule = self::getRuleByCode($code);
            if (!$rule) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Permissions -> addPermission: Правило доступа с кодом '".$code."' не найдено");
                return false;
            }

            $user = Users::getById($userId);
            if (!$user) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Permissions -> addPermission: Пользователь с идентификатором ".$userId." не найден");
                return false;
            }

            $result = DBManager::insert("kr_permissions", ["code", "user_id", "allowed"], ["'".$code."'", $userId, intval($allow)]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Permissions -> addPermission: Не удалось добавить разрешение в БД");
                return false;
            }

            $id = mysql_insert_id();
            $result = DBManager::select("kr_permissions", ["*"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Permissions -> addPermission: Не удвлось выбрать добавленное разрешение");
                return false;
            }

            $permission = Models::construct("Permission", false);
            $permission -> fromSource($result[0]);

            return $permission;
        }





        /**
        * Возвращает массив всех правил доступа
        **/
        public static function getAllRules () {
            return self::$rules;
        }





        /**
        * Возвращает правило доступа по его коде
        * @code {string} - код праавила доступа
        **/
        public static function getRuleByCode ($code) {
             if ($code == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> getRuleByCode: Не задан парметр - код правила");
                return false;
             }

             if (gettype($code) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> getRuleByCode: Неверно задан тип параметра - код правила");
                return false;
             }

             foreach (self::$rules as $key -> $rule) {
                if ($rule -> code -> value === $code)
                    return $rule;
             }

             return false;
        }





        /**
        * Добавляет новое правило доступа
        * @code {string} - код правила
        * @title {string} - наименование правила
        **/
        public static function addPermissionRule ($code, $title) {
            if ($code == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermissionRule: Не задан парметр - код правила");
                return false;
            }

            if (gettype($code) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermissionRule: Неверно задан тип параметра - код правила");
                return false;
            }

            if ($title == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermissionRule: Не задан парметр - наименование правила");
                return false;
            }

            if (gettype($title) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Permissions -> addPermissionRule: Неверно задан тип параметра - наименование правила");
                return false;
            }

            $result = DBManager::insert_row("kr_permission_rules", ["code", "title"], ["'".$code."'", "'".$title."'"]);
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Permissions -> addPermissionRule: Не удалось добавить правило в БД");
                return false;
            }

            $id = mysql_insert_id();
            $result = DBManager::select("kr_permission_rules", ["*"], "id = $id");
            if (!$result) {
                Errors::push(Errors::ERROR_TYPE_ENGINE, "Permissions -> addPermissionRule: Не удалось выбрать добавленное правило из БД");
                return false;
            }

            $rule = Models::construct("PermissionRule", false);
            $rule -> fromSource($result[0]);

            return $rule;
        }


    };


?>