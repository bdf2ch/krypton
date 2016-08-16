<?php

    class SubnetMask extends Model {
        public static $fields = array();

        public function __construct ($parameters) {
            self::field("id", new Field(array( "source" => "ID", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("organizationId", new Field(array( "source" => "ORGANIZATION_ID", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("mask", new Field(array( "source" => "MASK", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));



            if (is_null($parameters))
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> SubnetMask -> __construct: Не задан параметр - массив параметров инициализации");
            else {
                $class = get_called_class();
                switch (gettype($parameters)) {

                    case "array":
                        foreach ($class::$fields as $key => $field) {
                            if (array_key_exists($key, $parameters)) {
                                if (gettype($parameters[$key]) != gettype($field -> value))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> SubnetMask -> __construct: Тип значения параметра инициализации не соответствует типу значения свойства модели данных");
                                else
                                    $class::$fields[$key] -> value = $parameters[$key];
                            }
                        }
                        break;
                    case "boolean":
                        if ($parameters != false)
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> SubnetMask -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                    default:
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "Models -> SubnetMask -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                        break;
                }
                parent::__construct();
            }
        }


    };

?>