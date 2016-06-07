"use strict";


(function () {

    angular
        .module("krypton.app", [
            "ngRoute",
            "ngCookies",
            "krypton",
            "krypton.ui",
            "krypton.app.news",
            "krypton.app.telephones"]
        )
        .factory("$application", applicationFactory)
        .controller("testController", testController)
        .config(function ($routeProvider) {
            $routeProvider
                .when("/phones", {
                    templateUrl: "templates/app/telephones.html",
                    controller: "TelephonesController"
                })
                .otherwise({
                    templateUrl: "templates/app/news.html",
                    controller: "NewsController"
                });
        })
        .run(kryptonAppRun);





    function kryptonAppRun ($log, $classes, $http, $errors, $dateTimePicker) {
        $log.log("krypton.app run...");
        moment.locale("ru");



        if (krypton !== undefined)
            $log.info(krypton);


        //$http.post("serverside/libs/krypton/controllers/appController.php", {action: "test", parameters: {first: 10, second: "secpar"}})
        //    .success(function (data) {
        //        $log.log(data);
        //    });



    };



    function applicationFactory () {

    };




    function testController ($scope, $dateTimePicker, $session) {
        $scope.test = 1464249736;
        $scope.session = $session;

        $scope.test2 = 1463086800;
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
