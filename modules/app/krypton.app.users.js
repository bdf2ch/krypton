"use strict";


(function () {
    angular
        .module("krypton.app.users", [])
        .factory("$users", usersFactory)
        .run(usersRun)
        .controller("UsersController", UsersController)
        .controller("UserAccountController", UserAccountController);
    

    function usersFactory ($log, $factory) {
        var items = [];

        return {
            init: function () {
                if (window.krypton !== null && window.krypton !== undefined) {
                    if (window.krypton.users !== null && window.krypton.users !== undefined) {
                        var length = window.krypton.users.length;
                        for (var i = 0; i < length; i++) {
                            var user = $factory({ classes: ["User", "Model", "States", "Backup"], base_class: "User" });
                            user._model_.fromAnother(window.krypton.users[i]);
                            user._backup_.setup();
                            items.push(user);
                        }
                        $log.log("users = ", items);
                    }
                }
            },


            getAll: function () {
                return items;
            }
        }
    };


    function usersRun ($users) {
        $users.init();
    };

    
    function UsersController ($log, $scope) {
        
    };


    function UserAccountController ($log, $scope, $session) {
        $scope.user = $session.getCurrentUser();
        var position = false;
        var positionEdited = false;
        var editingPhones = false;


        $scope.phone = {
            editing: false,
            adding: false,
            edit: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.editing = flag;
                else
                    return this.editing;
            },
            add: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.adding = flag;
                else
                    return this.adding;
            }
        };

        
        $scope.mobile = {
            editing: false,
            adding: false,
            edit: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.editing = flag;
                else    
                    return this.editing;
            },
            add: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.adding = flag;
                else
                    return this.adding;
            }
        };
        
        
        $scope.position = function (flag) {
            if (flag !== undefined && typeof (flag) === "boolean") {
                position = flag;
            } else
                return position;
        };

        $scope.editPhonesMode = function (flag) {
            if (flag !== undefined && typeof flag === "boolean") {
                $scope.inPhonesEditMode = true;
            }
        };




        $scope.onChange = function () {
            $log.log("onChange from scope, ", $scope.user.position.value);
        };
        
        $scope.cancel = function () {
            $log.log("cancel called");
            $scope.user._states_.editing(false);
        };
    };
    
})();