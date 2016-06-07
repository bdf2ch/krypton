<?php

    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/config.php";

    class Controller {
        private static $actions = array();


        public function __construct ($className) {
            if ($className == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Controller -> __construct: Не задан параметр - наименование класса");
                return false;
            } else {
                if (gettype($className) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Controller -> __construct: Неверно задан тип параметра - наименование класса");
                    return false;
                } else {
                    if (file_get_contents('php://input') != null) {
                        $postdata = json_decode(file_get_contents('php://input'), true);
                        if (isset($postdata["action"])) {
                            if (gettype($postdata["action"]) != "string") {
                                return Errors::push(Errors::ERROR_TYPE_ENGINE, "Controller -> __construct: Неверно задан тип параметра - действие");
                            } else {
                                if (method_exists($className, $postdata["action"])) {
                                    if (isset($postdata["parameters"])) {
                                        $parameters = array();
                                        foreach ($postdata["parameters"] as $key => $param) {
                                            array_push($parameters, $param);
                                        }
                                        //var_dump($parameters);
                                        call_user_func_array($className."::".$postdata["action"], $parameters);
                                    } else {
                                        call_user_func($className."::".$postdata["action"]);
                                    }

                                } else
                                    return Errors::push(Errors::ERROR_TYPE_ENGINE, "Controller -> __construct: Метод '".$postdata["action"]."' не найден в контроллере '".$className."'");
                            }
                        }
                    }
                }
            }
        }



        public static function getAll () {
            return self::$actions;
        }


    };




?>