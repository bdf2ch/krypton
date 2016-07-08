<?php

    class Setting extends Model {
        public static $fields = array();

        public function __construct ($parameters) {
            self::field("extensionId", new Field(array( "source" => "extensions_id", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("code", new Field(array( "source" => "code", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("title", new Field(array( "source" => "title", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("description", new Field(array( "source" => "description", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("type", new Field(array( "source" => "type", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 2, "defaultValue" => 2 )));
            self::field("value", new Field(array( "source" => "value", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));


            if (is_null($parameters))
                 return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Session -> __construct: Не задан параметр - массив параметров инициализации");
            else {
                $class = get_called_class();
                switch (gettype($parameters)) {
                    case "array":
                        foreach ($class::$fields as $key => $field) {
                            if (array_key_exists($key, $parameters)) {
                                if (gettype($parameters[$key]) != gettype($field -> value))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Session -> __construct: Тип значения параметра инициализации не соответствует типу значения свойства модели данных");
                                else
                                    $class::$fields[$key] -> value = $parameters[$key];
                            }
                        }
                        break;
                    case "boolean":
                        if ($parameters != false)
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Session -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                    default:
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Session -> __construct: Неверно заДан тип параметра - массив параметров инициализации");
                        break;
                }
                parent::__construct();
            }
        }


    };

?>