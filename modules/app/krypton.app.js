"use strict";


(function () {

    angular
        .module("krypton.app", [
            "ngRoute",
            "ngCookies",
            "krypton",
            "krypton.ui",
            "krypton.app.kolenergo",
            "krypton.app.news",
            "krypton.app.telephones"]
        )
        .controller("testController", testController)
        .config(function ($routeProvider) {
            $routeProvider
                .when("/", {
                    templateUrl: "templates/app/news.html",
                    controller: "NewsController"
                })
                .when("/phones", {
                    templateUrl: "templates/app/telephones.html",
                    controller: "TelephonesController"
                })
                .when("/account", {
                    templateUrl: "templates/app/account.html",
                    controller: "UserAccountController"
                })
                .otherwise({
                    redirectTo: "/"
                });
        })
        .run(kryptonAppRun);





    function kryptonAppRun ($log, $classes, $session, $settings, $application, $http, $errors, $users) {
        $log.log("krypton.app run...");
        moment.locale("ru");

        $classes.getAll().User.departmentId = new Field({ source: "department_id", type: "integer", value: 0, default_value: 0, backupable: true });
        $classes.getAll().User.divisionId = new Field({ source: "division_id", type: "integer", value: 0, default_value: 0, backupable: true });

        $application.init();
        $errors.init();
        $session.init();
        $settings.init();
        $users.init();



        if (krypton !== undefined)
            $log.info(krypton);


        //$http.post("serverside/libs/krypton/controllers/appController.php", {action: "test", parameters: {first: 10, second: "secpar"}})
        //    .success(function (data) {
        //        $log.log(data);
        //    });



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
