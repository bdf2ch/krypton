<?php

    class ControllerAction {
        public $action;
        public $target;


        public function __construct ($action, $target) {
            if ($action == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "ControllerAction -> __construct: Не задан парметр - действие");
                return false;
            } else {
                if (gettype($action) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "ControllerAction -> __construct: Неверно задан тип параметра - действие");
                    return false;
                } else {
                    if ($target == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "ControllerAction -> __construct: Не задан параметр - целевая функция");
                        return false;
                    } else {
                        if (gettype($target) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "ControllerAction -> __construct: Неверно задан тип параметра - целевая функция");
                            return false;
                        } else {
                            $this -> action = $action;
                            $this ->target = $target;
                        }
                    }
                }
            }
        }


    };

?>