<?php

        class Model {
            public static $fields = array();

            public static function toJSON () {
                $obj = new stdObj();
                foreach (self::$field as $key => $field) {
                    if (get_class($field) == "Field") {
                        $obj -> $key = $field -> value;
                    }
                }
                return json_encode($obj);
            }

        };

?>