<?php

    class UserSession {
        public $token;
        public $start;
        public $end;

        public function __construct ($token, $start, $end) {
            if ($token == null) {
                Errors::push(1100);
                return false;
            } else {
                if (gettype($token) != "string") {
                    Errors::push(1101);
                    return false;
                } else {
                    if ($start == null) {
                        Errors::push(1102);
                        return false;
                    } else {
                        if (gettype(intval($start)) != "integer") {
                            Errors::push(1103);
                            return false;
                        } else {
                            if ($end == null) {
                                Errors::push(1104);
                                return false;
                            } else {
                                if (gettype(intval($end)) != "integer") {
                                    Errors::push(1105);
                                    return false;
                                } else {
                                    $this -> token = $token;
                                    $this -> start = intval($start);
                                    $this -> end = intval($end);
                                }
                            }
                        }
                    }
                }
            }
        }

    };

?>