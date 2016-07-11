<?php

        class Model {

            public function __construct () {
                $class = get_called_class();
                foreach ($class::$fields as $key => $property) {
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
                        else {
                            $class = get_called_class();
                            $class::$fields[$title] = $field;
                            //echo($class." -> ".$title."</br>");
                            //var_dump($class::$fields);
                        }
                    }
                }
            }



            public function fromSource ($data) {
                if ($data == null)
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Не задан параметр - набор данных");
                else
                    if (gettype($data) != "array")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Неверно задан тип параметра - набор данных");
                $class = get_called_class();
                foreach ($class::$fields as $key => $field) {
                    if (array_key_exists($field -> source, $data)) {
                        switch ($field -> type) {
                            case Krypton::DATA_TYPE_INTEGER:
                                if (!is_numeric($data[$field -> source]))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Тип значения параметра в наборе данных не соответствует типу значения свойства модели данных (int)");
                                else {
                                    $field -> value = intval($data[$field -> source]);
                                    $this -> $key -> value = intval($data[$field -> source]);
                                }
                                break;
                            case Krypton::DATA_TYPE_FLOAT:
                                if (gettype($data[$field -> source]) != "double")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Тип значения параметра в наборе данных не соответствует типу значения свойства модели данных (float)");
                                else {
                                    $field -> value = floatval($data[$field -> source]);
                                    $this -> $key -> value = floatval($data[$field -> source]);
                                }
                                break;
                            case Krypton::DATA_TYPE_STRING:
                                if (gettype($data[$field -> source]) != "string")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Тип значения параметра в наборе данных не соответствует типу значения свойства модели данных {string}");
                                else {
                                    $field -> value = strval($data[$field -> source]);
                                    $this -> $key -> value = strval($data[$field -> source]);
                                }
                                break;
                            case Krypton::DATA_TYPE_BOOLEAN:
                                if (!is_numeric($data[$field -> source]))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromSource: Тип значения параметра в наборе данных не соответствует типу значения свойства модели данных (bool)");
                                else {
                                    $field -> value = boolval($data[$field -> source]);
                                    $this -> $key -> value = boolval($data[$field -> source]);
                                }
                                break;
                        }
                        //self::$fields[$key] -> value = $data[$key];
                        //$this -> $key -> value = $data[$key];
                        //$this -> __set($key, );
                    } else {
                        $class::$fields[$key] -> value = $class::$fields[$key] -> defaultValue;
                        $this -> $key -> value  = $class::$fields[$key] -> defaultValue;
                    }
                }
            }



            public static function fromAnother ($another) {
                if ($another == null) {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromAnother: Не задан параметр - объект-источник инициализации");
                    return false;
                }

                if (gettype($another) != "object") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Model -> fromAnother: Неверно задан тип параметра - объект-источник инициализации");
                    return false;
                }


            }



            public static function toJSON () {
                $obj = new stdClass();
                $class = get_called_class();
                foreach ($class::$fields as $key => $field) {
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
                    $temp = Models::construct($class, false);
                    $temp -> fromSource($element);
                    array_push($collection, $temp);
                }
                return json_encode($collection);
            }

        };

?>