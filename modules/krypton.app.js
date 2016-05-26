"use strict";


(function () {

    angular
        .module("krypton.app", [
            "ngRoute",
            "ngCookies",
            "krypton",
            "krypton.ui"]
        )
        .factory("$application", applicationFactory)
        .controller("testController", testController)
        .run(kryptonAppRun);



    function kryptonAppRun ($log, $classes, $http, $errors, $dateTimePicker) {
        $log.log("krypton.app run...");
        moment.locale("ru");

        if (krypton !== undefined) {
            $log.info(krypton);
            //$session.setCurrentSession(krypton.session);
            //$session.setCurrentUser(krypton.user);
        }
        
        //$log.log("session: ", $session.getCurrentSession());
        //$log.log("user: ", $session.getCurrentUser());
        $log.log($errors.add(50, "test error"));

        $http.post("serverside/libs/krypton/controllers/appController.php", {action: "test", parameters: {first: 10, second: "secpar"}})
            .success(function (data) {
                $log.log(data);
            });

        $dateTimePicker.open("testInput2");



    };



    function applicationFactory () {

    };




    function testController ($scope, $dateTimePicker) {
        $scope.test = 1464249736;

        /*
        $dateTimePicker.add({
            element: "testInput",
            modelValue: $scope.test,
            isModal: true,
            title: "Выберите дату"

        }).open();
        */


        //$dateTimePicker.open("testInput");

        //$dateTimePicker.open("testInput2");

        //});

        //$dateTimePicker.open("testInput2");

        //$dateTimePicker.open("testInput");
        $dateTimePicker.show("testInput");

    };

})();
