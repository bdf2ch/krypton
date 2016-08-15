"use strict";


(function () {
    angular
        .module("krypton.admin", ["ngRoute", "ngCookies", "ngSanitize", "angularFileUpload", "krypton", "krypton.ui", "krypton.app.kolenergo"])
        .filter("userSearch", userSearchFilter)
        .config(function ($routeProvider,$sceProvider) {
            $sceProvider.enabled(false);

            $routeProvider
                .when("/users/new",
                    {
                        templateUrl: "../templates/admin/users/new-user.html",
                        controller: newUserController
                    }
                )
                .when("/users/:userId",
                    {
                        templateUrl: "../templates/admin/users/edit-user.html",
                        controller: AdminEditUserController
                    }
                );
        })
        .run(kryptonAdminRun);



    function dashboardController ($log, $scope) {

    };




    

    function usersController ($log, $scope, $http, $factory, $users, $modals, $kolenergo, $location) {
        $scope.users = $users;
        $scope.modals = $modals;
        $scope.kolenergo = $kolenergo;
        $scope.newUserGroup = $factory({ classes: ["UserGroup", "Model", "Backup", "States"], base_class: "UserGroup" });
        //$scope.searchResult = [];
        
        $scope.$watch("users.users.searchKeyWord", function (newVal, oldVal) {
            $log.log("search = ", newVal);
            if (newVal.length > 2) {
                $users.users.search(function (result) {
                    //$scope.searchResult = result;
                    var length = result.phones.length;
                    for (var i = 0; i < length; i++) {
                        var phone = $factory({ classes: ["Phone", "Model", "States", "Backup"], base_class: "Phone" });
                        phone._model_.fromAnother(result.phones[i]);
                        phone._backup_.setup();
                        $kolenergo.phones.add(phone);
                    }
                });
            } else if (newVal.length < 3 && oldVal.length > 2) {
                $users.users.pages.set(1);
            }
        }, true);


        $scope.gotoAddUser = function () {
            $location.url("/users/new");
        };
        
        $scope.gotoEditUser = function () {
            $location.url("/users/" + $users.users.getCurrent().id.value);
        };

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
            var params = {
                title: $scope.newUserGroup.title.value
            };
            $http.post("/serverside/libs/krypton/api.php", { action: "addUserGroup", data: params })
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



        $scope.openEditUserGroupModal = function () {
            $modals.open("edit-user-group");
        };



        $scope.closeEditUserGroupModal = function () {
            $users.groups.getCurrent()._backup_.restore();
            $users.groups.getCurrent()._states_.changed(false);
        };



        $scope.editUserGroup = function () {
            var params = {
                id: $users.groups.getCurrent().id.value,
                title: $users.groups.getCurrent().title.value
            };
            $http.post("/serverside/libs/krypton/api.php", { action: "editUserGroup", data: params })
                .success(function (data) {
                    if (data !== undefined) {
                        var group = $factory({ classes: ["UserGroup", "Model", "Backup", "States"], base_class: "UserGroup" });
                        group._model_.fromAnother(data);
                        $users.groups.getCurrent()._model_.fromAnother(group);
                        $users.groups.getCurrent()._backup_.setup();
                        $modals.close();
                    }
                });
        };



        /**
         * Открывает модальное окно удаления группы пользователей
         */
        $scope.openDeleteUserGroupModal = function () {
            $modals.open("delete-user-group-modal");
        };



        /**
         * Удаляет группу пользователей
         */
        $scope.deleteUserGroup = function () {
            var params = {
                id: $users.groups.getCurrent().id.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "deleteUserGroup", data : params })
                .success(function (data) {
                    $log.log(data);
                    if (data !== undefined && data !== null) {
                        if (JSON.parse(data) === true) {
                            $users.groups.delete(params.id);
                            $modals.close("delete-user-group-modal");
                        }
                    }
                });
        };
    };




    function newUserController ($scope, $log, $users) {

    };
    
    
    function AdminEditUserController ($scope, $log, $routeParams, $factory, $users, $navigation, $kolenergo) {
        $scope.users = $users;
        $scope.kolenergo = $kolenergo;
        $scope.divisions = [];
        //$scope.search = "";


        if ($routeParams.userId !== undefined) {
            $log.log("params = ", $routeParams);
            if ($users.users.getCurrent() === undefined || $users.users.getCurrent().id.value !== parseInt($routeParams.userId)) {
                $users.users.select(parseInt($routeParams.userId));
                $scope.divisions = $kolenergo.divisions.getByOrganizationId($users.users.getCurrent().organizationId.value);
                $log.log("divisions = ", $scope.divisions);
                $scope.uploaderData = {
                    action: "uploadUserPhoto",
                    userId: $users.users.getCurrent().id.value
                };
                $log.log("uploader data = ", $scope.uploaderData);
            }

            $navigation.getCurrent().title = $users.users.getCurrent().name.value + " " + $users.users.getCurrent().surname.value;
            $log.log("current user = ", $users.users.getCurrent());


            var phones = $kolenergo.phones.getByUserId($users.users.getCurrent().id.value);
            $users.users.getCurrent().phones = phones;
        }
        
        $scope.editUser = function () {
            //var length = $users.users.getCurrent().phones.length;
           // var phone = "";
           // for (var i = 0; i < length; i++) {
           //     phone += $users.users.getCurrent().phones[i];
           //     phone += i < length - 1 ? "," : "";
           // }
           // $log.log("phone = ", phone);
            $users.users.edit()
        };

        $scope.onCompleteUploadPhoto = function (data) {
            $log.log("photo upload complete");
            $log.log(data);
            //$http.post("serverside/libs/krypton/uploader.php", $scope.uploaderData)
            //    .success(function (data) {
            //        if (data !== undefined) {
            //            $log.log(data);
            //        }
            //    });
            if (data.result !== false) {
                var file = $factory({ classes: ["File", "Model", "States"], base_class: "File" });
                file._model_.fromAnother(data.result);
                $users.users.getCurrent().photo.value = file.url.value;
            }

        };
        
        $scope.addPhone = function () {
            var phone = $factory({ classes: ["Phone", "Model", "Backup", "States"], base_class: "Phone" });
            $users.users.getCurrent().phones.push(phone);
        };
    };




    function settingsController ($log, $scope, $settings) {
        $scope.settings = $settings;
    };

    
    
    function filesController ($scope, $log, $factory) {
        
    };


    function AdminPermissionRulesController ($scope, $log, $permissions, $factory, $modals) {
        $scope.permissions = $permissions;
        $scope.newPermissionRule = $factory({ classes: ["PermissionRule", "Model", "Backup", "States"], base_class: "PermissionRule" });

        $scope.openAddPermissionRuleModal = function () {
            $scope.newPermissionRule._backup_.setup();
            $modals.open("add-permission-rule");
        };

        $scope.closeAddPermissionRuleModal = function () {
            $scope.newPermissionRule._backup_.restore();
        };

        $scope.addPermissionRule = function () {
            $permissions.rules.add($scope.newPermissionRule, function () {
                $scope.newPermissionRule._backup_.restore();
                $modals.close();
            });
        };

        $scope.openEditPermissionRuleModal = function () {
            $modals.open("edit-permission-rule");
        };

        $scope.closeEditPermissionRuleModal = function () {
            $permissions.rules.getCurrent()._backup_.restore();
        };
        
        $scope.editPermissionRule = function () {
            $permissions.rules.edit(function () {
                $modals.close();
            });
        };

    };


    function AdminATSController ($log, $scope, $users, $kolenergo, $modals) {
        $scope.user = $users;
        $scope.kolenergo = $kolenergo;
        $scope.modals = $modals;
        $scope.submitted = false;
        $scope.newATSCodeOrganizationId = 0;

        $scope.openNewATSModal = function () {
            $modals.open("new-ats-modal");
        };

        $scope.closeNewATSModal = function () {
            $scope.new_ats.$setValidity();
            $scope.new_ats.$setPristine();
            $scope.submitted = false;
        };

        $scope.addATS = function () {
            $scope.submitted = true;
            if ($scope.new_ats.$valid) {
                $kolenergo.ats.add(function () {
                    $modals.close();
                });
            } 
        };

        $scope.openEditATSModal = function () {
            $modals.open("edit-ats-modal");
        };

        $scope.closeEditATSModal = function () {
            $scope.edit_ats.$setValidity();
            $scope.edit_ats.$setPristine();
            $scope.submitted = false;
            $kolenergo.ats.getCurrent()._backup_.restore();
            $kolenergo.ats.getCurrent()._states_.changed(false);
        };

        $scope.editATS = function () {
            $scope.submitted = true;
            if ($scope.edit_ats.$valid) {
                $kolenergo.ats.edit(function () {
                    $modals.close();
                });
            }
        };

        $scope.openNewATSCodeModal = function () {
            $modals.open("new-ats-code-modal");
        };

        $scope.closeNewATSCodeModal = function () {
            $scope.new_ats_code.$setValidity();
            $scope.new_ats_code.$setPristine();
            $scope.submitted = false;
        };

        $scope.addATSCode = function () {
            $log.log($scope.new_ats_code);
            $scope.submitted = true;
            if ($scope.new_ats_code.$valid) {
                $kolenergo.codes.add(function () {
                    $modals.close();
                });
            }
        };
    };



    function AdminPhonesController ($log, $scope, $kolenergo, $users, $modals, $factory) {
        $scope.kolenergo = $kolenergo;
        $scope.users = $users;
        $scope.search = "";
        $scope.submitted = false;

        $users.users.clear();


        $scope.$watch("users.users.searchKeyWord", function (newVal, oldVal) {
            $log.log("search = ", newVal);
            if (newVal.length > 2) {
                $users.users.search(function (result) {
                    //$scope.searchResult = result;
                    $kolenergo.phones.clear();
                    var length = result.phones.length;
                    for (var i = 0; i < length; i++) {
                        var phone = $factory({ classes: ["Phone", "Model", "States", "Backup"], base_class: "Phone" });
                        phone._model_.fromAnother(result.phones[i]);
                        phone._backup_.setup();
                        $kolenergo.phones.append(phone);
                    }
                    $log.log("phones = ", $kolenergo.phones.getAll());
                });
            } else if (newVal.length < 3 && oldVal.length > 2) {
                $users.users.pages.set(1);
            }     
        });


        $scope.selectUser = function (id) {
            $users.users.select(id, function () {
                $kolenergo.phones.getNew().userId.value = id;
            });
        };


        $scope.openNewPhoneModal = function () {
            $modals.open("new-phone-modal");
        };


        $scope.closeNewPhoneModal = function () {
            $scope.submitted = false;
            $scope.new_phone.$setPristine();
            $scope.new_phone.$setValidity();
            $kolenergo.phones.getNew()._backup_.restore();
        };
        
        
        $scope.addPhone = function () {
            $scope.submitted = true;
            if ($scope.new_phone.$valid) {
                $kolenergo.phones.add(function () {
                    $modals.close();
                });
            }
        };


        $scope.openEditPhoneModal = function () {
            $modals.open("edit-phone-modal");
        };


        $scope.closeEditPhoneModal = function () {
            $scope.edit_phone.$setValidity();
            $scope.edit_phone.$setPristine();
            $scope.submitted = false;
            $kolenergo.phones.getCurrent()._backup_.restore();
            $kolenergo.phones.getCurrent()._states_.changed(false);
        };


        $scope.editPhone = function () {
            $scope.submitted = true;
            if ($scope.edit_phone.$valid) {
                $kolenergo.phones.edit(function () {
                    $modals.close();
                });
            }
        };


        $scope.openDeletePhoneModal = function () {
            $modals.open("delete-phone-modal");
        };


        $scope.deletePhone = function () {
            $kolenergo.phones.delete(function () {
                $modals.close();
            });
        };

    };




    function kryptonAdminRun ($log, $location, $navigation, $factory, $rootScope, $users, $permissions, $http, $settings, $session) {
        $log.log(window.krypton);
        $log.log("krypton admin run");
        $log.log(window.krypton);
        //$location.url("/users");
        $rootScope.navigation = $navigation;
        $rootScope.session = $session;

        $session.init();
        $settings.init();
        $users.init();
        $permissions.init();
        
        
        var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp.init({
            id: "users",
            parentId: "",
            url: "/users",
            templateUrl: "../../templates/admin/users/users.html",
            controller: usersController,
            title: "Пользователи",
            description: "Управление пользователями",
            icon: "fa-user"
        });
        $navigation.add(temp);

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    id: "edit-user",
                    parentId: "users",
                    url: "/users/:userId",
                    templateUrl: "../../templates/admin/users/edit-user.html",
                    controller: AdminEditUserController,
                    //resolve: {
                    //    "kolenergo": function ($kolenergo) {

                        //}
                    //},
                    title: "Редактирование пользователя",
                    description : "Редактирование данных пользователя"
                }));

        var temp2 = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
        temp2.init({
            id: "dashboard",
            parentId: "",
            url: "/dashboard",
            templateUrl: "../../templates/admin/dashboard/dashboard.html",
            controller: dashboardController,
            title: "Дашборд",
            description: "Панель управления",
            icon: "fa-home",
            isDefault: true
        });
        $navigation.add(temp2);

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    id: "settings",
                    parentId: "",
                    url: "/settings",
                    templateUrl: "../../templates/admin/settings/settings.html",
                    controller: settingsController,
                    title: "Настройки",
                    description : "Управление настройками системы и модулей",
                    icon: "fa-cog"
                }));

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    id: "permissions",
                    parentId: "",
                    url: "/permissions",
                    templateUrl: "../../templates/admin/permissions/permissions.html",
                    controller: AdminPermissionRulesController,
                    title: "Контроль доступа",
                    description : "Управление правилами доступа к данным",
                    icon: "fa-shield"
                }));

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    id: "ats",
                    parentId: "",
                    url: "/ats",
                    templateUrl: "../../templates/admin/kolenergo/ats.html",
                    controller: AdminATSController,
                    title: "Справочник АТС",
                    description : "Управление АТС и кодами связи между АТС",
                    icon: "fa-phone-square"
                }));

        $navigation.add(
            $factory({ classes: ["Menu", "Model"], base_class: "Menu" })
                .init({
                    id: "phones",
                    parentId: "",
                    url: "/phones",
                    templateUrl: "../../templates/admin/kolenergo/phones.html",
                    controller: AdminPhonesController,
                    title: "Тел. справочник",
                    description : "Управление АТС и кодами связи между АТС",
                    icon: "fa-phone"
                }));


        //$http.post("../../serverside/libs/krypton/api.php", { action: "test", data: {} })
        //    .success(function (data) {
        //        $log.log(data);
        //    });


    };


    function userSearchFilter () {
        return function (input, search) {
            if (search !== undefined && search !== "") {
                var length = input.length;
                var result = [];
                for (var i = 0; i < length; i++) {
                    if (input[i].search.toLowerCase().indexOf(search.toLowerCase()) !== -1)
                        result.push(input[i]);
                }
                return result;
            } else
                return input;
        }
    };
})();