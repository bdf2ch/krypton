"use strict";

(function () {
    angular
        .module("krypton.app.kolenergo", ["krypton"])
        .factory("$kolenergo", kolenergoFactory)
        .controller("UserAccountController", UserAccountController)
        .filter("byDepartmentId", byDepartmentIdFilter)
        .run(kolenergoRun);



    function kolenergoFactory ($log, $classes, $factory, $errors, $navigation) {
        /**
         * Organization
         * Набор свойств и методов, описывающих организацию
         */
        $classes.add("Organization", {
            __dependencies__: [],
            __icon__: "",
            id: new Field ({ source: "id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            title: new Field ({ source: "title", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        /**
         * Department
         * Набор свойств и методов, описывающих производственное отделение
         */
        $classes.add("Department", {
            __dependencies__: [],
            __icon__: "",
            id: new Field({ source: "id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            title: new Field({ source: "title", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        /**
         * Division
         * Набор свойств и методов, описывающих структурное подразделение
         */
        $classes.add("Division", {
            __dependencies__: [],
            id: new Field({ source: "id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            departmentId: new Field({ source: "department_id", type: DATA_TYPE_STRING, value: 0, default_value: 0, backupable: true }),
            parentId: new Field({ source: "parent_id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true }),
            title: new Field({ source: "title", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        var organizations = [];
        var departments = [];
        var divisions = [];

        var currentOrganization = undefined;
        var currentDivision = undefined;

        return {
            init: function () {
                if (window.krypton !== null && window.krypton !== undefined) {
                    if (window.krypton.organizations !== null && window.krypton.organizations !== undefined) {
                        var length = window.krypton.organizations.length;
                        for (var i = 0; i < length; i++) {
                            var organization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
                            organization._model_.fromAnother(window.krypton.organizations[i]);
                            organization._backup_.setup();
                            $log.log(organization._backup_.data);
                            organizations.push(organization);
                        }
                        $log.log("organizations = ", organizations);
                    }

                    if (window.krypton.departments !== null && window.krypton.departments !== undefined) {
                        var length = window.krypton.departments.length;
                        for (var i = 0; i < length; i++) {
                            var department = $factory({ classes: ["Department", "Model", "Backup", "States"], base_class: "Department" });
                            department._model_.fromAnother(window.krypton.departments[i]);
                            department._backup_.setup();
                            $log.log(department._backup_.data);
                            departments.push(department);
                        }
                        $log.log("departments = ", departments);
                    }

                    if (window.krypton.divisions !== null && window.krypton.divisions !== undefined) {
                        var length = window.krypton.divisions.length;
                        for (var i = 0; i < length; i++) {
                            var division = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
                            division._model_.fromAnother(window.krypton.divisions[i]);
                            division._backup_.setup();
                            divisions.push(division);
                        }
                        $log.log("divisions = ", divisions);
                    }
                }

                $classes.getAll().User.departmentId = new Field({ source: "departementId", type: "integer", value: 0, default_value: 0, backupable: true, displayable: true, title: "Произв. отделение" });

                var temp = $factory({ classes: ["Menu", "Model"], base_class: "Menu" });
                temp.init({
                    url: "/company",
                    templateUrl: "../../templates/admin/kolenergo/company.html",
                    controller: companyController,
                    title: "Организации",
                    description: "Управление структурой организаций",
                    icon: "fa-building"
                });
                $navigation.add(temp);
            },
            
            
            
            organizations: {
                /**
                 * Возвращает массив всех организаций
                 * @returns {Array}
                 */
                getAll: function () {
                    return organizations;
                },

                /**
                 * Возвращает текущую организацию
                 * @returns {undefined}
                 */
                getCurrent: function () {
                    return currentOrganization;
                },

                /**
                 * Выбирает организацию по идентификатору
                 * @param organizationId {number} - идентификатор организации
                 * @returns {boolean}
                 */
                select: function (organizationId) {
                    if (organizationId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> selectOrganization: Не задан параметр - идентификатор организации");
                        return false;
                    }

                    var length = organizations.length;
                    for (var i = 0; i < length; i++) {
                        if  (organizations[i].id.value === organizationId) {
                            if (organizations[i]._states_.selected() === false) {
                                organizations[i]._states_.selected(true);
                                currentOrganization = organizations[i];
                            } else {
                                organizations[i]._states_.selected(false);
                                currentOrganization = undefined;
                            }
                        } else
                            organizations[i]._states_.selected(false);
                    }
                    $log.log("curret org = ", currentOrganization);

                    return true;
                },

                /**
                 * Добавляет новую организацию
                 * @param organization {Organization} - объект с информацией о новой организации
                 * @returns {boolean}
                 */
                add: function (organization) {
                    if (organization == undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> addOrganization: Не залан параметр - объект с информацией о новой организации");
                        return false;
                    }

                    organizations.push(organization);
                    return true;
                },

                /**
                 * Удаляет организацию с заданным идентификатором
                 * @param id {number} - идентификатор организации
                 * @returns {boolean}
                 */
                delete: function (organizationId) {
                    if (organizationId === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> deleteOrganization: Не задан параметр - идентификатор организации");
                        return false;
                    }

                    var length = organizations.length;
                    for (var i = 0; i < length; i++) {
                        if (organizations[i].id.value === organizationId) {
                            organizations.splice(i, 1);
                            currentOrganization = undefined;
                            return true;
                        }
                    }

                    $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> deleteOrganization: Организация с идентификатором " + organizationId + " не найдена");
                    return false;
                },

            },



            /**
             * Возвращает массив всех производственных отделений
             * @returns {Array}
             */
            getDepartments: function () {
                return departments;
            },

            getDepartmentById: function (id) {
                if (id !== undefined) {
                    var length = departments.length;
                    for (var i = 0; i < length; i++) {
                        if (departments[i].id.value === id)
                            return departments[i];
                    }
                    return false;
                } else {  
                    $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> getDepartmentById: Не задан параметр - идентификатор производственного отделения");
                    return false
                }
            },

            getDivisions: function () {
                return divisions;
            },

            getDivisionById: function (id) {
                if (id !== undefined) {
                    var length = divisions.length;
                    for (var i = 0; i < length; i++) {
                        if (divisions[i].id.value === id)
                            return divisions[i];
                    }
                    return false;
                } else {
                    $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> getDivisionById: Не задан параметр - идентификатор структурного подразделения");
                    return false
                }
            },

            addDivision: function (division) {
                if (division !== undefined) {
                    divisions.push(division);
                    return true;
                } else {
                    $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> addDivision: Не задан параметр - объект с информацией о новом отделе");
                    return false
                }
            }
        }
    };



    function kolenergoRun ($log, $kolenergo, $classes) {
        $log.log("krypton.app.kolenergo run...");
        $kolenergo.init();
    };


    function UserAccountController ($log, $scope, $session, $kolenergo) {
        $scope.user = $session.getCurrentUser();
        $scope.kolenergo = $kolenergo;


        $scope.department = {
            editing: false,
            loading: false,
            edit: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.editing = flag;
                else
                    return this.editing;
            },
            cancel: function () {
                if (this.edit() === true) {
                    this.edit(false);
                    $scope.user.departmentId._change_(false);
                    $scope.user.departmentId._backup_();
                }
            },
            save: function () {
                if (this.edit() === true) {

                }
            }
        };
        
        
        
        $scope.position = {
            editing: false,
            adding: false,
            loading: false,
            edit: function (flag) {
                if (flag !== undefined && typeof flag === "boolean")
                    this.editing = flag;
                else
                    return this.editing;
            },
            cancel: function () {
                if (this.edit() === true) {
                    this.edit(false);
                    $scope.user.position._change_(false);
                    $scope.user.position._backup_();
                }
            },
            save: function () {
                if (this.edit() === true) {

                }
            }
        };



        $scope.phone = {
            editing: false,
            adding: false,
            loading: false,
            new: "",
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
            },
            cancel: function () {
                if (this.edit() === true) {
                    this.edit(false);
                    $scope.user.phone._change_(false);
                    $scope.user.phone._backup_();
                }
                if (this.add() === true) {
                    this.add(false);
                    this.new = "";
                }
            },
            save: function () {
                if (this.edit() === true) {

                }
                if (this.add() === true) {

                }
            }
        };


        $scope.mobile = {
            editing: false,
            adding: false,
            loading: false,
            new: "",
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
            },
            cancel: function () {
                if (this.edit() === true) {
                    this.edit(false);
                    $scope.user.mobile._change_(false);
                    $scope.user.mobile._backup_();
                }
                if (this.add() === true) {
                    this.add(false);
                    this.new = "";
                }
            },
            save: function () {
                if (this.edit() === true) {

                }
                if (this.add() === true) {

                }
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




    function companyController ($log, $scope, $kolenergo, $filter, $modals, $rootScope, $factory, $http) {
        $scope.kolenergo = $kolenergo;
        $scope.$modals = $modals;
        $scope.departments = $kolenergo.getDepartments();
        $scope.divisions = $kolenergo.getDivisions();
        $scope.currentOrganization = undefined;
        $scope.currentDepartmentId = undefined;
        $scope.currentDivision = undefined;
        $scope.newOrganization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
        $scope.newDivision = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });

        $log.log("scope");


        $scope.newDivision._backup_.setup();


        $scope.onSelectDepartment = function (departmentId) {
            $log.log("onSelect fired");
            if (departmentId !== undefined) {
                var length = $kolenergo.getDepartments().length;
                for (var i = 0; i < length; i++) {
                    var department = $kolenergo.getDepartments()[i];
                    if (department.id.value === departmentId) {
                        department._states_.selected(true);
                        $scope.currentDepartment = department;
                    } else
                        department._states_.selected(false);
                }
                $scope.divisions = $filter("byDepartmentId")($scope.divisions, department.id.value);
                $log.log("filter = ", $scope.divisions);
            }
        };


        
        $scope.onSelectDivision = function (division) {
            if (division !== undefined) {
                //$log.log("onSelectHierarchyItem", division);
                $scope.currentDivision = division;
                $log.log("current division = ", $scope.currentDivision);
                $scope.newDivision.parentId.value = $scope.currentDivision.id.value;
            } else {
                $scope.currentDivision = undefined;
                $scope.newDivision.parentId.value = 0;
            }
        };


        /**
         * Открывает модальное окно добавления новой организации
         */
        $scope.openAddOrganizationModal = function () {
            $modals.open("new-organization-modal");
            $scope.newOrganization._backup_.restore();
            $scope.newOrganization._backup_.setup();
        };


        /**
         * Закрывает модальное окно добавления новой организации
         */
        $scope.closeAddOrganizationModal = function () {
            $log.log("org add close");
            $scope.newOrganization._backup_.restore();
            $log.log("new org title = ", $scope.newOrganization.title.value);
        };


        /**
         * Добавляет новую организацию
         */
        $scope.addOrganization = function () {
            var data = {
                title: $scope.newOrganization.title.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "addOrganization", data : data })
                .success(function (data) {
                    if (data !== undefined) {
                        $log.log(data);
                        var organization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
                        organization._model_.fromAnother(data);
                        organization._backup_.setup();
                        $kolenergo.organizations.add(organization);
                        $modals.close("new-organization-modal");
                    }
                });
        };

        
        /**
         * Открывает модальное окно редактирования организации
         */
        $scope.openEditOrganizationModal = function () {
            $modals.open("edit-organization-modal");
        };


        /**
         * Закрывает модальное окно редактирования организации
         */
        $scope.closeEditOrganizationModal = function () {
            //$log.log($scope.currentOrganization._backup_.data);
            //$scope.currentOrganization._backup_.restore();
            $kolenergo.organizations.getCurrent()._backup_.restore();
            $log.log("close edit");
        };

        
        /**
         * Сохраняет изменения измененнной организации
         */
        $scope.editOrganization = function () {
            var data = {
                id: $kolenergo.organizations.getCurrent().id.value,
                title: $kolenergo.organizations.getCurrent().title.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "editOrganization", data : data })
                .success(function (data) {
                    if (data !== undefined && data !== null) {
                        $log.log(data);
                        var organization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
                        organization._model_.fromAnother(data);
                        $kolenergo.organizations.getCurrent()._model_.fromAnother(organization);
                        $kolenergo.organizations.getCurrent()._backup_.setup();
                        $modals.close("edit-organization-modal");
                    }
                });
        };


        /**
         * Открывает модальное окно удаления организации
         */
        $scope.openDeleteOrganizationModal = function () {
            $modals.open("delete-organization-modal");
        };


        /**
         * Удаляет организацию
         */
        $scope.deleteOrganization = function () {
            var params = {
                id: $kolenergo.organizations.getCurrent().id.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "deleteOrganization", data : params })
                .success(function (data) {
                    $log.log(data);
                    if (data !== undefined && data !== null) {
                        if (JSON.parse(data) === true) {
                            $kolenergo.organizations.delete(params.id);
                            //$scope.currentOrganization = undefined;
                            $modals.close("delete-organization-modal");
                        }
                    }
                });
        };



        
        $scope.onAddNewDivision = function () {
            var data = {
                title: $scope.newDivision.title.value,
                parentId: $scope.newDivision.parentId.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "addDivision", data : data })
                .success(function (data) {
                    if (data !== undefined) {
                        $log.log(data);
                        var division = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
                        division._model_.fromAnother(data);
                        division._backup_.setup();
                        $kolenergo.addDivision(division);
                        $modals.close("new-division-modal");
                    }
                });
        };

        


        $scope.onAddDivision = function () {
            $modals.open("new-division-modal");
        };

        $scope.onCancelAddNewDivision = function () {
            $log.log("closed");
            $scope.newDivision._backup_.restore();
            if ($scope.currentDivision !== undefined)
                $scope.newDivision.parentId.value = $scope.currentDivision.id.value;
            //$modals.close("test-modal");
        };


        $scope.onEditDivision = function () {
            $modals.open("edit-division-modal");
        };

        $scope.editDivision = function () {
            var data = {
                id: $scope.currentDivision.id.value,
                title: $scope.currentDivision.title.value,
                parentId: $scope.currentDivision.parentId.value
            };
            $http.post("/serverside/libs/krypton/api.php", {action: "editDivision", data : data })
                .success(function (data) {
                    if (data !== undefined) {
                        $log.log(data);
                        var division = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
                        division._model_.fromAnother(data);
                        $scope.currentDivision._model_.fromAnother(division);
                        $scope.currentDivision._backup_.setup();
                        $log.log("updated division = ", $scope.currentDivision);
                        $modals.close("edit-division-modal");
                    }
                });
        };

        $scope.onCancelEditDivision = function () {
            $scope.currentDivision._backup_.restore();
            $scope.currentDivision._states_.changed(false);
        };
    };






    function byDepartmentIdFilter () {
        return function (input, id) {
            if (id !== undefined || id !== 0) {
                var result = [];
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    if (input[i].departmentId.value === id)
                        result.push(input[i]);
                }
                return result;
            }
        }
    };
})();
