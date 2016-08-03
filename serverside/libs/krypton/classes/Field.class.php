<?php

    class Field {

        public $type = KRYPTON::DATA_TYPE_INTEGER;
        public $source;
        public $value;
        public $defaultValue;

        public function __construct ($parameters) {
            if ($parameters == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Не задан параметр - массив параметров инициализации при создании экземпляра класса");
            else {
                if (gettype($parameters) != "array")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Неверно задан тип параметра - массив параметров инициализации при создании экземпляра класса");
                else {

                    if ($parameters["type"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Не задан параметр - тип данных поля");
                    else {
                        if (gettype($parameters["type"]) != "integer")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Неверно задан тип параметра - тип данных поля");
                        else {
                            if ($parameters["type"] != Krypton::DATA_TYPE_INTEGER && $parameters["type"] != Krypton::DATA_TYPE_FLOAT && $parameters["type"] != Krypton::DATA_TYPE_STRING && $parameters["type"] != Krypton::DATA_TYPE_BOOLEAN)
                                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Неверно задано значение параметра - тип данных поля");
                            else {
                                $this -> type = $parameters["type"];
                            }
                        }
                    }

                    if ($parameters["source"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Не задан параметр - поле-источник данных в БД");
                    else {
                        if (gettype($parameters["source"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Неверно задан тип параметра - поле-источник данных в БД");
                        else {
                            $this -> source = $parameters["source"];
                        }
                    }


                    if ($parameters["value"] == null && $parameters["value"] != 0 && $parameters["value"] != "" && gettype($parameters["value"]) != "boolean")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Не задан параметр - значение поля");
                    else {
                        switch ($this -> type) {
                            case Krypton::DATA_TYPE_INTEGER:
                                if (gettype($parameters["value"]) != "integer")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Тип значения параметра не соответствует типу данных поля");
                                else
                                    $this -> value = intval($parameters["value"]);
                                break;
                            case Krypton::DATA_TYPE_FLOAT:
                                if (gettype($parameters["value"]) != "double")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Тип значения параметра не соответствует типу данных поля");
                                else
                                    $this -> value = floatval($parameters["value"]);
                                break;
                            case Krypton::DATA_TYPE_STRING:
                                if (gettype($parameters["value"]) != "string")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Тип значения параметра не соответствует типу данных поля");
                                else
                                    $this -> value = strval($parameters["value"]);
                                break;
                            case Krypton::DATA_TYPE_BOOLEAN:
                                if (gettype($parameters["value"]) != "boolean")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Тип значения параметра не соответствует типу данных поля");
                                else
                                    $this -> value = boolval($parameters["value"]);
                                break;
                        }
                    }

                    if ($parameters["defaultValue"] == null && gettype($parameters["defaultValue"]) != "boolean" && $parameters["defaultValue"] != 0)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Не задан параметр - значение поля по умолчанию");
                    else {
                        switch ($this -> type) {
                            case Krypton::DATA_TYPE_INTEGER:
                                if (gettype($parameters["value"]) != "integer")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Тип значения параметра не соответствует типу данных поля");
                                else
                                    $this -> defaultValue = intval($parameters["defaultValue"]);
                                break;
                            case Krypton::DATA_TYPE_FLOAT:
                                if (gettype($parameters["defaultValue"]) != "double")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Тип значения параметра не соответствует типу данных поля");
                                else
                                    $this -> defaultValue = floatval($parameters["defaultValue"]);
                                break;
                            case Krypton::DATA_TYPE_STRING:
                                if (gettype($parameters["defaultValue"]) != "string")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Тип значения параметра не соответствует типу данных поля");
                                else
                                    $this -> defaultValue = strval($parameters["defaultValue"]);
                                break;
                            case Krypton::DATA_TYPE_BOOLEAN:
                                if (gettype($parameters["defaultValue"]) != "boolean")
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Field -> __construct: Тип значения параметра не соответствует типу данных поля");
                                else
                                    $this -> defaultValue = boolval($parameters["defaultValue"]);
                                break;
                        }
                    }
                }
            }
        }


    };

?>