<?php

        class Model {
            public static $fields = array();



            public static function extend ($title, $field) {
                if ($title == null)
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> extend: Не задан параметр - наименование поля данных модели");
                else
                    if (gettype($title) != "string")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> extend: Неверно задан тип параметра - наименование поля данных модкли");

                if ($field == null)
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> extend: Не задан параметр - экземпляр класса Field");
                else {
                    if (gettype($field) != "object")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> extend: Неверно задан тип параметра - экземпляр класса Field");
                    else {
                        if (get_class($field) != "Field")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> extend: Неверно задан тип параметра - экземпляр класса Field");
                        else {
                            self::$field[$title] = $field;
                        }
                    }
                }
            }



            public static function toJSON () {
                $obj = new stdObj();
                foreach (self::$field as $key => $field) {
                    if (get_class($field) == "Field") {
                        $obj -> $key = $field -> value;
                    }
                }
                return json_encode($obj);
            }

        };

?>