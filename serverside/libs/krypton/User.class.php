<?php

    class User {
        private $name;
        private $surname;
        private $fname;
        private $position;
        private $email;
        private $phone;

        public function ___construct ($surname, $name, $fname, $position, $email, $phone) {
            if ($surname== null) {
                Errors::push();
                return false;
            } else {
                if (gettype($surname) != "string") {
                    Errors::push();
                    return false;
                } else {
                    if ($name == null) {
                        Errors::push();
                        return false;
                    } else {
                        if (gettype($name) != "string") {
                            Errors::push();
                            return false;
                        } else {
                            if ($fname != null && gettype($fname) != "string") {
                                Errors::push();
                                return false;
                            } else {
                                if ($position != null && gettype($position) != "string'") {
                                    Errors::push();
                                    return false;
                                } else {
                                    if ($email == null) {
                                        Errors::push();
                                        return false;
                                    } else {
                                        if (gettype($email) != "string") {
                                            Errors::push();
                                            return false
                                        } else {
                                            if ($phone == null) {
                                                Errors::push();
                                                return false;
                                            } else {
                                                if (gettype($phone) != "string") {
                                                    Errors::push();
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
        }
    };

?>