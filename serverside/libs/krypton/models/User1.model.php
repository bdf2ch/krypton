<?php

    class User1 extends Model {
        public static $fields = array();

        public function __construct ($parameters) {
            self::field("id", new Field(array( "source" => "ID", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("userGroupId", new Field(array( "source" => "USER_GROUP_ID", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("surname", new Field(array( "source" => "SURNAME", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("name", new Field(array( "source" => "NAME", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("fname", new Field(array( "source" => "FNAME", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("position", new Field(array( "source" => "POSITION", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("email", new Field(array( "source" => "EMAIL", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("phone", new Field(array( "source" => "PHONE", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("mobile", new Field(array( "source" => "MOBILE_PHONE", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("photo", new Field(array( "source" => "PHOTO_URL", "type" => Krypton::DATA_TYPE_STRING, "value" => "", "defaultValue" => "" )));
            self::field("isAdmin", new Field(array( "source" => "IS_ADMIN", "type" => Krypton::DATA_TYPE_BOOLEAN, "value" => false, "defaultValue" => false )));

            self::field("ldapEnabled", new Field(array( "source" => "LDAP_ENABLED", "type" => Krypton::DATA_TYPE_BOOLEAN, "value" => true, "defaultValue" => true )));
            self::field("organizationId", new Field(array( "source" => "ORGANIZATION_ID", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("divisionId", new Field(array( "source" => "DIVISION_ID", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("departmentId", new Field(array( "source" => "DEPARTMENT_ID", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));
            self::field("atsId", new Field(array( "source" => "ATS_ID", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 0, "defaultValue" => 0 )));

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