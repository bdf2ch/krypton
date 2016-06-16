<?php

    class User {
        //public $id;
        //public $name;
        //public $surname;
        //public $fname;
        //public $position;
        //public $email;
        //public $phone;
        //public $mobile;
        //public $isAdmin;

        private static $properties = array(
            "id" => 0,
            "name" => "",
            "surname" => "",
            "fname" => "",
            "position" => "",
            "email" => "",
            "phone" => "",
            "mobile" => "",
            "isAdmin" => false
        );



        public function __construct ($parameters) {
            if ($parameters == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - массив параметров инициализации при создании экземпляра класса");
            else {
                if (gettype($parameters) != "array")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                else {
                    /*
                    foreach (self::$properties as $key => $property) {

                        foreach ($parameters as $key2 => $parameter) {
                            if ($key == $key2) {
                                echo("constructor property = ".$key."</br>");
                                if (gettype($parameters[$key2]) != gettype(self::$properties[$key]))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - ".$key);
                                else {
                                    self::$properties[$key] = $parameters[$key2];
                                    $this -> {$key2} = $parameter;
                                }
                            }
                        }
                    }
                    */


                    foreach (self::$properties as $key => $property) {
                        if (array_key_exists($key, $parameters)) {
                            if (gettype($parameters[$key]) != gettype(self::$properties[$key]))
                                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - ".$key);
                            else {
                                self::$properties[$key] = $parameters[$key];
                                $this -> $key = $parameters[$key];
                            }
                        } else
                            $this -> $key = $property;
                    }


                    /*** Идентификатиор пользователя ***/
                    /*
                    if ($parameters["id"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - идентификатор пользователя");
                    else {
                        if (gettype($parameters["id"]) != "integer")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - идентификатор пользователя");
                        else
                            $this -> id = $parameters["id"];
                    }
                    */

                    /***  Фамилия пользователя ***/
                    /*
                    if ($parameters["surname"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - фамилия пользователя");
                    else {
                        if (gettype($parameters["surname"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - фамилия пользователя");
                        else
                            $this -> surname = $parameters["surname"];
                    }
                    */

                    /*** Имя пользователя ***/
                    /*
                    if ($parameters["name"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - имя пользователя");
                    else {
                        if (gettype($parameters["name"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - имя пользователя");
                        else
                            $this -> name = $parameters["name"];
                    }
                    */

                    /*** Отчество пользователя ***/
                    /*
                    if ($parameters["fname"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - отчество пользователя");
                    else {
                        if (gettype($parameters["fname"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - отчество пользователя");
                        else
                            $this -> fname = $parameters["fname"];
                    }*/

                    /*** Должность пользователя ***/
                    /*
                    if ($parameters["position"] != null && gettype($parameters["position"]) != "string") {
                        $this -> position = "";
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - должность пользователя");
                    } else
                        $this -> position = $parameters["position"];
                        */

                    /*** Email пользователя ***/
                    /*
                    if ($parameters["email"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - email пользователя");
                    else {
                        if (gettype($parameters["email"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - email пользователя");
                        else
                            $this -> email = $parameters["email"];
                    }
                    */

                    /*** Телефон пользователя ***/
                    /*
                    if ($parameters["phone"] != null && gettype($parameters["phone"]) != "string") {
                        $this -> phone = "";
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - телефон пользователя");
                    } else
                        $this -> phone = $parameters["phone"];
                    */

                    /*** Мобильный телефон пользователя ***/
                    /*
                    if ($parameters["mobile"] != null && gettype($parameters["mobile"]) != "string") {
                        $this -> mobile = "";
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - мобильный телефон пользователя");
                    } else
                        $this -> mobile = $parameters["mobile"];
                     */

                    /*** Является ли пользователь администратором ***/
                    /*
                    if ($parameters["isAdmin"] != null && gettype($parameters["isAdmin"]) != "boolean") {
                        $this -> isAdmin = false;
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - является ли пользователь администратором");
                    } else
                        $this -> isAdmin = $parameters["isAdmin"];
                    */

                    /*** Идентификатор филиала предприятия ***/
                    /*
                    if ($parameters["departmentId"] != null && gettype($parameters["departmentId"]) != "integer") {
                        $this -> departmentId = "dsfsd";
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - идентификатор филиала предприятия");
                    } else if ($parameters)
                        $this -> departmentId = $parameters["departmentId"];
                        */

                    /*** Идентификатор отдела ***/
                    /*
                    if ($parameters["divisionId"] != null && gettype($parameters["divisionId"]) != "integer") {
                        $this -> divisionId = 0;
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - идентификатор отдела");
                    } else
                        $this -> divisionId = $parameters["divisionId"];
                    */
                }
            }
        }



        public static function addProperty ($title, $value) {
            if ($title == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> addProperty: Не задан параметр - наименование свойства");
            else {
                if (gettype($title) != "string")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> addProperty: Неверно задан тип параметра - наименование свойства");
                else {
                    if ($value == null && gettype($value) != "boolean" && $value != 0)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> addProperty: Не задан параметр - значение свойства");
                    else {
                        //array_push(self::$properties, $title);
                        self::$properties[$title] = $value;
                        //self::${$title} = $value;
                        var_dump(self::$properties);
                        return true;
                    }
                }
            }
        }



        public function __set ($title, $value) {
            foreach (self::$properties as $key => $property) {
                if ($key == $title) {
                    if ($value != null)
                        self::$properties[$key] = $value;
                    else
                        self::$properties[$key] = self::$properties[$key];
                }
            }
            $this -> $title  = $value;
            //var_dump($this);
        }


        /**
        public function __get ($title) {
            foreach (self::$properties as $key => $property) {
                if ($key == $title) {
                    return self::$properties[$key];
                }
            }
        }
        **/



    };

?>