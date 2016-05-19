<?php

    class Controller {
        private static $actions = array();


        public function __construct () {
        echo(json_decode(file_get_contents('php://input')));
            if (file_get_contents('php://input') != null) {
                var_dump(file_get_contents('php://input'));
                $postdata = file_get_contents('php://input');
                var_dump(json_decode($postdata));
                if (isset($postdata -> action)) {
                    if (gettype($postdata -> action) != "string") {
                        return Errors::push(Errors::ERROR_TYPE_ENGINE, "Controller -> __construct: Неверно задан типа параметра - действие");
                    } else {
                        echo("test");
                    }
                } else
                    echo("no action");
            } else
                echo("no POST</br>");
        }


        public static function add ($action, $class, $target) {
            if ($action == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Controller -> add: Не задан параметр - действие");
                return false;
            } else {
                if (gettype($action) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Controller -> add: Неверно задан тип параметра - действие");
                    return false;
                } else {
                    if ($class == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Controller -> add: Не задан параметр - наименование класса");
                        return false;
                    } else {
                        if (gettype($class) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Controller -> add: Неверно задан тип параметра - наименование класса");
                            return false;
                        } else {
                            if (!class_exists($class)) {
                                Errors::push(Errors::ERROR_TYPE_ENGINE, "Controller -> add: Класс '".$class."' не найден");
                                return false;
                            } else {
                                if ($target == null) {
                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Controller -> add: Не задан параметр - функция");
                                    return false;
                                } else {
                                    if (gettype($target) != "string") {
                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Controller -> add: Неверно задан тип параметра - целевая функция");
                                    } else {
                                        if (!method_exists($class, $target)) {
                                            Errors::push(Errors::ERROR_TYPE_ENGINE, "Controller -> add: Целевая функция '".$target."' в классе '".$class."' не найдена");
                                            return false;
                                        } else {
                                            $action = new ControllerAction($action, $target);
                                            array_push(self::$actions, $action);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }



        public static function getAll () {
            return self::$actions;
        }



        public static function listen () {
            var_dump(json_decode(file_get_contents('php://input')));
        }


    };

?>