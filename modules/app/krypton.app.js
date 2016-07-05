"use strict";


(function () {

    angular
        .module("krypton.app", [
            "ngRoute",
            "ngCookies",
            "ngSanitize",
            "krypton",
            "krypton.ui",
            "krypton.app.kolenergo"
            //"krypton.app.news",
            //"krypton.app.telephones"
             ]
        )
        .controller("testController", testController)
        .controller("adminLoginController", adminLoginController)
        .config(function ($routeProvider) {
            $routeProvider
                .when("/admin", {
                    //template: "",
                    controller: "adminLoginController"
                })
                .when("/", {
                    templateUrl: "templates/app/account.html",
                    controller: "UserAccountController"
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




    function getAdminTemplate ($http) {
        $http.post("serverside/libs/krypton/api.php", { action: "getAdminTemplate" })
            .success(function (data) {
                $log.log(data);
                if ($errors.isError(data))
                    $log.error(data);
            });
    };

    function adminLoginController ($log, $scope) {
        $scope.test = "test scope";
    };



    function kryptonAppRun ($log, $classes, $session, $settings, $navigation, $application, $http, $errors, $users, $rootScope) {
        $log.log("krypton.app run...");
        moment.locale("ru");

        $rootScope.errors = $errors;
        $rootScope.application = $application;

        $classes.getAll().User.departmentId = new Field({ source: "department_id", type: "integer", value: 0, default_value: 0, backupable: true });
        $classes.getAll().User.divisionId = new Field({ source: "division_id", type: "integer", value: 0, default_value: 0, backupable: true });

        $application.init();
        $navigation.init();
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
