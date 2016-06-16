<?php

    class Models {
        public static $items = array();

        public static function load ($model) {
            if ($model == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> load: Не задан параметр - наименование модели");
            else {
                if (gettype($model) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> load: Неверно задан тип параметра - наименование модели");
                else {
                    
                }
            }
        }
    };

?>