<?php

    class Extension extends Model {
        public static $fields = array();

        public function __construct ($parameters) {
            self::field("id", new Field(array( "source" => "EXTENSION_ID", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("title", new Field(array( "source" => "EXTENSION_TITLE", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("description", new Field(array( "source" => "EXTENSION_DESCRIPTION", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("url", new Field(array( "source" => "EXTENSION_URL", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("enabled", new Field(array( "source" => "ENABLED", "type" => Krypton::DATA_TYPE_BOOLEAN, "value" => true, "defaultValue" => true )));


            if (is_null($parameters))
                 return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Extension -> __construct: Не задан параметр - массив параметров инициализации");
            else {
                $class = get_called_class();
                //echo(gettype($parameters)."</br>");
                switch (gettype($parameters)) {
                    case "array":
                        foreach ($class::$fields as $key => $field) {
                            if (array_key_exists($key, $parameters)) {
                                if (gettype($parameters[$key]) != gettype($field -> value))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Extension -> __construct: Тип значения параметра инициализации не соответствует типу значения свойства модели данных");
                                else
                                    $class::$fields[$key] -> value = $parameters[$key];
                            }
                        }
                        break;
                    case "boolean":
                        if ($parameters != false)
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Extension -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                    //default:
                    //    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Extension -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                    //    break;
                }
                parent::__construct();
            }
        }


        public function getUrl () {
                    if (defined("ENGINE_ADMIN_MODE"))
                        return "../../".$this -> url -> value;
                    else
                        return $this -> url -> value;
        }


    };

?>