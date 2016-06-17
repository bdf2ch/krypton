<?php

        class Model {
            public static $fields = array();



            public function __construct () {
                foreach (self::$fields as $key => $property) {
                    $this -> {$key} = $property;
                }
            }



            public function __set ($title, $value) {
                $this -> $title = $value;
            }



            public function field ($title, $field) {
                if ($title == null)
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> field: Не задан параметр - наименование поля данных модели");
                else
                    if (gettype($title) != "string")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> field: Неверно задан тип параметра - наименование поля данных модкли");

                if ($field == null)
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> field: Не задан параметр - экземпляр класса Field");
                else {
                    if (gettype($field) != "object")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> field: Неверно задан тип параметра - экземпляр класса Field");
                    else {
                        if (get_class($field) != "Field")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> field: Неверно задан тип параметра - экземпляр класса Field");
                        else
                            self::$fields[$title] = $field;
                    }
                }
            }



            public function fromSource ($data) {
                if ($data == null)
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Не задан параметр - набор данных");
                else
                    if (gettype($data) != "array")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Неверно задан тип параметра - набор данных");

                foreach (self::$fields as $key => $field) {
                    if (array_key_exists($key, $data)) {
                        switch (self::$fields[$key] -> type) {
                            case Krypton::DATA_TYPE_INTEGER:
                                if (!is_numeric($data[$key]))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Тип значения параметра в наборе данных не соответствует типу значения свойства модели данных (int)");
                                else {
                                    self::$fields[$key] -> value = intval($data[$key]);
                                    $this -> $key -> value = intval($data[$key]);
                                }
                                break;
                            case Krypton::DATA_TYPE_FLOAT:
                                if (gettype($data[$key]) != "double")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Тип значения параметра в наборе данных не соответствует типу значения свойства модели данных (float)");
                                else {
                                    self::$fields[$key] -> value = floatval($data[$key]);
                                    $this -> $key -> value = floatval($data[$key]);
                                }
                                break;
                            case Krypton::DATA_TYPE_STRING:
                                if (gettype($data[$key]) != "string")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Тип значения параметра в наборе данных не соответствует типу значения свойства модели данных {string}");
                                else {
                                    self::$fields[$key] -> value = strval($data[$key]);
                                    $this -> $key -> value = strval($data[$key]);
                                }
                                break;
                            case Krypton::DATA_TYPE_BOOLEAN:
                                if (gettype($data[$key]) != "boolean")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Тип значения параметра в наборе данных не соответствует типу значения свойства модели данных (bool)");
                                else {
                                    self::$fields[$key] -> value = boolval($data[$key]);
                                    $this -> $key -> value = boolval($data[$key]);
                                }
                                break;
                        }
                        //self::$fields[$key] -> value = $data[$key];
                        //$this -> $key -> value = $data[$key];
                        //$this -> __set($key, );
                    } else {
                        self::$fields[$key] -> value = self::$fields[$key] -> defaultValue;
                        $this -> $key -> value  = self::$fields[$key] -> defaultValue;
                    }
                }
            }





            public static function toJSON () {
                $obj = new stdClass();
                foreach (self::$fields as $key => $field) {
                    if (get_class($field) == "Field") {
                        $obj -> $key = $field -> value;
                    }
                }
                return json_encode($obj);
            }


            public static function fromSourceArrayToJSON ($array) {
                $collection = array();
                foreach ($array as $key => $element) {
                    $class = get_called_class();
                    $temp = new $class();
                    $temp = Models::construct($class, false);
                    $temp -> fromSource($element);
                    array_push($collection, $temp);
                }
                return json_encode($collection);
            }

        };

?>