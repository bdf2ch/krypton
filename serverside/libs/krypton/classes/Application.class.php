<?php

    class Application {
        public $title;
        public $description;
        public $inDebugMode = false;
        public $inConstructionMode = false;



        /*
        public function __construct ($appTitle, $appDescription) {
            if ($appTitle == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> __construct: Не задан параметр - наименование приложения");
            else {
                if (gettype($appTitle) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> __construct: Неверно задан тип параметра - наименование приложения");
                else {
                    $this -> title = $appTitle;

                    if (appDescription != null && gettype($appDescription) != "string")
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> __construct: Неверно задан тип параметра - описание приложения");
                    else
                        $this -> description = $appDescription;
                }
            }
        }
        */

        public function __construct (){}


        public function init () {
            if (DBManager::is_connected()) {
                $info = DBManager::select("kr_app_info", ["*"], "''");
                if ($info != false) {
                    $this -> title = $info[0]["title"];
                    $this -> description = $info[0]["description"];
                    $this -> inDebugMode = boolval($info[0]["is_in_debug_mode"]);
                    $this -> inConstructionMode = boolval($info[0]["is_in_construction_mode"]);
                    return true;
                } else
                    return false;
            }
        }



        public function get ($property) {
            if ($property == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> get: Не задан параметр - наименование свойства");
            else {
                if (gettype($property) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> get: Неверно задан тип параметра - наименование свойства");
                else {
                    if (property_exists(get_class(), $property) == false)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> get: Свойство '".$property."' не найдено");
                    else {
                        return $this -> $property;
                    }
                }
            }
        }



        public function set ($property, $value) {
            if ($property == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> set: Не задан параметр - наименование свойства");
            else {
                if (gettype($property) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> set: Неверно задан тип параметра - наименование настройки");
                else {
                    if (property_exists(get_class(), $property) == false)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> set: Свойство '".$property."' не найдено");
                    else {
                        if ($value == null && gettype($value) != "boolean")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> set: Не задан параметр - значение ствойства");
                        else {
                            switch ($property) {
                                case "title":
                                    if (DBManager::update("kr_app_info", ["title"], ["'".strval($value)."'"], ""))
                                        $this -> title = $value;
                                    break;
                                case "description":
                                    if (DBManager::update("kr_app_info", ["description"], ["'".strval($value)."'"], ""))
                                        $this -> description = $value;
                                    break;
                                case "inDebugMode":
                                    if (gettype($value) != "boolean")
                                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> set: Неверно задан тип параметра - значение ствойства (inDebugMode)");
                                    else {
                                        if (DBManager::update("kr_app_info", ["is_in_debug_mode"], [intval($value)], "")) {
                                            Settings::setByCode("app_debug_mode", $value);
                                            $this -> inDebugMode = $value;
                                        }
                                    }
                                    break;
                                case "inConstructionMode":
                                    if (gettype($value) != "boolean")
                                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> set: Неверно задан типа параметра - значение свойства (inConstructionMode)");
                                    else {
                                        if (DBManager::update("kr_app_info", ["is_in_construction_mode"], [intval($value)], "")) {
                                            Settings::setByCode("app_construction_mode", $value);
                                            $this -> inConstructionMode = $value;
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }

    };

?>