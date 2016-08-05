"use strict";

(function () {
    angular
        .module("krypton.app.kolenergo", ["krypton"])
        .factory("$kolenergo", kolenergoFactory)
        .controller("UserAccountController", UserAccountController)
        .controller("LoginController", LoginController)
        .controller("PhoneBookController", PhoneBookController)
        .filter("byDepartmentId", byDepartmentIdFilter)
        .filter("byDivisionId", byDivisionIdFilter)
        .filter("phoneBook", phoneBookFilter)
        .run(kolenergoRun);



    function kolenergoFactory ($log, $classes, $factory, $errors, $navigation, $http) {
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
            organizationId: new Field({ source: "organization_id", type: DATA_TYPE_INTEGER, default_value: 0, value: 0, backupable: true }),
            departmentId: new Field({ source: "department_id", type: DATA_TYPE_STRING, value: 0, default_value: 0, backupable: true }),
            parentId: new Field({ source: "parent_id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true }),
            title: new Field({ source: "title", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            path: new Field({ source: "path", type: DATA_TYPE_STRING, default_value: "", value: "", backupable: true })
        });

        var organizations = [];
        var departments = [];
        var divisions = [];

        var currentOrganization = undefined;
        var currentDivision = undefined;
        var currentOrganizationDivisions = [];
        
        return {


            init: function () {
                $classes.getAll().User.organizationId = new Field({ source: "organization_id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true });
                $classes.getAll().User.departmentId = new Field({ source: "department_id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true });
                $classes.getAll().User.divisionId = new Field({ source: "division_id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true });

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
                    id: "companies",
                    parentId: "",
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
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> organizations -> select: Не задан параметр - идентификатор организации");
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

                    return true;
                },

                /**
                 * Добавляет новую организацию
                 * @param organization {Organization} - объект с информацией о новой организации
                 * @returns {Organization / boolean}
                 */
                add: function (organization, callback) {
                    if (organization === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> organizations -> add: Не задан параметр - объект с информацией о новой организации");
                        return false;
                    }

                    var params = {
                        action: "addOrganization",
                        title: organization.title.value
                    };

                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var organization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
                                    organization._model_.fromAnother(data.result);
                                    organization._backup_.setup();
                                    organizations.push(organization);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(organization);

                                    return true;
                                }
                            }
                        });

                    return false;
                },

                edit: function (callback) {
                    if (currentOrganization === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> organizations -> edit: Не выбрана текущая организация");
                        return false;
                    }

                    var params = {
                        action: "editOrganization",
                        id: currentOrganization.id.value,
                        title: currentOrganization.title.value
                    };

                    currentOrganization._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    currentOrganization._backup_.setup();
                                    currentOrganization._states_.loading(false);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback();
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                /**
                 * Удаляет организацию с заданным идентификатором
                 * @param id {number} - идентификатор организации
                 * @returns {boolean}
                 */
                delete: function (callback) {
                    if (currentOrganization === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> organizations -> delete: Не выбрана текущая организация");
                        return false;
                    }

                    var params = {
                        action: "deleteOrganization",
                        id: currentOrganization.id.value
                    };

                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            currentOrganization._states_.loading(false);
                            $log.log(data);
                            if (data !== undefined && data !== null) {
                                $errors.checkResponse(data);
                                if (data.result !== false) {
                                    if (JSON.parse(data.result) === true) {
                                        var length = organizations.length;
                                        for (var i = 0; i < length; i++) {
                                            if (organizations[i].id.value === currentOrganization.id.value) {
                                                currentOrganization._states_.loading(false);
                                                currentOrganization = undefined;
                                                organizations.splice(i, 1);
                                                if (callback !== undefined && typeof callback === "function")
                                                    callback();
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                },

            },

            divisions: {

                /**
                 * Возвращает массив всех отделов
                 * @returns {Array}
                 */
                getAll: function () {
                    return divisions;
                },

                getByOrganizationId: function (organizationId) {
                    $log.log("org id = ", organizationId);
                    if (organizationId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> divisions -> getByOrganizationId: Не задан параметр - идентификатор организации");
                        return false;
                    }

                    var result = [];
                    var length = divisions.length;
                    for (var i = 0; i < length; i++) {
                        if (divisions[i].organizationId.value === organizationId)
                            result.push(divisions[i]);
                    }

                    return result;
                },

                /**
                 * Возвращает текущий отдел
                 * @returns {Division / undefined}
                 */
                getCurrent: function () {
                    return currentDivision;
                },

                /**
                 * Выбирает отдел по идентификатору
                 * @param divisionId {number} - идентификатор отдела
                 * @returns {boolean}
                 */
                select: function (divisionId) {
                    if (divisionId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$kolenergo -> divisions -> select: Не задан параметр - идентификатор отдела");
                        return false;
                    }

                    var length = divisions.length;
                    for (var i = 0; i < length; i++) {
                        if  (divisions[i].id.value === divisionId) {
                            $log.log("div found", divisions[i]);
                            if (divisions[i]._states_.selected() === false) {
                                divisions[i]._states_.selected(true);
                                currentDivision = divisions[i];
                                $log.log("cur div = ", currentDivision);
                            } else {
                                divisions[i]._states_.selected(false);
                                currentDivision = undefined;
                            }
                        } else
                            divisions[i]._states_.selected(false);
                    }

                    return true;
                },

                /**
                 * Добавляет новый отдел
                 * @param division {Division} - объект с информацией о новом отделе
                 * @returns {boolean}
                 */
                add: function (division, callback) {
                    if (division == undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> divisions -> add: Не задан параметр - объект с информацией о новом отделе");
                        return false;
                    }

                    var params = {
                        action: "addDivision",
                        organizationId: division.organizationId.value,
                        departmentId: division.departmentId.value,
                        parentId: division.parentId.value,
                        title: division.title.value,
                        path: division.path.value
                    };

                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    var division = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });
                                    division._model_.fromAnother(data.result);
                                    division._backup_.setup();
                                    divisions.push(division);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(division);
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                edit: function (callback) {
                    if (currentDivision === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> divisions -> edit: Не выбрано текущее структурное подразделение");
                        return false;
                    }

                    var params = {
                        action: "editDivision",
                        id: currentDivision.id.value,
                        departmentId: currentDivision.departmentId.value,
                        parentId: currentDivision.parentId.value,
                        title: currentDivision.title.value
                    };

                    currentDivision._states_.loading(true);
                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                $errors.checkResponse(data);
                                if (data.result !== undefined && data.result !== false) {
                                    currentDivision._backup_.setup();
                                    currentDivision._states_.loading(false);
                                    if (callback !== undefined && typeof  callback === "function")
                                        callback(currentDivision);
                                    return true;
                                }
                            }
                        });

                    return false;
                },

                /**
                 * Удаляет структурное подразделение с заданным идентификатором
                 * @callback {function} - callback-функция
                 * @returns {boolean}
                 */
                delete: function (callback) {
                    if (currentDivision === undefined) {
                        $errors.add(ERROR_TYPE_ENGINE, "$kolenergo -> divisions -> delete: Не выбрано текущее структурное подразделение");
                        return false;
                    }

                    var params = {
                        action: "deleteDivision",
                        id: currentDivision.id.value
                    };

                    $http.post("/serverside/libs/krypton/api.php", params)
                        .success(function (data) {
                            currentDivision._states_.loading(false);
                            $log.log(data);
                            if (data !== undefined && data !== null) {
                                $errors.checkResponse(data);
                                if (data.result !== false) {
                                    if (JSON.parse(data.result) === true) {
                                        var length = divisions.length;
                                        for (var i = 0; i < length; i++) {
                                            if (divisions[i].id.value === currentDivision.id.value) {
                                                currentDivision._states_.loading(false);
                                                currentDivision = undefined;
                                                divisions.splice(i, 1);
                                                if (callback !== undefined && typeof callback === "function")
                                                    callback();
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                }
            },


            getUsersByDivisionId: function (id) {
                if (id === undefined) {
                    $errors.push(ERROR_TYPE_DEFAULT, "$kolenergo -> getUsersByDivisionId: Не задан парметр - идентификатор структурного подразделения");
                    return false;
                }

                var params = {
                    action: "getUsersByDivisionId",
                    id: id
                };

                $http.post("/serverside/libs/krypton/api.php", params)
                    .success(function (data) {
                        if (data !== undefined) {
                            $errors.checkResponse(data);
                            if (data.result !== undefined && data.result !== false) {
                                return data.result;
                            }
                        }
                    });
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



    function kolenergoRun ($log, $kolenergo, $classes, $http) {
        $log.log("krypton.app.kolenergo run...");
        $kolenergo.init();
    };


    function UserAccountController ($log, $scope, $session, $kolenergo, $http, $factory) {
        $scope.user = $session.getCurrentUser();
        $scope.kolenergo = $kolenergo;
        
        $scope.uploaderData = {
            action: "uploadUserPhoto",
            userId: $session.getCurrentUser().id.value
        };


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
        
        
        $scope.onCompleteUploadUserPhoto = function (data) {
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
                $session.getCurrentUser().photo.value = file.url.value;
            }

        };
    };




    function companyController ($log, $scope, $kolenergo, $filter, $modals, $rootScope, $factory, $http, $hierarchy, $errors, $tree) {
        $scope.kolenergo = $kolenergo;
        $scope.modals = $modals;
        $scope.tree = [];
        //$scope.hierarchy = $kolenergo.organizations.getCurrent() !== undefined ? $kolenergo.divisions.getByOrganizationId($kolenergo.organizations.getCurrent().id.value) : [];
        $scope.newOrganization = $factory({ classes: ["Organization", "Model", "Backup", "States"], base_class: "Organization" });
        $scope.newDivision = $factory({ classes: ["Division", "Model", "Backup", "States"], base_class: "Division" });

        if ($kolenergo.organizations.getCurrent() !== undefined && $scope.tree.length === 0) {
            $scope.tree = $kolenergo.divisions.getByOrganizationId($kolenergo.organizations.getCurrent().id.value);
            $log.log("stack before = ", $tree.getById("test-tree").stack);
            for (var i = 0; i < $scope.tree.length; i ++) {
                $tree.addItem("test-tree", $scope.tree[i]);
            }
            $log.log("stack after = ", $tree.getById("test-tree").stack);
        }

        $log.log("tree = ", $scope.tree);

        
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


        
        $scope.selectDivision = function (division) {
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


        
        $scope.selectOrganization = function (organizationId) {
            if ($kolenergo.organizations.getCurrent() === undefined) {
                $kolenergo.organizations.select(organizationId);
                $scope.tree = $kolenergo.divisions.getByOrganizationId(organizationId);
                for (var x = 0; x < $scope.tree.length; x++) {
                    $tree.addItem("test-tree", $scope.tree[x]);
                }
            } else {
                $kolenergo.organizations.select(organizationId);
                $tree.clear("test-tree");
                $scope.tree = [];
            }

            //$scope.tree = $kolenergo.divisions.getByOrganizationId(organizationId);
            //$scope.tree = $filter("orderBy")($scope.tree, "id.value");



            $log.log("tree = ", $scope.tree);
            //$log.log("hierarchy = ", $scope.hierarchy);
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
            $scope.newOrganization._states_.loading(true);
            $kolenergo.organizations.add($scope.newOrganization, function () {
                $modals.close();
                $scope.newOrganization._states_.loading(false);
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
            $kolenergo.organizations.getCurrent()._backup_.restore();
        };


        
        /**
         * Сохраняет изменения измененнной организации
         */
        $scope.editOrganization = function () {
            $kolenergo.organizations.edit(function () {
                $modals.close();
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
            $kolenergo.organizations.delete(function () {
                $modals.close();
            });
        };

        

        $scope.openAddNewDivisionModal = function () {
            $modals.open("new-division-modal");
            if ($kolenergo.organizations.getCurrent() !== undefined)
                $scope.newDivision.organizationId.value = $kolenergo.organizations.getCurrent().id.value;
        };



        $scope.closeAddNewDivisionModal = function () {
            $scope.newDivision._backup_.restore();
            if ($kolenergo.divisions.getCurrent() !== undefined)
                $scope.newDivision.parentId.value = $kolenergo.divisions.getCurrent().id.value;
        };
        
        
        
        $scope.addDivision = function () {
            $kolenergo.divisions.add($scope.newDivision, function (div) {
                $scope.newDivision._backup_.restore();
                if ($kolenergo.divisions.getCurrent() !== undefined)
                    $scope.newDivision.parentId.value = $kolenergo.divisions.getCurrent().id.value;
                $tree.addItem("test-tree", div);
                $modals.close();
            });
        };
        
        
        
        $scope.selectDivision = function (division) {
            if (division !== undefined) {
                $kolenergo.divisions.select(division.id.value);
                if (division._states_.selected() === true)
                    $scope.newDivision.parentId.value = division.id.value;
                else
                    $scope.newDivision.parentId.value = 0;
                $log.log("new div parentId = ", $scope.newDivision.parentId.value);
            }
        };



        $scope.openEditDivisionModal = function () {
            $modals.open("edit-division-modal");
        };



        $scope.closeEditDivisionModal = function () {
            $kolenergo.divisions.getCurrent()._backup_.restore();
            $kolenergo.divisions.getCurrent()._states_.changed(false);
        };



        $scope.editDivision = function () {
            $kolenergo.divisions.edit(function () {
                $modals.close();
            });
        };



        $scope.openDeleteDivisionModal = function () {
            $modals.open("delete-division-modal");
        };



        /**
         * Удаляет структурное подразделение
         */
        $scope.deleteDivision = function () {
            var id = $kolenergo.divisions.getCurrent().id.value;
            $kolenergo.divisions.delete(function () {
                $modals.close("delete-division-modal");
                $tree.deleteItem("test-tree", id);
            });
        };


    };
    
    
    
    
    function LoginController ($scope, $log,  $errors, $http, $factory, $location, $session) {
        $scope.login = "";
        $scope.password = "";
        $scope.errors = [];
        $scope.loading = false;


        $scope.send = function () {
            $scope.errors.splice(0, $scope.errors.length);

            if ($scope.login === "")
                $scope.errors.push("Вы не указали учетную запись");

            if ($scope.password === "")
                $scope.errors.push("Вы не указали пароль");

            if ($scope.errors.length === 0) {
                $scope.loading = true;
                var params = {
                    action: "login",
                    login: $scope.login,
                    password: $scope.password
                };
                $http.post("serverside/libs/krypton/api.php", params)
                    .success (function (data) {
                        $log.log(data);
                        $scope.loading = false;

                        $errors.checkResponse(data);


                        if (data.result !== false) {
                            var user = $factory({ classes: ["User", "Model", "Backup", "States"], base_class: "User" });
                            user._model_.fromAnother(data.result);
                            user._backup_.setup();
                            $log.log(user);
                            $session.setCurrentUser(user);
                            $location.url("\account");
                        } else {
                            $scope.errors.push("Пользователь не найден");
                        }

                    });
            }
        };
    };





    function PhoneBookController ($scope, $log, $users, $kolenergo, $tree) {
        $scope.users = $users;
        $scope.kolenergo = $kolenergo;
        $scope.search = "";

        $scope.div = 0;

        //$scope.divs = $kolenergo.divisions.getByOrganizationId(8);
        //$log.log("divs = ", $scope.divs);
        //var length = $scope.divs.length;
        //for (var i = 0; i < length; i++) {
        //    $tree.addItem("test", $scope.divs[i]);
        //
        //}

        $scope.selectDivision = function (div) {
            $log.log("selected = ", div.id.value);
            //$kolenergo.divisions.select(div.id.value);
            //if ($scope.div !== div.id.value)
            //    $scope.div = div.id.value;
            //else
            //    $scope.div = 0;
            $kolenergo.getUsersByDivisionId(div.id.value);
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


    function byDivisionIdFilter ($log, $kolenergo) {
        return function (input, id) {
            if (id !== -1 && id !== undefined) {
                var divs = [];
                var result = [];

                var divLength = $kolenergo.divisions.getAll().length;
                for (var x = 0; x < divLength; x++) {
                    var div = $kolenergo.divisions.getAll()[x];
                    if (div.path.value.indexOf(id) !== -1)
                        divs.push(div);
                }

                var length = input.length;
                for (var i = 0; i < length; i++) {
                    var selected = divs.length;
                    for (var z = 0; z< selected; z++) {
                        if (input[i].divisionId.value === divs[z].id.value)
                            result.push(input[i]);
                    }
                }
                $log.log("filtered ", result.length);
                return result;
            } else
                return input;
        }
    };

    
    function phoneBookFilter ($log, $kolenergo) {
        return function (input, search) {
            var result = [];

            if ($kolenergo.divisions.getCurrent() === undefined && search.length > 2) {
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    var temp = input[i].search.toLowerCase();
                    if (temp.indexOf(search.toLowerCase()) !== -1)
                        result.push(input[i]);
                }
                return result;
            }
            else if ($kolenergo.divisions.getCurrent() === undefined) {

            } else if ($kolenergo.divisions.getCurrent() !== undefined && search.length > 2) {
                var length = input.length;
                for (var i = 0; i < length; i++) {
                    var temp = input[i].search.toLowerCase();
                    if (temp.indexOf(search.toLowerCase()) !== -1)
                        result.push(input[i]);
                }
                return result;
            } else if ($kolenergo.divisions.getCurrent() !== undefined && search.length === 0) {
                return input;
            }
        }
    };


})();
