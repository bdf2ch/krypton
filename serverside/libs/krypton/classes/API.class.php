<?php

    class API {
        private static $entries = array();


        public static function init () {}


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
                    //echo("4ntry = ".$entry);
                    $isEntryExists = self::getEntry($entry);
                    //var_dump($isEntryExists);
                    if ($isEntryExists != false) {
                        $callable = $isEntryExists -> class."::".$isEntryExists -> method;
                        //echo($callable."</br>");
                        $result = call_user_func(array($isEntryExists -> class, $isEntryExists -> method));
                        echo(json_encode($result));
                    }
                }

        }



        public static function getAll () {
            return self::$entries;
        }
    };

?>