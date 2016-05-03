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
                Errors::push(1116);
                return false;
            } else {
                if (gettype($id) != "integer") {
                    Errors::push(1117);
                    return false;
                } else {
                    if ($surname == null) {
                        Errors::push(1106);
                        return false;
                    } else {
                        if (gettype($surname) != "string") {
                            Errors::push(1107);
                            return false;
                        } else {
                            if ($name == null) {
                                Errors::push(1108);
                                return false;
                            } else {
                                if (gettype($name) != "string") {
                                    Errors::push(1109);
                                    return false;
                                } else {
                                    if ($fname != null && gettype($fname) != "string") {
                                        Errors::push(1110);
                                        return false;
                                    } else {
                                        if ($position != null && gettype($position) != "string") {
                                            Errors::push(1111);
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
                                                    if ($phone != null && gettype($phone) != "string") {
                                                        Errors::push(1114);
                                                        return false;
                                                    } else {
                                                        if ($isAdmin != null && gettype($isAdmin) != "boolean") {
                                                            Errors::push();
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
                    }
                }
            }

        }
    };

?>