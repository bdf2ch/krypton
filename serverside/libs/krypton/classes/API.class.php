<?php

    class API {
        private static $entries = array();


        public function __construct () {
             global $db_host;
             global $db_name;
             global $db_user;
             global $db_password;

             $result = DBManager::connect($db_host, $db_user, $db_password);
             if (!$result)
                Errors::push(Errors::ERROR_TYPE_DATABASE, "API -> __construct: Не удалось установить соединени с БД");

             $result = DBManager::select_db("krypton");
             if (!$result)
                Errors::push(Errors::ERROR_TYPE_DATABASE, "API -> __construct: Не удалось выбрать БД");
        }


        public static function add ($entry, $class, $method) {
            if ($entry == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> add: Не задан параметр - точка входа");
            else
                if (gettype($entry) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> add: Неверно задан тип параметра - точка входа");

            if ($class == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> add: Не задан параметр - класс/сервис");
            else
                if (gettype($class) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> add: Неверно задан тип параметра - класс/сервис");
                else if (class_exists($class) == false)
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> add: Класс '".$class."' не найден");

            if ($method == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> add: Не задан параметр - метод");
            else
                if (gettype($method) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> add: Неверно задан тип параметра - метод");
                else
                    if (method_exists($class, $method) == false)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> add: Метод '".$method."' не найден");

            $newEntry = new stdClass();
            $newEntry -> entry = $entry;
            $newEntry -> class = $class;
            $newEntry -> method = $method;
            array_push(self::$entries, $newEntry);
            //var_dump(self::$entries);
        }



        public static function getEntry ($entry) {
            if ($entry == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> entryExists: Не задан параметр - точка входа");
            else
                if (gettype($entry) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> entryExists: Неверно задан тип параметра - точка входа");
                else {
                    //var_dump(self::$entries);
                    foreach (self::$entries as $key => $apiEntry) {
                        if ($apiEntry -> entry == $entry)
                            return $apiEntry;
                    }
                    return false;
                }
        }



        public static function call ($entry, $data) {
            if ($entry == null) {
                echo(json_encode(Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> call: Не задан параметр - точка входа")));
                return false;
            } else
                if (gettype($entry) != "string") {
                    echo(json_encode(Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> call: Неверно задан тип параметра - точка входа")));
                    return false;
                } else {
                    $isEntryExists = self::getEntry($entry);
                    if ($isEntryExists != false) {
                        $callable = $isEntryExists -> class."::".$isEntryExists -> method;
                        $result = call_user_func(array($isEntryExists -> class, $isEntryExists -> method));
                        echo(json_encode($result));
                    }
                }

        }


        public static function isExists ($entry) {
            if ($entry == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> isExists: Не задан параметр - точка входа");
                return null;
            }

            if (gettype($entry) != "string") {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> isExists: Неверно задан тип параметра - точка входа");
                return null;
            }

            foreach (self::$entries as $key => $ent) {
                if ($ent -> entry == $entry)
                    return true;
            }

            return false;
        }


        //public function send ($result) {
        //    if ($result == null)
        //        return Errors::push();
        //}



        public static function getAll () {
            return self::$entries;
        }
    };

?>