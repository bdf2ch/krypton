<?php
    require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Controller.class.php";
    //require_once $_SERVER["DOCUMENT_ROOT"]."/serverside/libs/krypton/Users.cla";



    class UsersController extends Controller {

        public function __construct () {
            parent::__construct(__CLASS__);
        }

        public function  ($first, $second) {
            echo("test called with first = ".$first." & second = ".$second);
        }

    };

    $controller = new AppController();

?>