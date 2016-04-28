<?php

    class User {
        public $name;
        public $surname;
        public $fname;
        public $position;
        public $email;
        public $phone;

        public function __construct ($surname, $name, $fname, $position, $email, $phone) {
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
                                                $this -> surname = $surname;
                                                $this -> name = $name;
                                                $this -> fname = $fname;
                                                $this -> position = $position;
                                                $this -> phone = $phone;
                                                $this -> email = $email;
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