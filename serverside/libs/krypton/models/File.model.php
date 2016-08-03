<?php

    class File extends Model {
        public static $fields = array();

        public function __construct ($parameters) {
            self::field("title", new Field(array( "source" => "NAME", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("type", new Field(array( "source" => "TYPE", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("size", new Field(array( "source" => "SIZE", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("isFolder", new Field(array( "source" => "IS_FOLDER", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("url", new Field(array( "source" => "URL", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));


            if (is_null($parameters))
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> File -> __construct: Не задан параметр - массив параметров инициализации");
            else {
                $class = get_called_class();
                switch (gettype($parameters)) {

                    case "array":
                        foreach ($class::$fields as $key => $field) {
                            if (array_key_exists($key, $parameters)) {
                                if (gettype($parameters[$key]) != gettype($field -> value))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> File -> __construct: Тип значения параметра инициализации не соответствует типу значения свойства модели данных");
                                else
                                    $class::$fields[$key] -> value = $parameters[$key];
                            }
                        }
                        break;
                    case "boolean":
                        if ($parameters != false)
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> File -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                    default:
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> File -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                }
                parent::__construct();
            }
        }


    };

?>