<?php

    class API {
        private static $entries = array();



        public static add ($entry, $class, $method) {
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

            $entry = new stdClass();
            $entry -> entry = $entry;
            $entry -> class = $class;
            $entry -> method = $method;
            array_push(self::$entries, $entry);
        }



        public function entryExists ($entry) {
            if ($entry == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> entryExists: Не задан параметр - точка входа");
            else
                if (gettype($entry) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "API -> entryExists: Неверно задан тип параметра - точка входа");
                else {
                    for ($ent in self::$entries) {
                        if ($ent -> entry == $entry)
                            return true;
                    }
                    return false;
                }
        }



        public static getAll () {
            return self::$entries;
        }
    };

?>