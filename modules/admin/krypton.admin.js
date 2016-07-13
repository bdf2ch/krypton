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





    function usersController ($log, $scope, $http, $factory, $users, $modals, $kolenergo) {
        $scope.users = $users;
        $scope.modals = $modals;
        $scope.kolenergo = $kolenergo;

        $scope.newUserGroup = $factory({ classes: ["UserGroup", "Model", "Backup", "States"], base_class: "UserGroup" });


        $scope.openNewUserGroupModal = function () {
            $modals.open("new-user-group");
            //$scope.newUserGroup._backup_.restore();
            $scope.newUserGroup._backup_.setup();

        };

        $scope.closeNewUserGroupModal = function () {
            $log.log("close");
            $scope.newUserGroup._backup_.restore();
        };
        
        $scope.addUserGroup = function () {
            var data = {
                title: $scope.newUserGroup.title.value
            };
            $http.post("/serverside/libs/krypton/api.php", { action: "addUserGroup", data: data })
                .success(function (data) {
                    if (data !== undefined) {
                        var group = $factory({ classes: ["UserGroup", "Model", "Backup", "States"], base_class: "UserGroup" });
                        group._model_.fromAnother(data);
                        group._backup_.setup();
                        $users.addGroup(group);
                        $modals.close();
                    }
                });
        };
    };




    function settingsController ($log, $scope, $settings) {
        $scope.settings = $settings;
    };



    function kryptonAdminRun ($log, $location, $navigation, $factory, $rootScope, $users, $http, $settings, $session) {
        $log.log("krypton admin run");
        //$location.url("/users");
        $rootScope.navigation = $navigation;
        $rootScope.session = $session;

        $session.init();
        $settings.init();
        $users.init();
        
        
        var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp.init({
            url: "/users",
            templateUrl: "../../templates/admin/users/users.html",
            controller: usersController,
            title: "Пользователи",
            description: "Управление пользователями",
            icon: "fa-user"
        });
        $navigation.add(temp);

        var temp2 = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp2.init({
            url: "/dashboard",
            templateUrl: "../../templates/admin/dashboard/dashboard.html",
            controller: dashboardController(),
            title: "Дашборд",
            description: "Панель управления",
            icon: "fa-home",
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
                    description : "Управление настройками системы и модулей",
                    icon: "fa-cog"
                }));


        $http.post("../../serverside/libs/krypton/api.php", { action: "test", data: {} })
            .success(function (data) {
                $log.log(data);
            });

        $log.log(window.krypton);
    };
})();