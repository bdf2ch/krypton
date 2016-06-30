<?php

    class Services {
        private static $items = array();

        public static function register ($service) {
            if ($service == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Services -> register: Не задан параметр - наименование сервиса");

            if (gettype($service) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Services -> register: Неверно задан тип параметра - наименование сервиса");

            array_push(self::$items, $service);
            return true;
        }

    };

?>