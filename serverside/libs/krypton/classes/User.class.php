<?php

    class User {
        public $id;
        public $name;
        public $surname;
        public $fname;
        public $position;
        public $email;
        public $phone;
        public $mobile;
        public $isAdmin;



        public function __construct ($parameters) {
            if ($parameters == null)
                return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - массив параметров инициализации при создании экземпляра класса");
            else {
                echo("type = ".gettype($parameters)."</br>");
                if (gettype($parameters) != "array")
                    return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - массив параметров инициализации");
                else {

                    /*** Идентификатиор пользователя ***/
                    if ($parameters["id"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - идентификатор пользователя");
                    else {
                        if (gettype($parameters["id"]) != "integer")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - идентификатор пользователя");
                        else
                            $this -> id = $parameters["id"];
                    }

                    /***  Фамилия пользователя ***/
                    if ($parameters["surname"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - фамилия пользователя");
                    else {
                        if (gettype($parameters["surname"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - фамилия пользователя");
                        else
                            $this -> surname = $parameters["surname"];
                    }

                    /*** Имя пользователя ***/
                    if ($parameters["name"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - имя пользователя");
                    else {
                        if (gettype($parameters["name"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - имя пользователя");
                        else
                            $this -> name = $parameters["name"];
                    }

                    /*** Отчество пользователя ***/
                    if ($parameters["fname"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - отчество пользователя");
                    else {
                        if (gettype($parameters["fname"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - отчество пользователя");
                        else
                            $this -> fname = $parameters["fname"];
                    }

                    /*** Должность пользователя ***/
                    if ($parameters["position"] != null && gettype($parameters["position"]) != "string") {
                        $this -> position = "";
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - должность пользователя");
                    } else
                        $this -> position = $parameters["position"];

                    /*** Email пользователя ***/
                    if ($parameters["email"] == null)
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - email пользователя");
                    else {
                        if (gettype($parameters["email"]) != "string")
                            return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - email пользователя");
                        else
                            $this -> email = $parameters["email"];
                    }

                    /*** Телефон пользователя ***/
                    if ($parameters["phone"] != null && gettype($parameters["phone"]) != "string") {
                        $this -> phone = "";
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - телефон пользователя");
                    } else
                        $this -> phone = $parameters["phone"];

                    /*** Мобильный телефон пользователя ***/
                    if ($parameters["mobile"] != null && gettype($parameters["mobile"]) != "string") {
                        $this -> mobile = "";
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - мобильный телефон пользователя");
                    } else
                        $this -> mobile = $parameters["mobile"];

                    /*** Является ли пользователь администратором ***/
                    if ($parameters["isAdmin"] != null && gettype($parameters["isAdmin"]) != "boolean") {
                        $this -> isAdmin = false;
                        return Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - является ли пользователь администратором");
                    } else
                        $this -> isAdmin = $parameters["isAdmin"];
                }
            }
        }



        /*
        public function __construct ($id, $surname, $name, $fname, $position, $email, $phone, $isAdmin) {
            if ($id == null) {
                Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - идентификатор пользователя");
                return false;
            } else {
                if (gettype($id) != "integer") {
                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - идентификатор пользователя");
                    return false;
                } else {
                    if ($surname == null) {
                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - фамилия пользователя");
                        return false;
                    } else {
                        if (gettype($surname) != "string") {
                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - фамилия пользователя");
                            return false;
                        } else {
                            if ($name == null) {
                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - имя пользователя");
                                return false;
                            } else {
                                if (gettype($name) != "string") {
                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - имя пользователя");
                                    return false;
                                } else {
                                    if ($fname == null) {
                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - отчество пользователя");
                                        return false;
                                    } else {
                                        if(gettype($fname) != "string") {
                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - отчество пользователя");
                                            return false;
                                        } else {
                                            if ($position == null) {
                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - должность пользователя");
                                                return false;
                                            } else {
                                                if (gettype($position) != "string") {
                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - должность пользователя");
                                                    return false;
                                                } else {
                                                    if ($email == null) {
                                                        Errors::push(1112);
                                                        return false;
                                                    } else {
                                                        if (gettype($email) != "string") {
                                                            Errors::push(1113);
                                                            return false;
                                                        } else {
                                                            if ($phone == null) {
                                                                Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Не задан параметр - телефон пользователя");
                                                                return false;
                                                            } else {
                                                                if (gettype($phone) != "string") {
                                                                    Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - телефон пользователя");
                                                                    return false;
                                                                } else {
                                                                    if ($isAdmin == null && gettype($isAdmin) != "boolean") {
                                                                        Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан параметр - является ли пользователь администратором");
                                                                        return false;
                                                                    } else {
                                                                        if (gettype($isAdmin) != "boolean") {
                                                                            Errors::push(Errors::ERROR_TYPE_DEFAULT, "User -> __construct: Неверно задан тип параметра - является ли пользователь администратором");
                                                                            return false;
                                                                        } else {
                                                                            $this -> id = $id;
                                                                            $this -> surname = $surname;
                                                                            $this -> name = $name;
                                                                            $this -> fname = $fname;
                                                                            $this -> position = $position;
                                                                            $this -> phone = $phone;
                                                                            $this -> email = $email;
                                                                            $this -> isAdmin = $isAdmin;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    //}
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        */
    };

?>