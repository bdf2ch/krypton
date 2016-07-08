<?php

    class User1 extends Model {
        public static $fields = array();

        public function __construct ($parameters) {
            self::field("id", new Field(array( "source" => "id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("surname", new Field(array( "source" => "surname", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("name", new Field(array( "source" => "name", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("fname", new Field(array( "source" => "fname", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("position", new Field(array( "source" => "position", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("email", new Field(array( "source" => "email", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("phone", new Field(array( "source" => "phone", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("mobile", new Field(array( "source" => "mobile_phone", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("photo", new Field(array( "source" => "photo_url", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("isAdmin", new Field(array( "source" => "is_admin", "type" => Krypton::DATA_TYPE_BOOLEAN, "value" => false, "defaultValue" => false )));

            if (is_null($parameters))
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> User -> __construct: Не задан параметр - массив параметров инициализации");
            else {
                switch (gettype($parameters)) {
                    case "array":
                        foreach (self::$fields as $key => $field) {
                            if (array_key_exists($key, $parameters)) {
                                if (gettype($parameters[$key]) != gettype($field -> value))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> User -> __construct: Тип значения параметра инициализации не соответствует типу значения свойства модели данных");
                                else
                                    self::$fields[$key] -> value = $parameters[$key];
                            }
                        }
                        break;
                    case "boolean":
                        if ($parameters != false)
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> User -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                    default:
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> User -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                }
                parent::__construct();
            }
        }

    };

?>