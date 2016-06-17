<?php

    class Department extends Model {
        public function __construct ($parameters) {
            self::field("id", new Field(array( "source" => "id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("title", new Field(array( "source" => "surname", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));

            if (is_null($parameters))
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Department -> __construct: Не задан параметр - массив параметров инициализации");
            else {
                switch (gettype($parameters)) {
                    case "array":
                        foreach (self::$fields as $key => $field) {
                            if (array_key_exists($key, $parameters)) {
                                if (gettype($parameters[$key]) != gettype($field -> value))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Department -> __construct: Тип значения параметра инициализации не соответствует типу значения свойства модели данных");
                                else
                                    self::$fields[$key] -> value = $parameters[$key];
                            }
                        }
                        break;
                    case "boolean":
                        if ($parameters != false)
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Department -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                    default:
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> Department -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                }
                parent::__construct();
            }
        }
    };

?>