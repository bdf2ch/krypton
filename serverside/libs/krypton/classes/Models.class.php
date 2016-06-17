<?php

    class Models {
        public static $items = array();



        public static function construct ($model, $parameters) {
            if ($model == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> construct: Не задан параметр - наименование модели");
            else
                if (gettype($model) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> load: Неверно задан тип параметра - наименование модели");

            if ($parameters == null && gettype($parameters) != "boolean") {
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> load: Не задан параметр - массив параметров инициализации модели");
            }

            if (file_exists( $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/models/".$model.".model.php")) {
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/models/".$model.".model.php";
                $temp = new $model($parameters);
                return $temp;
            } else
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> load: Модель '".$model."' не найдена");
        }



        public static function extend ($model, $title, $field) {
            if ($model == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> extend: Не задан параметр - наименование модели данных");
            else
                if (gettype($model) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> extend: Неверно задан тип параметра - наименование модели данных");

            if ($title == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> extend: Не задан параметр - наименование поля модели данных");
            else
                if (gettype($title) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> extend: Неверно задан тип параметра - наименование поля модели данных");

            if ($field == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> extend: Не задан параметр - экземпляр класса Field");
            else
                if (gettype($field) != "object")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> extend: Неверно задан тип параметра - экземпляр класса Field");
                else
                    if (get_class($field) != "Field")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> extend: Неверно задан тип параметра - экземпляр класса Field");

            if (file_exists( $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/models/".$model.".model.php")) {
                require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/models/".$model.".model.php";
                //$temp = new $model();
                $model::field($title, $field);
                return true;
            } else
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> load: Модель '".$model."' не найдена");
        }


        //public static function toJSONCollection ($) {
        //                $collection = array();
        //}
    };

?>