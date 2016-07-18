"use strict";

var requests = angular.module("gears.app.requests", [])
    .config(function ($provide) {
        $provide.factory("$requests", ["$log", "$http", "$factory", "$session", function ($log, $http, $factory, $session) {
            var service = {};

            /**
             * Наборы свойст и методов, описывающих модели данных
             */
            service.classes = {
                /**
                 * RequestType
                 * Набор свойств, описывающих тип заяви
                 */
                RequestType: {
                    id: new Field({ source: "ID", value: 0, default_value: 0 }),
                    title: new Field({ source: "TITLE", value: "", default_value: "" })
                },

                /**
                 * RequestStatus
                 * Набор свойств, описывающих статус заявки
                 */
                RequestStatus: {
                    id: new Field({ source: "ID", value: 0, default_value: 0 }),
                    title: new Field({ source: "TITLE", value: "", default_value: "", backupable: true, required: true })
                },

                /**
                 * RequestStatusAttachment
                 * Набор свойств, описывающих приложение к статусу заяки
                 */
                RequestStatusAttachment: {
                    id: new Field({ source: "ID", value: 0, default_value: 0 }),
                    requestId: new Field({ source: "REQUEST_ID", value: 0, default_value: 0 }),
                    statusId: new Field({ source: "STATUS_ID", value: 0, default_value: 0 }),
                    userId: new Field({ source: "USER_ID", value: 0, default_value: 0 }),
                    added: new Field({ source: "ADDED", value: 0, default_value: 0 })
                },

                /**
                 * Request
                 * Набор свойств, описывающих заявку
                 */
                Request: {
                    id: new Field({ source: "ID", value: 0, default_value: 0 }),
                    requestTypeId: new Field({ source: "REQUEST_TYPE_ID", value: 1, default_value: 1, backupable: true, required: true }),
                    statusId: new Field({ source: "STATUS_ID", value: 0, default_value: 0, backupable: true }),
                    userId: new Field({ source: "USER_ID", value: 0, default_value: 0, backupable: true, required: true }),
                    curatorId: new Field({ source: "CURATOR_ID", value: 0, default_value: 0, backupable: true }),
                    investorId: new Field({ source: "INVESTOR_ID", value: 0, default_value:0, backupable: true, required: true }),
                    titleId: new Field({ source: "TITLE_ID", value: 0, default_value: 0, backupable: true }),
                    title: new Field({ source: "TITLE", value: "", default_value: "", backupable: true, required: true }),
                    description: new Field({ source: "DESCRIPTION", value: "", default_value: "", backupable: true }),
                    buildingPlanDate: new Field({ source: "BUILDING_PLAN_DATE", value: 0, default_value: 0 }),
                    resources: new Field({ source: "RESOURCES", value: "", default_value: "", backupable: true}),
                    added: new Field({ source: "ADDED", value: 0, default_value: 0 }),
                    tu: new Field({ source: "TU_DOC", value: 0, default_value: 0, backupable: true }),
                    genSogl: new Field({ source: "GEN_SOGL_DOC", value: 0, default_value: 0, backupable: true }),
                    doud: new Field({ source: "DOUD_DOC", value: 0, default_value: 0, backupable: true }),
                    inputDocs: $factory({ classes: ["Collection", "States"], base_class: "Collection" }),

                    onInitModel: function () {
                        this.tu.value = this.tu.value === 1 ? true : false;
                        this.genSogl.value = this.genSogl.value === 1 ? true : false;
                        this.doud.value = this.doud.value === 1 ? true : false;
                    }
                },

                /**
                 * RequestHistory
                 * набор свойст, описывающих изменение истории статусов заявки
                 */
                RequestHistory: {
                    id: new Field({ source: "ID", value: 0, default_value: 0, backupable: true, required: true }),
                    requestId: new Field({ source: "REQUEST_ID", value:0, default_value: 0, backupable: true }),
                    statusId: new Field({ source: "STATUS_ID", value: 1, default_value: 1, backupable: true }),
                    userId: new Field({ source: "USER_ID", value: 0, default_value: 0, backupable: true }),
                    description: new Field({ source: "DESCRIPTION", value: "", default_value: "", backupable: true }),
                    timestamp: new Field({ source: "TIMESTAMP", value: 0, default_value: 0 })
                }
            };


            /**
             * Получает список всех типов заявки
             */
            service.getRequestTypes = function () {
                service.requestTypes._states_.loaded(false);
                $http.post("serverside/controllers/titles.php", { action: "getRequestTypes" })
                    .success(function (data) {
                            if (data !== undefined) {
                                if (data["error_code"] !== undefined) {
                                    var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                    db_error.init(data);
                                    db_error.display();
                                } else {
                                    angular.forEach(data, function (requestType) {
                                        var temp_type = $factory({ classes: ["RequestType", "Model", "Backup", "States"], base_class: "RequestType" });
                                        temp_type._model_.fromJSON(requestType);
                                        temp_type._backup_.setup();
                                        service.requestTypes.append(temp_type);
                                    });
                                }
                            }
                            service.requestTypes._states_.loaded(true);
                        }
                    );
            };


            /**
             * Получает список всех статусов заявки
             */
            service.getRequestStatuses = function () {
                service.requestStatuses._states_.loaded(false);
                $http.post("serverside/controllers/titles.php", { action: "getRequestStatuses" })
                    .success(function (data) {
                            if (data !== undefined) {
                                if (data["error_code"] !== undefined) {
                                    var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                    db_error.init(data);
                                    db_error.display();
                                } else {
                                    angular.forEach(data, function (requestStatus) {
                                        var temp_status = $factory({ classes: ["RequestStatus", "Model", "Backup", "States"], base_class: "RequestStatus" });
                                        temp_status._model_.fromJSON(requestStatus);
                                        temp_status._backup_.setup();
                                        service.requestStatuses.append(temp_status);
                                    });
                                }
                            }
                            service.requestStatuses._states_.loaded(true);
                        }
                    );
            };


            /**
             * Добавляет заявку
             * @param request {Request} - долбавляемая заявка
             * @param callback {Function} - коллбэк-функция
             */
            service.addRequest = function (request, callback) {
                if (request !== undefined) {
                    var params = {
                        action: "addRequest",
                        data: {
                            requestTypeId: request.requestTypeId.value,
                            title: request.title.value,
                            description: request.description.value,
                            buildingPlanDate: request.buildingPlanDate.value,
                            resources: request.resources.value,
                            investorId: request.investorId.value,
                            userId: $session.user.get().id.value,
                            curatorId: request.curatorId.value
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                        db_error.init(data);
                                        db_error.display();
                                    } else {
                                        if (callback !== undefined)
                                            callback(data);
                                    }
                                }
                            }
                        );
                }
            };


            /**
             * Редактирует заявку
             * @param request {Request} - редактируеая заявка
             * @param callback {Function} - функция-коллбэк
             */
            service.editRequest = function (request, callback) {
                if (request !== undefined) {
                    var params = {
                        action: "editRequest",
                        data: {
                            requestId: request.id.value,
                            requestTypeId: request.requestTypeId.value,
                            investorId: request.investorId.value,
                            title: request.title.value,
                            description: request.description.value,
                            resources: request.resources.value,
                            buildingPlanDate: request.buildingPlanDate.value
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                        db_error.init(data);
                                        db_error.display();
                                    } else {
                                        if (callback !== undefined)
                                            callback(data);
                                    }
                                }
                            }
                        );
                }
            };


            /**
             * Удаляет заявку
             * @param requestId {Number} - Идентификатор заявки
             * @param callback {Function} - Коллбэк-функция
             */
            service.deleteRequest = function (requestId, callback) {
                if (requestId !== undefined) {
                    var params = {
                        action: "deleteRequest",
                        data: {
                            requestId: requestId
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                        db_error.init(data);
                                        db_error.display();
                                    } else {
                                        if (callback !== undefined)
                                            callback(data);
                                    }
                                }
                            }
                        );
                }
            };


            /**
             * Меняет статус заявки
             * @param requestId {Number} - идентификатор заявки
             * @param statusId {Number} - Идентификатор статуса заявки
             * @param description {String} - Комментарий к новому статусу заявки
             * @param callback {Function} - Коллбэк-функция
             */
            service.changeRequestStatus = function (requestId, statusId, description, callback) {
                if (requestId !== undefined && statusId !== undefined) {
                    var params = {
                        action: "changeRequestStatus",
                        data: {
                            requestId: requestId,
                            statusId: statusId,
                            userId: $session.user.get().id.value,
                            description: description
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                        db_error.init(data);
                                        db_error.display();
                                    } else {

                                        if (callback !== undefined)
                                            callback(data);
                                    }
                                }
                            }
                        );
                }
            };


            /**
             * Получает историю изменения статусаов заявки
             * @param request {Request} - Заявка, историю статусов которой требуется получить
             * @param callback {Function} - Коллбэк-функция
             */
            service.getRequestHistory = function (request, callback) {
                if (request !== undefined) {
                    var params = {
                        action: "getRequestHistory",
                        data: {
                            requestId: request.id.value
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                        db_error.init(data);
                                        db_error.display();
                                    } else {
                                        if (callback !== undefined)
                                            callback(data);
                                    }
                                }
                            }
                        );
                }
            };


            /**
             * Добавляет историю изменения статуса заявки
             * @param history {RequestHistory} - История изменения статуса заявки
             * @param callback {Function} - Коллбэк-функция
             */
            service.addRequestHistory = function (history, callback) {
                if (history !== undefined) {
                    var params = {
                        action: "addRequestHistory",
                        data: {
                            requestId: history.requestId.value,
                            statusId: history.statusId.value,
                            userId: 46,
                            description: history.description.value
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                        db_error.init(data);
                                        db_error.display();
                                    } else {
                                        if (callback !== undefined)
                                            callback(data);
                                    }
                                }
                            }
                        );
                }
            };


            /**
             * Редактирует историю изменения статуса заявки
             * @param historyId {Number} - Идентификатор истории изменения статуса заявки
             * @param description {String} - Комментарий к истории изменения статуса заявки
             * @param callback {Function} - Коллбэк-функция
             */
            service.editRequestHistory = function (historyId, description, callback) {
                if (historyId !== undefined && description !== undefined) {
                    var params = {
                        action: "editRequestHistory",
                        data: {
                            historyId: historyId,
                            description: description
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({classes: ["DBError"], base_class: "DBError"});
                                        db_error.init(data);
                                        db_error.display();
                                    } else {
                                        if (callback !== undefined)
                                            callback(data);
                                    }
                                }
                            }
                        );
                }
            };


            /**
             * Удаляет историю изменения статуса заявки
             * @param history {RequestHistory} - История изменения статуса заявки
             * @param oldStatusId {Number} - Идентификатор предыдущего статуса заявки
             * @param callback {Function} - Коллбэк-функция
             */
            service.deleteRequestHistory = function (history, oldStatusId, callback) {
                if (history !== undefined && oldStatusId !== undefined) {
                    var params = {
                        action: "deleteRequestHistory",
                        data: {
                            historyId: history.id.value,
                            oldStatusId: oldStatusId
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                        db_error.init(data);
                                        db_error.display();
                                    } else {
                                        if (callback !== undefined)
                                            callback(data);
                                    }
                                }
                            }
                        );
                }
            };


            /**
             * Удаляет приложение к истории изменения статуса заявки
             * @param rsdId {Number} - Идентификатор приложения
             * @param callback {Function} - Коллбэк-функция
             */
            service.deleteRequestStatusDoc = function (rsdId, callback) {
                if (rsdId !== undefined) {
                    var params = {
                        action: "deleteRequestStatusDoc",
                        data: {
                            rsdId: rsdId
                        }
                    };
                    $http.post("serverside/controllers/titles.php", params)
                        .success(function (data) {
                                if (data !== undefined) {
                                    if (data["error_code"] !== undefined) {
                                        var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                        db_error.init(data);
                                        db_error.display();
                                    } else {
                                        if (callback !== undefined)
                                            callback();
                                    }
                                }
                            }
                        );
                }
            };



            /**
             * Переменные сервиса
             */
            service.requestTypes = $factory({ classes: ["Collection", "States"], base_class: "Collection" });
            service.requestStatuses = $factory({ classes: ["Collection", "States"], base_class: "Collection" });
            service.requests = $factory({ classes: ["Collection", "States"], base_class: "Collection" });

            return service;
        }])
    })
    .run(function ($modules, $menu, $requests) {
        $modules.load($requests);
        $menu.add({
            id: "requests",
            title: "Заявки",
            description: "Заявки",
            url: "#/requests",
            template: "templates/requests/requests.html",
            controller: "RequestsController",
            icon: "resources/img/icons/request.png",
            order: 1
        });
    });





/**
 * RequestsController
 * Контроллер раздела журнала заявок
 */
requests.controller("RequestsController", ["$log", "$scope", "$titles", "$requests", "$application", "$modals", "$contractors", "$factory", "$users", "$session", "$permissions", function ($log, $scope, $titles, $requests, $application, $modals, $contractors, $factory, $users, $session, $permissions) {
    $scope.app = $application;
    $scope.session = $session;
    $scope.permissions = $permissions;
    $scope.titles = $titles;
    $scope.requests = $requests;
    $scope.contractors = $contractors;
    $scope.users = $users;
    $scope.tabs = [
        {
            id: 1,
            title: "Информация о заявке",
            template: "templates/requests/request-details.html",
            isActive: true
        },
        {
            id: 2,
            title: "Документы",
            template: "templates/requests/request-documents.html",
            isActive: false
        }
    ];


    /**
     * Делает заяку текущей
     * @param requestId {number} - Идентификатор заявки
     */
    $scope.selectRequest = function (requestId) {
        if (requestId !== undefined) {
            angular.forEach($requests.requests.items, function (request) {
                if (request.id.value === requestId) {
                    if ($application.currentRequest !== undefined)
                        $application.currentRequest.inputDocs.clear();
                    if (request._states_.selected() === true) {
                        request._states_.selected(false);
                        $application.currentRequest = undefined;
                        $application.currentRequestHistory.clear();
                        $application.currentRequestStatusDocs.clear();
                    } else {
                        request._states_.selected(true);
                        $application.currentRequest = request;
                        $application.currentUploaderData["requestId"] = requestId;
                        $application.currentRequestHistory.clear();
                        $application.currentRequestHistory._states_.loaded(false);
                        $application.currentRequestStatusDocs.clear();
                        $application.currentRequestStatusDocs._states_.loaded(false);
                        $requests.getRequestHistory($application.currentRequest, $scope.onSuccessGetRequestHistory);
                    }
                } else {
                    request._states_.selected(false);
                }
            });
        }
    };


    /**
     * Коллбэк при получении исвтории изменения статусов заявки
     * @param data
     */
    $scope.onSuccessGetRequestHistory = function (data) {
        if (data !== undefined) {
            var firstHistoryId = 0;
            if (data["history"] !== undefined) {
                angular.forEach(data["history"], function (history) {
                    var temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
                    temp_history._model_.fromJSON(history);
                    temp_history._backup_.setup();
                    $application.currentRequestHistory.append(temp_history);
                    if (temp_history.statusId.value === 1) {
                        firstHistoryId = temp_history.id.value;
                    }
                });
                $application.currentRequestHistory._states_.loaded(true);
            }
            if (data["docs"] !== undefined) {
                angular.forEach(data["docs"], function (history) {
                    var temp_doc = $factory({ classes: ["RequestStatusAttachment", "FileItem_", "Model"], base_class: "RequestStatusAttachment" });
                    temp_doc._model_.fromJSON(history);
                    $application.currentRequestStatusDocs.append(temp_doc);
                    if (temp_doc.statusId.value === firstHistoryId) {
                        $application.currentRequest.inputDocs.append(temp_doc);
                    }
                });
                $application.currentRequestStatusDocs._states_.loaded(true);
            }
        }
    };


    /**
     * Обработчик добавления заявки
     */
    $scope.addRequest = function () {
        $modals.show({
            width: 500,
            position: "center",
            caption: "Новая заявка",
            showFog: true,
            closeButton: false,
            template: "templates/modals/new-request_.html",
            onClose: function () {},
            scope: function (scope) {}
        });
    };


    /**
     * Обработчик редактирования заявки
     * @param requestId {number} - Идентификатор заявки
     * @param event {event} - Объект-событие
     */
    $scope.editRequest = function (requestId, event) {
        event.stopPropagation();
        if (requestId !== undefined) {
            $modals.show({
                width: 500,
                position: "center",
                caption: "Редактирование заявки",
                showFog: true,
                closeButton: false,
                template: "templates/modals/edit-request.html"
            });
        }
    };


    /**
     * Обработчик удаления заявки
     * @param requestId {number} - Идентификатор заявки
     * @param event {event} - Объект-событие
     */
    $scope.deleteRequest = function (requestId, event) {
        event.stopPropagation();
        if (requestId !== undefined) {
            $modals.show({
                width: 500,
                position: "center",
                caption: "Удаление заявки",
                showFog: true,
                closeButton: true,
                template: "templates/modals/delete-request.html"
            });
        }
    };


    /**
     * Обработчик изменения статуса заявки
     */
    $scope.changeStatus = function () {
        $modals.show({
            width: 400,
            position: "center",
            caption: "Изменение статуса заявки",
            showFog: true,
            closeButton: false,
            template: "templates/modals/edit-request-status.html"
        });
    };

}]);




/**
 * RequestDetaailController
 * Контроллер информации о заявке
 */
requests.controller("RequestDetailsController", ["$log", "$scope", "$titles", "$application", "$contractors", "$factory", "$location", "$permissions", "$requests", function ($log, $scope, $titles, $application, $contractors, $factory, $location, $permissions, $requests) {
    $scope.titles = $titles;
    $scope.requests = $requests;
    $scope.app = $application;
    $scope.permissions = $permissions;
    $scope.contractors = $contractors;


    /**
     * Срабатывает перед отправкой ТУ на сервер
     */
    $scope.onBeforeUploadTU = function () {
        $application.currentUploaderData["doc_type"] = "tu";
    };

    /**
     * Коллбэк после отправки ТУ на сервер
     * @param data
     */
    $scope.onSuccessUploadTU = function (data) {
        if (data !== undefined)
            $application.currentRequest.tu.value = true;
    };

    /**
     * Срабатывает перед отправкой ГенСоглашения на сервер
     */
    $scope.onBeforeUploadGS = function () {
        $application.currentUploaderData["doc_type"] = "gs";
    };

    /**
     * Коллбэк после отправки ГенСоглашения на сервер
     * @param data
     */
    $scope.onSuccessUploadGS = function (data) {
        if (data !== undefined)
            $application.currentRequest.genSogl.value = true;
    };

    /**
     * Срабатывает перед отправкой ДОУД на сервер
     */
    $scope.onBeforeUploadDOUD = function () {
        $application.currentUploaderData["doc_type"] = "doud";
    };

    /**
     * Коллбэк после отправки ДОУД на сервер
     * @param data
     */
    $scope.onSuccessUploadDOUD = function (data) {
        if (data !== undefined)
            $application.currentRequest.doud.value = true;
    };
}]);





/**
 * RequestsDocumentController
 * Контроллер раздела документов заявки
 */
requests.controller("RequestDocumentsController", ["$log", "$scope", "$titles", "$application", "$users", function ($log, $scope, $titles, $application, $users) {
    $scope.titles = $titles;
    $scope.app = $application;
    $scope.users = $users;
}]);





/**********
 * MODAL CONTROLLERS
 **********/


/**
 * AddRequestModalController
 * Контроллер модального окна добавления заявки
 */
requests.controller("AddRequestModalController", ["$log", "$scope", "$misc", "$factory", "$application", "$modals", "$contractors", "$titles", "$requests", function ($log, $scope, $misc, $factory, $application, $modals, $contractors, $titles, $requests) {
    $scope.contractors = $contractors;
    $scope.titles = $titles;
    $scope.requests = $requests;
    $scope.app = $application;
    $scope.errors = [];
    $scope.tabs = [
        {
            id: 1,
            title: "Подробности",
            template: "templates/requests/new-request-details.html",
            isActive: true
        },
        {
            id: 2,
            title: "Приложения",
            template: "templates/requests/new-request-documents.html",
            isActive: false
        }
    ];



    /**
     * Производит валидацию данных формы добавления заявки
     */
    $scope.validate = function () {
        $scope.errors.splice(0, $scope.errors.length);
        if ($application.newRequest.title.value === "")
            $scope.errors.push("Вы не указали наименование объекта");
        if ($application.newRequest.description.value === "")
            $scope.errors.push("Вы не указали общую информацию об объекте");
        if ($application.newRequest.buildingPlanDate.value === "" || $application.newRequest.buildingPlanDate.value === 0)
            $scope.errors.push("Вы не указали планируемую дату строительства и ввода в эксплуатацию объекта");
        if ($scope.errors.length === 0) {
            if ($application.newRequest.id.value !== 0) {
                $requests.editRequest($application.newRequest, $scope.onSuccessEditRequest);
            } else {
                $requests.addRequest($application.newRequest, $scope.onSuccessAddRequest);
            }
        }
    };



    /**
     * Коллбэк при добавлении заявки
     * @param data
     */
    $scope.onSuccessAddRequest = function (data) {
        if (data !== undefined) {
            if (data["request"] !== undefined) {
                var temp_request = $factory({classes: ["Request", "Model", "Backup", "States"], base_class: "Request"});
                temp_request._model_.fromJSON(data["request"]);
                temp_request._backup_.setup();
                $scope.requests.requests.append(temp_request);
            }
            if (data["title"] !== undefined) {
                var temp_title = $factory({ classes: ["Title", "Model", "Backup", "States"], base_class: "Title" });
                temp_title._model_.fromJSON(data["title"]);
                temp_title._backup_.setup();
                $titles.titles.append(temp_title);
            }
        }
        $modals.close();
        $application.newRequest._model_.reset();
    };



    /**
     * Коллбэк при заверщшении редактирования заявки
     * @param data
     */
    $scope.onSuccessEditRequest = function (data) {
        if (data !== undefined) {
            if (data["request"] !== undefined) {
                var temp_request = $factory({ classes: ["Request", "Model", "Backup", "States"], base_class: "Request" });
                temp_request._model_.fromJSON(data["request"]);
                temp_request._backup_.setup();
                $requests.requests.append(temp_request);
            }
            if (data["title"] !== undefined) {
                var temp_title = $factory({ classes: ["Title", "Model", "Backup", "States"], base_class: "Title" });
                temp_title._model_.fromJSON(data["title"]);
                temp_title._backup_.setup();
                $titles.titles.append(temp_title);
            }
            $modals.close();
            $application.newRequest._model_.reset();
        }
    };



    /**
     * Обработчик закрытия модального окна добавления новой заявки
     */
    $scope.cancel = function () {
        $modals.close();
        if ($application.newRequest.id.value !== 0) {
            $requests.deleteRequestHistory($application.newRequestHistory, 0, $scope.onSuccessCancelAddNewRequest);
        }
        $scope.errors.splice(0, $scope.errors.length);
        $application.newRequest._model_.reset();
    };



    /**
     * Коллбэк отмены добавления новой заявки
     * @param data
     */
    $scope.onSuccessCancelAddNewRequest = function (data) {
        $application.newRequestHistory._model_.reset();
        $scope.errors.splice(0, $scope.errors.length);
        $application.newRequest._model_.reset();
        $application.newRequest._states_.changed(false);
    };
}]);





/**
 * NewRequestDetailsController
 * Контроллер отображения информации о новой заявке
 */
requests.controller("NewRequestDetailsController", ["$log", "$scope", "$application", "$titles", "$contractors", "$location", "$requests", function ($log, $scope, $application, $titles, $contractors, $location, $requests) {
        $scope.app = $application;
        $scope.titles = $titles;
        $scope.requests = $requests;
        $scope.contractors = $contractors;
}]);





/**
 * NewRequestDocumentsController
 * Контроллер документов новой заявки
 */
requests.controller("NewRequestDocumentsController", ["$log", "$scope", "$application", "$titles", "$factory", "$session", function ($log, $scope, $application, $titles, $factory, $session) {
    $scope.app = $application;
    $scope.titles = $titles;
    $scope.uploadedDocs = [];



    /**
     * Срабатывает перед отправкой документа-приложения на сервер
     */
    $scope.onBeforeUploadRID = function () {
        $application.currentUploaderData["doc_type"] = "rid";
        $application.currentUploaderData["newRequestId"] = $application.newRequest.id.value;
        $application.currentUploaderData["userId"] = $session.user.get().id.value;
    };



    /**
     * Коллбэк добавления документа-приложения на сервер
     * @param data
     */
    $scope.onCompleteUploadRID = function (data) {
        var temp_request = $factory({classes: ["Request", "Model", "Backup", "States"], base_class: "Request"});
        temp_request._model_.fromJSON(data["request"]);
        $application.newRequest.id.value = temp_request.id.value;
        if (data["rid"] !== undefined) {
            var temp_file = $factory({
                classes: ["RequestAttachment", "FileItem_", "Model", "States"],
                base_class: "RequestAttachment"
            });
            temp_file._model_.fromJSON(data["rid"]);
            $scope.uploadedDocs.push(temp_file);
        }
        if (data["history"] !== undefined)
            $application.newRequestHistory._model_.fromJSON(data["history"]);
        if ($application.currentUploaderData["newRequestId"] !== undefined)
            delete $application.currentUploaderData.newRequestId;
        if ($application.currentUploaderData["doc_type"] !== undefined)
            delete $application.currentUploaderData.doc_type;
        if ($application.currentUploaderData["userId"] !== undefined)
            delete $application.currentUploaderData.userId;
    };
}]);





/**
 * EditRequestStatusModalController
 * Контроллер модального окна изменения статуса заявки
 */
requests.controller("EditRequestStatusModalController", ["$log", "$scope", "$application", "$requests", "$contractors", "$modals", "$factory", "$session", function ($log, $scope, $application, $requests, $contractors, $modals, $factory, $session) {
    $scope.app = $application;
    $scope.requests = $requests;
    $scope.contractors = $contractors;
    $scope.uploadedDocs = [];
    $scope.temp_file = $factory({ classes: ["RequestStatusAttachment", "FileItem_", "Model"], base_class: "RequestStatusAttachment" });
    $scope.temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
    $scope.errors = [];



    /**
     * Обраьотчик изменения статуса заявки
     * @param statusId
     */
    $scope.onChangeStatus = function (statusId) {
        if (statusId !== undefined) {
            if (statusId !== $application.currentRequest.statusId.value)
                $application.currentRequest._states_.changed(true);
            else
                $application.currentRequest._states_.changed(false);
        }
    };



    /**
     * Срабатывает перед загрузкой документа-приложения на сервер
     */
    $scope.onBeforeUploadRSD = function () {
        $application.currentUploaderData["doc_type"] = "rsd";
        $application.currentUploaderData["statusId"] = $application.currentRequest.statusId.value;
        $application.currentUploaderData["userId"] = $session.user.get().id.value;
        $application.currentUploaderData["description"] = $application.newRequestHistory.description.value;
        $application.currentUploaderData["historyId"] = $scope.temp_history.id.value;
        $application.currentRequest._states_.loaded(false);
    };



    /**
     * Коллбэк загрузки документа-приложения на сервер
     * @param data
     */
    $scope.onCompleteUploadRSD = function (data) {
        /* Если новая история изменения заявки не создана */
        if ($scope.temp_history.id.value === 0) {
            $scope.temp_history._model_.fromJSON(data["status"]);

            var history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
            history._model_.fromAnother($scope.temp_history);
            $application.currentRequestHistory.append(history);
        }
        $application.currentRequestHistory.find("id", $scope.temp_history.id.value).description.value = data["status"]["DESCRIPTION"];
        $scope.temp_file._model_.fromJSON(data["rsd"]);
        var file = $factory({ classes: ["RequestStatusAttachment", "FileItem_", "Model"], base_class: "RequestStatusAttachment" });
        file._model_.fromJSON(data["rsd"]);
        $scope.uploadedDocs.push(file);
        $application.currentRequestStatusDocs.append(file);


        if ($application.currentUploaderData.historyId !== undefined)
            delete $application.currentUploaderData.historyId;
        if ($application.currentUploaderData.statusId !== undefined)
            delete $application.currentUploaderData.statusId;
        if ($application.currentUploaderData.userId !== undefined)
            delete $application.currentUploaderData.userId;
        if ($application.currentUploaderData.description !== undefined)
            delete $application.currentUploaderData.description;
        if ($application.currentUploaderData.historyId !== undefined)
            delete $application.currentUploaderData.historyId;
        $application.currentRequest._states_.loaded(true);
    };



    /**
     * Обработчик сохранения изменений статуса заявки
     */
    $scope.save = function () {
        if ($scope.temp_history.id.value === 0) {
            $requests.changeRequestStatus(
                $application.currentRequest.id.value,
                $application.currentRequest.statusId.value,
                $application.newRequestHistory.description.value,
                $scope.onSuccessChangeRequestStatus
            );
        } else {
            $requests.editRequestHistory(
                $scope.temp_history.id.value,
                $application.newRequestHistory.description.value,
                $scope.onSuccessEditRequestStatus
            );
        }


        $scope.uploadedDocs.splice(0, $scope.uploadedDocs.length);
        $scope.temp_history._model_.reset();
        $scope.temp_file._model_.reset();
        $application.newRequestHistory._model_.reset();
        $application.currentRequest._states_.changed(false);
        $modals.close();
    };



    /**
     * Коллбэк редактирования статуса заявки
     * @param data
     */
    $scope.onSuccessEditRequestStatus = function (data) {
        if (data !== undefined) {
            var temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"] });
            temp_history._model_.fromJSON(data);
            $application.currentRequestHistory.find("id", temp_history.id.value).description.value = temp_history.description.value;
            $application.currentRequest.statusId.value = temp_history.statusId.value;
            $application.currentRequest._backup_.setup();
        }
    };



    /**
     * Коллбэк изменения статуса заявки
     * @param data
     */
    $scope.onSuccessChangeRequestStatus = function (data) {
        if (data !== undefined) {
            var temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
            temp_history._model_.fromJSON(data);
            $application.currentRequestHistory.append(temp_history);
            $application.currentRequest._backup_.setup();

            $application.currentRequest.statusId.value = temp_history.statusId.value;
            $application.currentRequest._backup_.setup();

            $application.newRequestHistory._model_.reset();
            $modals.close();
        }
    };



    /**
     * Коллбэк добавления истории изменения статуса заявки
     * @param data
     */
    $scope.onSuccessAddRequestHistory = function (data) {
        if (data !== undefined) {
            var temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
            temp_history._model_.fromJSON(data);
            $application.currentRequestHistory.append(temp_history);
        }
    };



    /**
     * Обработчик закрытия модального окна изменения статуса заявки
     */
    $scope.cancel = function () {
        $modals.close();
        $scope.errors.splice(0, $scope.errors.length);
        $application.currentRequest._backup_.restore();
        $application.currentRequest._states_.changed(false);
        if ($scope.temp_history.id.value !== 0) {
            $requests.deleteRequestHistory($scope.temp_history, $application.currentRequest._backup_.data.statusId);
            $application.currentRequestStatusDocs.delete("id", $scope.temp_file.id.value);
            $application.currentRequestHistory.delete("id", $scope.temp_history.id.value);
        }
        $scope.uploadedDocs.splice(0, $scope.uploadedDocs.length);
        $application.newRequestHistory._model_.reset();
        $scope.temp_file._model_.reset();
        $scope.temp_history._model_.reset();
    };

}]);





/**
 * DeleteRequestModalController
 * Контроллер модального окна удаления заявки
 */
requests.controller("DeleteRequestModalController", ["$log", "$scope", "$users", "$application", "$modals", "$titles", "$requests", function ($log, $scope, $users, $application, $modals, $titles, $requests) {
    $scope.app = $application;



    /**
     * Удаляет текущую заявку
     */
    $scope.delete = function () {
        $titles.deleteRequest($application.currentRequest.id.value, $scope.onSuccessDeleteRequest);
    };



    /**
     * Коллбэк удаления текущей заявки
     * @param data
     */
    $scope.onSuccessDeleteRequest = function (data) {
        $requests.requests.delete("id", $application.currentRequest.id.value);
        $application.currentRequestHistory.clear();
        $application.currentRequestStatusDocs.clear();
        $application.currentRequest = undefined;
        $modals.close();
    };
}]);





/**
 * EditRequestModalController
 * Контроллер модального окна редактирования заявки
 */
requests.controller("EditRequestModalController", ["$log", "$scope", "$misc", "$application", "$modals", "$nodes", "$contractors", "$location", "$titles", "$requests", function ($log, $scope, $misc, $application, $modals, $nodes, $contractors, $location, $titles, $requests) {
    $scope.app = $application;
    $scope.misc = $misc;
    $scope.contractors = $contractors;
    $scope.errors = [];
    $scope.tabs = [
        {
            id: 1,
            title: "Подробности",
            template: "templates/requests/edit-request-details.html",
            isActive: true
        },
        {
            id: 2,
            title: "Приложения",
            template: "templates/requests/edit-request-documents.html",
            isActive: false
        }
    ];



    /**
     * Переход на старницу добавления нового титула
     */
    $scope.gotoNewTitle = function () {
        $modals.close();
        $location.url("/new-title");
    };



    /**
     * Обработчик закрытия модального окна редактирования заявки
     */
    $scope.cancel = function () {
        $modals.close();
        $scope.errors.splice(0, $scope.errors.length);
        $application.currentRequest._backup_.restore();
        $application.currentRequest._states_.changed(false);
    };



    /**
     * Производит валидацию формы редактирования заявки
     */
    $scope.validate = function () {
        $scope.errors.splice(0, $scope.errors.length);
        if ($application.currentRequest.title.value === "")
            $scope.errors.push("Вы не указали наименование объекта");
        if ($application.currentRequest.description.value === "")
            $scope.errors.push("Вы не указали общую информацию об объекте");
        if ($application.currentRequest.buildingPlanDate.value === "" || $application.currentRequest.buildingPlanDate.value === 0)
            $scope.errors.push("Вы не указали планируемую дату строительства и ввода в эксплуатацию объекта");
        if ($scope.errors.length === 0) {
            $requests.editRequest($application.currentRequest, $scope.onSuccessEditRequest);
        }
    };



    /**
     * Коллбэк редактирования заявки
     * @param data
     */
    $scope.onSuccessEditRequest = function (data) {
        $application.currentRequest._states_.loaded(true);
        $application.currentRequest._states_.loading(false);
        $application.currentRequest._states_.changed(false);
        $application.currentRequest._backup_.setup();
        $modals.close();
    };
}]);





/**
 * EditRequestsDetailsController
 * Контроллер редактирования информации о заявке
 */
requests.controller("EditRequestDetailsController", ["$log", "$scope", "$application", "$titles", "$contractors", function ($log, $scope, $application, $titles, $contractors) {
        $scope.app = $application;
        $scope.titles = $titles;
        $scope.contractors = $contractors;
}]);





/**
 * EditRequestDocumentsController
 * Контроллер списка документо заявки
 */
requests.controller("EditRequestDocumentsController", ["$log", "$scope", "$application", "$titles", "$factory", "$session", "$requests", function ($log, $scope, $application, $titles, $factory, $session, $requests) {
    $scope.app = $application;
    $scope.titles = $titles;
    $scope.requests = $requests;



    /**
     * Обработчик удаления документа
     * @param rsdId {number} - Идентификатор документа
     */
    $scope.deleteRsd = function (rsdId) {
        if (rsdId !== undefined) {
            $requests.deleteRequestStatusDoc(rsdId, function () {
                $application.currentRequest.inputDocs.delete("id", rsdId);
            });
        }
    };



    /**
     * Срабатывает перед отправкой документа-приложения к заявке на сервер
     */
    $scope.onBeforeUploadRID = function () {
        $application.currentUploaderData["doc_type"] = "rid";
        $application.currentUploaderData["userId"] = $session.user.get().id.value;
        $application.currentUploaderData["newRequestId"] = $application.currentRequest.id.value;
    };



    /**
     * Коллбэк отправки документа-приложения к заявке на сервер
     * @param data
     */
    $scope.onCompleteUploadRID = function (data) {
        if (data["rid"] !== undefined) {
            var temp_rid = $factory({
                classes: ["RequestStatusAttachment", "FileItem_", "Model", "Backup", "States"],
                base_class: "RequestStatusAttachment"
            });
            temp_rid._model_.fromJSON(data["rid"]);
            $application.currentRequest.inputDocs.append(temp_rid);
            $application.currentRequestStatusDocs.append(temp_rid);
        }
        if ($application.currentUploaderData["doc_type"] !== undefined)
            delete $application.currentUploaderData.doc_type;
        if ($application.currentUploaderData["userId"] !== undefined)
            delete $application.currentUploaderData.userId;
    };
}]);





/**
 * EditRequestStatusModalController
 * Контроллер модфльногоокна изменения статуса заявки
 */
requests.controller("EditRequestStatusModalController", ["$log", "$scope", "$application", "$titles", "$contractors", "$modals", "$factory", "$session", "$requests", function ($log, $scope, $application, $titles, $contractors, $modals, $factory, $session, $requests) {
    $scope.app = $application;
    $scope.titles = $titles;
    $scope.contractors = $contractors;
    $scope.uploadedDocs = [];
    $scope.temp_file = $factory({ classes: ["RequestStatusAttachment", "FileItem_", "Model"], base_class: "RequestStatusAttachment" });
    $scope.temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
    $scope.errors = [];



    /**
     * Срабатывает при изменении статуса заявки
     * @param statusId {number} - Идентификатор статуса
     */
    $scope.onChangeStatus = function (statusId) {
        if (statusId !== undefined) {
            if (statusId !== $application.currentRequest.statusId.value)
                $application.currentRequest._states_.changed(true);
            else
                $application.currentRequest._states_.changed(false);
        }
    };



    /**
     * Срабатывает перед загрузкой документа-приложения к статусу на сервер
     */
    $scope.onBeforeUploadRSD = function () {
        $application.currentUploaderData["doc_type"] = "rsd";
        $application.currentUploaderData["statusId"] = $application.currentRequest.statusId.value;
        $application.currentUploaderData["userId"] = $session.user.get().id.value;
        $application.currentUploaderData["description"] = $application.newRequestHistory.description.value;
        $application.currentUploaderData["historyId"] = $scope.temp_history.id.value;
        $application.currentRequest._states_.loaded(false);
    };



    /**
     * Коллбэк при загрузке документа-приложения к статусу на сервер
     * @param data
     */
    $scope.onCompleteUploadRSD = function (data) {
        /* Если новая история изменения заявки не создана */
        if ($scope.temp_history.id.value === 0) {
            $scope.temp_history._model_.fromJSON(data["status"]);

            var history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
            history._model_.fromAnother($scope.temp_history);
            $application.currentRequestHistory.append(history);
        }
        $application.currentRequestHistory.find("id", $scope.temp_history.id.value).description.value = data["status"]["DESCRIPTION"];
        $scope.temp_file._model_.fromJSON(data["rsd"]);
        var file = $factory({ classes: ["RequestStatusAttachment", "FileItem_", "Model"], base_class: "RequestStatusAttachment" });
        file._model_.fromJSON(data["rsd"]);
        $scope.uploadedDocs.push(file);
        $application.currentRequestStatusDocs.append(file);


        if ($application.currentUploaderData.historyId !== undefined)
            delete $application.currentUploaderData.historyId;
        if ($application.currentUploaderData.statusId !== undefined)
            delete $application.currentUploaderData.statusId;
        if ($application.currentUploaderData.userId !== undefined)
            delete $application.currentUploaderData.userId;
        if ($application.currentUploaderData.description !== undefined)
            delete $application.currentUploaderData.description;
        if ($application.currentUploaderData.historyId !== undefined)
            delete $application.currentUploaderData.historyId;
        $application.currentRequest._states_.loaded(true);
    };



    /**
     * Обрботчик сохранения статуса заявки
     */
    $scope.save = function () {
        if ($scope.temp_history.id.value === 0) {
            $requests.changeRequestStatus(
                $application.currentRequest.id.value,
                $application.currentRequest.statusId.value,
                $application.newRequestHistory.description.value,
                $scope.onSuccessChangeRequestStatus
            );
        } else {
            $requests.editRequestHistory(
                $scope.temp_history.id.value,
                $application.newRequestHistory.description.value,
                $scope.onSuccessEditRequestStatus
            );
        }

        $scope.uploadedDocs.splice(0, $scope.uploadedDocs.length);
        $scope.temp_history._model_.reset();
        $scope.temp_file._model_.reset();
        $application.newRequestHistory._model_.reset();
        $application.currentRequest._states_.changed(false);
        $modals.close();
    };



    /**
     * Срабатывает при успешном изменении статуса заявки
     * @param data
     */
    $scope.onSuccessEditRequestStatus = function (data) {
        if (data !== undefined) {
            var temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"] });
            temp_history._model_.fromJSON(data);
            $application.currentRequestHistory.find("id", temp_history.id.value).description.value = temp_history.description.value;
            $application.currentRequest.statusId.value = temp_history.statusId.value;
            $application.currentRequest._backup_.setup();
        }
    };



    /**
     * Срабатывает при успешном изменении статуса заявки
     * @param data
     */
    $scope.onSuccessChangeRequestStatus = function (data) {
        if (data !== undefined) {
            var temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
            temp_history._model_.fromJSON(data);
            $application.currentRequestHistory.append(temp_history);
            $application.currentRequest._backup_.setup();

            $application.currentRequest.statusId.value = temp_history.statusId.value;
            $application.currentRequest._backup_.setup();

            $application.newRequestHistory._model_.reset();
            $modals.close();
        }
    };



    /**
     * Срабатывает при успешном добавлении статуса заявки
     * @param data
     */
    $scope.onSuccessAddRequestHistory = function (data) {
        if (data !== undefined) {
            var temp_history = $factory({ classes: ["RequestHistory", "Model", "Backup", "States"], base_class: "RequestHistory" });
            temp_history._model_.fromJSON(data);
            $application.currentRequestHistory.append(temp_history);
        }
    };



    /**
     * Обработчик закрытия модального окна с редактированием статуса заявки
     */
    $scope.cancel = function () {
        $modals.close();
        $scope.errors.splice(0, $scope.errors.length);
        $application.currentRequest._backup_.restore();
        $application.currentRequest._states_.changed(false);
        if ($scope.temp_history.id.value !== 0) {
            $requests.deleteRequestHistory($scope.temp_history, $application.currentRequest._backup_.data.statusId);
            $application.currentRequestStatusDocs.delete("id", $scope.temp_file.id.value);
            $application.currentRequestHistory.delete("id", $scope.temp_history.id.value);
        }
        $scope.uploadedDocs.splice(0, $scope.uploadedDocs.length);
        $application.newRequestHistory._model_.reset();
        $scope.temp_file._model_.reset();
        $scope.temp_history._model_.reset();
    };
}]);


