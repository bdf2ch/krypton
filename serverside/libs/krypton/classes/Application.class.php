<?php

    class Application {
        public $title;
        public $description;
        public $inDebugMode = false;
        public $inConstructionMode = false;
        public $extensions = array();
        private $initials = array();
        private $scripts = array();



        public function init () {
            $info = DBManager::select("kr_app_info", ["*"], "''");
            if ($info != false) {
                $this -> title = $info[0]["title"];
                $this -> description = $info[0]["description"];
                $this -> inDebugMode = boolval($info[0]["is_in_debug_mode"]);
                $this -> inConstructionMode = boolval($info[0]["is_in_construction_mode"]);
            }

                $extensions = DBManager::select("kr_app_extensions", ["*"], "''");
                //var_dump($extensions);
                if ($extensions != false) {
                    foreach ($extensions as $key => $ext) {
                        $extension = Models::construct("Extension", false);
                        $extension -> fromSource($ext);
                        array_push($this -> extensions, $extension);
                    }
                    var_dump($this);
                } else
                    return false;
        }



        /**
        * Проверяет, загружено ли расширение
        * @extension {string} - наименование расширения
        **/
        public function isExtensionLoaded ($extension) {
            if ($extension == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> isExtensionLoaded: Не задан параметр - наименование расширения");

            if (gettype($extension) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> isExtensionLoaded: Неверно задан тип параметра - наименование расширения");

            foreach ($this -> extensions as $key => $ext) {
                if ($ext -> id -> value == $extension)
                    return true;
            }

            return false;
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



        /**
        * Добавляет инийиализационные данные на клиентскую часть приложения
        * @var {string} - наименование переменной на клиентской чтороне приложения
        * @data {any} - данные, которыми требуется инициализировать переменную
        **/
        function addInitData ($var, $data) {
            if ($var == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> addInitData: Не задан параметр - наименование переменной");

            if (gettype($var) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> addInitData: Неверно задан тип параметра - наименование переменной");

            if ($data == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> addInitData: Не задан параметр - данные для стартовой инициализации");

            $this -> initials[$var] = $data;

            return true;
        }



        /**
        * Добавляет javascript-файл в клиентскую часть приложения
        * @url {string} - url файла
        **/
        function addJavaScript ($url) {
            if ($url == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> addJavaScript: Не задан параметр - url скрипта");

            if (gettype($url) != "string")
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Application -> addJavaScript: Неверно задан типа параметра - url скрипта");

            array_push($this -> scripts, $url);

            return true;
        }

    };

?>