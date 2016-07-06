"use strict";


(function () {
    angular
        .module("krypton.admin", ["ngRoute", "ngCookies", "ngSanitize", "krypton", "krypton.ui", "krypton.app.kolenergo"])
        .config(function ($routeProvider,$sceProvider) {
            $sceProvider.enabled(false);
        })
        .run(kryptonAdminRun);



    function dashboardController ($log, $scope) {

    };



    function usersController ($log, $scope, $users, $kolenergo) {
        $scope.users = $users;
        $scope.kolenergo = $kolenergo;
    };


    function settingsController ($log, $scope, $settings) {
        $scope.settings = $settings;
    };



    function kryptonAdminRun ($log, $location, $navigation, $factory, $rootScope, $users, $http, $settings) {
        $log.log("krypton admin run");
        //$location.url("/users");
        $rootScope.navigation = $navigation;

        $settings.init();
        $users.init();
        
        
        var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp.init({
            url: "/users",
            templateUrl: "../../templates/admin/users/users.html",
            controller: usersController,
            title: "Пользователи",
            description: "Управление пользователями"
        });
        $navigation.add(temp);

        var temp2 = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp2.init({
            url: "/dashboard",
            templateUrl: "../../templates/admin/dashboard/dashboard.html",
            controller: dashboardController(),
            title: "Дашборд",
            description: "Панель управления",
            isDefault: true
        });
        $navigation.add(temp2);

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    url: "/settings",
                    templateUrl: "../../templates/admin/settings/settings.html",
                    controller: settingsController,
                    title: "Настройки",
                    description : "Управление настройками системы и модулей"
                }));


        $http.post("../../serverside/libs/krypton/api.php", { action: "test", data: {} })
            .success(function (data) {
                $log.log(data);
            });

        $log.log(window.krypton);
    };
})();