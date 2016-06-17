<?php

    class Test extends Model {

        public function __construct () {
            self::extend("id", new Field(array( "source" => "id", "type" => Krypton::DATA_TYPE_INTEGER, "value" => 1, "defaultValue" => 0 )));
            self::extend("title", new Field(array( "source" => "title", "type" => Krypton::DATA_TYPE_STRING, "value" => "test string value", "defaultValue" => "")));
        }

    };

?>