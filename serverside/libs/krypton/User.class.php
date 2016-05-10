<?php

    class User {
        public $id = 0;
        public $name;
        public $surname;
        public $fname;
        public $position;
        public $email;
        public $phone;
        public $isAdmin = false;

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
    };

?>