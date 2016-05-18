<?php

    class Setting {
        public $moduleId;
        public $code;
        public $title;
        public $description;
        public $dataType;
        public $isSystem = false;
        public $value;


        public function __construct ($module, $code, $title, $dataType, $value, $description, $isSystem) {
            if ($module == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Не задан параметр - идентификатор модуля");
                return false;
            } else {
                if (gettype($module) != "string") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Неверно задан тип параметра - идентификатор модуля");
                    return false;
                } else {
                    if ($code == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Не задан параметр - код настройки");
                        return false;
                    } else {
                        if (gettype($code) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Неверно задан типа параметра - код настройки");
                            return false;
                        } else {
                            if ($title == null) {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Не задан параметр - наименование настройки");
                                return false;
                            } else {
                                if (gettype($title) != "string") {
                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Неверно задан тип параметра - наименование настройки");
                                    return false;
                                } else {
                                    if ($dataType == null) {
                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Не задан параметр - типа данных настройки");
                                        return false;
                                    } else {
                                        if (gettype($dataType) != "integer") {
                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Неверно задан тип параметра - тип данных настройки");
                                            return false;
                                        } else {
                                            if ($dataType != Krypton::DATA_TYPE_INTEGER || $dataType != Krypton::DATA_TYPE_STRING || $dataType != Krypton::DATA_TYPE_BOOLEAN || $dataType != Krypton::DATA_TYPE_FLOAT) {
                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Неверно задано значение параметра - тип данных настройки");
                                                return false;
                                            } else {
                                                $this -> $module = $module;
                                                $this -> code = $code;
                                                $this -> title = $title;
                                                $this -> dataType = $dataType;

                                                if ($value != null) {
                                                    switch ($dataType) {
                                                        case Krypton::DATA_TYPE_INTEGER:
                                                            if (gettype($value) != "integer") {
                                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Тип значения настройки не соответствует типу данных настройки");
                                                                return false;
                                                            }
                                                            break;
                                                        case Krypton::DATA_TYPE_STRING:
                                                            if (gettype($value) != "string") {
                                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Тип значения настройки не соответствует типу данных настройки");
                                                                return false;
                                                            }
                                                            break;
                                                        case Krypton::DATA_TYPE_BOOLEAN:
                                                            if (gettype($value) != "boolean") {
                                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Тип значения настройки не соответствует типу данных настройки");
                                                                return false;
                                                            }
                                                            break;
                                                        case Krypton::DATA_TYPE_FLOAT:
                                                            if (gettype($value) != "double") {
                                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Тип значения настройки не соответствует типу данных настройки");
                                                                return false;
                                                            }
                                                            break;
                                                    }
                                                    $this -> value = $value;
                                                }


                                                if ($description != null && gettype($description) != "string") {
                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Неверно задан тип параметра - описание настройки");
                                                    return false;
                                                } else
                                                    $this -> description = $description;

                                                if ($isSystem != null && gettype($isSystem) != "boolean") {
                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "Setting -> __construct: Неверно задан тип параметра - является ли настройки системной");
                                                    return false;
                                                } else
                                                    $this -> isSystem = $isSystem;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    };

?>