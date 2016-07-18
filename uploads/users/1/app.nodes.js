"use strict";


var nodes = angular.module("gears.app.nodes", [])
    .config(function ($provide) {

        /**
         * $nodes
         * Сервис, содержащий функционал для работы с узлами
         */
        $provide.factory("$nodes", ["$log", "$http", "$factory", "$menu", function ($log, $http, $factory, $menu) {
            var nodes = {};


            /**
             * Наборы свойст и методов, описывающих модели данных
             */
            nodes.classes = {
                /**
                 * NodeType
                 * Набор свойств, описывающих тип узла
                 */
                NodeType: {
                    id: new Field({ source: "ID", value: 0 }),
                    title: new Field({ source: "TITLE", value: "" }),
                    isBasicNode: new Field({ source: "IS_BASE_OBJECT", value: false }),
                    isAllowConnection: new Field({ source: "IS_ALLOW_CONNECTION", value: false }),

                    onInitModel: function () {
                        this.isBasicNode.value = this.isBasicNode.value === 1 ? true : false;
                        this.isAllowConnection.value = this.isAllowConnection.value === 1 ? true : false;

                    }
                },

                /**
                 * UnknownNode
                 * Набор свойст, описывающих неформализованный узел
                 */
                UnknownNode: {
                    id: new Field({ source: "NODE_ID", value: 0, default_value: 0 }),
                    nodeTypeId: new Field({ source: "OBJECT_TYPE_ID", value: 0, default_value: 0, backupable: true, required: true }),
                    pointId: new Field({ source: "POINT_ID", value: 0, default_value: 0, backupable: true, required: true }),
                    titleId: new Field({ source: "TITULE_ID", value: 0, default_value: 0, backupable: true, required: true }),
                    titlePartId: new Field({ source: "TITULE_PART_ID", value: 0, default_value: 0, backupable: true, required: true }),
                    branchesCount: new Field({ source: "OUT_PATHS", value: 0, default_value: 0 }),
                    latitude: new Field({ source: "LATITUDE", value: 0, default_value: 0, backupable: true, raw: true }),
                    longitude: new Field({ source: "LONGITUDE", value: 0, default_value: 0, backupable: true, raw: true })
                },

                /**
                 * Pylon
                 * Набор свойст, описывающих опору
                 */
                Pylon: {
                    id: new Field({ source: "NODE_ID", value: 0, default_value: 0, type: "integer" }),
                    nodeTypeId: new Field({ source: "OBJECT_TYPE_ID", value: 1, default_value: 1, backupable: true, required: true, type: "integer" }),
                    pointId: new Field({ source: "POINT_ID", value: 0, default_value: 0, backupable: true, required: true, type: "integer" }),
                    pylonTypeId: new Field({ source: "PYLON_TYPE_ID", value: 0, default_value: 0, backupable: true, required: true, type: "integer" }),
                    pylonSchemeTypeId: new Field({ source: "PYLON_SCHEME_TYPE_ID", value: 0, default_value: 0, backupable: true, type: "integer" }),
                    powerLineId: new Field({ source: "POWER_LINE_ID", value: 0, default_value: 0, backupable: true, required: true, type: "integer" }),
                    number: new Field({ source: "PYLON_NUMBER", value: 0, default_value: 0, backupable: true, required: true, type: "integer" }),
                    branchesCount: new Field({ source: "OUT_PATHS", value: 0, default_value: 0, type: "integer" }),
                    display: "",
                    search: "",
                    latitude: new Field({ source: "LATITUDE", value: 0, default_value: 0, backupable: true, type: "float" }),
                    longitude: new Field({ source: "LONGITUDE", value: 0, default_value: 0, backupable: true, type: "float" }),
                    position: new Field({ source: "POSITION", value: 0, default_value: 0, backupable: true, type: "integer" }),

                    _init_: function () {
                        this.display = "#" + this.number.value;
                        this.search = "#" + this.number.value.toString();
                        this.latitude
                    }
                },

                /**
                 * PowerStation
                 * Набор свойств, описывающих электростанцию
                 */
                PowerStation: {
                    id: new Field({ source: "OBJECT_ID", value: 0, default_value: 0 }),
                    nodeTypeId: new Field({ source: "OBJECT_TYPE_ID", value: 0, default_value: 0, backupable: true, required: true }),
                    title: new Field({ source: "TITLE", value: "", default_value: "", backupable: true, required: true }),
                    voltage: new Field({ source: "VOLTAGE", value: 0, default_value: 0, backupable: true, required: true }),
                    position: new Field({ source: "POSITION", value: 0, default_value: 0, backupable: true, type: "integer" })
                },

                /**
                 * Anchor
                 * Набор свойств и методов, описывающих крепление
                 */
                Anchor: {
                    id: new Field({ source: "ID", value: 0, default_value: 0 }),
                    nodeTypeId: new Field({ source: "OBJECT_TYPE_ID", value: 0, default_value: 0, backupable: true, required: true }),
                    anchorTypeId: new Field({ source: "ANCHOR_TYPE_ID", value: 0, default_value: 0, backupable: true }),
                    nodeId: new Field({ source: "BASE_NODE_ID", value: 0, default_value: 0, backupable: true })
                },

                /**
                 * Union
                 * Набор свойств и методов, описывающих муфту
                 */
                Union: {
                    id: new Field({ source: "ID", value: 0, default_value: 0, type: "integer", backupable: true }),
                    nodeTypeId: new Field({ source: "OBJECT_TYPE_ID", value: 0, default_value: 0, type: "integer", backupable: true }),
                    unionTypeId: new Field({ source: "UNION_TYPE_ID", value: 0, default_value: 0, type: "integer", backupable: true }),
                    number: new Field({ source: "UNION_NUMBER", value: "", default_value: "", type: "string", backupable: true }),
                    nodeId: new Field({ source: "BASE_NODE_ID", value: 0, default_value: 0, type: "integer", backupable: true })
                }
            };


            /**
             * Описание раздела меню
             */
            nodes.types = $factory({ classes: ["Collection", "States"], base_class: "Collection" });


            /**
             * Получает список всех типов узлов
             */
            nodes.getNodeTypes = function () {
                $http.post("serverside/controllers/nodes.php", { action: "getNodeTypes" })
                    .success(function (data) {
                        if (data !== undefined) {
                            if (data["error_code"] !== undefined) {
                                var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                db_error.init(data);
                                db_error.display();
                            } else {
                                angular.forEach(data, function (type) {
                                    var temp_type = $factory({ classes: ["NodeType", "Model"], base_class: "NodeType" });
                                    temp_type._model_.fromJSON(type);
                                    nodes.types.append(temp_type);
                                });
                            }
                        }
                    }
                );
            };


            /**
             * Получает список опор по идентификатору линии
             * @param powerLineId {number} - Идентификатор линии
             * @param callback {Function} - Коллбэк
             */
            nodes.getPylonsByPowerLineId = function (powerLineId, callback) {
                if (powerLineId !== undefined) {
                    var params = {
                        action: "getPylonsByPowerLineId",
                        data: {
                            powerLineId: powerLineId
                        }
                    };
                    $http.post("serverside/controllers/nodes.php", params)
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
             * Парсит узел
             * @param node {array} - Массив с raw-описанием узла
             * @returns {boolean} - true - в случае успеха, false - в обратном случае
             */
            nodes.parseNode = function (node) {
                var result = false;
                if (node !== undefined) {
                    if (node["OBJECT_TYPE_ID"] !== undefined) {
                        var node_type_id = parseInt(node["OBJECT_TYPE_ID"]);
                        switch (node_type_id) {
                            case 0:
                                result = $factory({ classes: ["UnknownNode", "Model", "Backup", "States"], base_class: "UnknownNode" });
                                result._model_.fromJSON(node);
                                result._backup_.setup();
                                break;
                            case 1:
                                result = $factory({ classes: ["Pylon", "Model", "Backup", "States"], base_class: "Pylon" });
                                result._model_.fromJSON(node);
                                result._backup_.setup();
                                break;
                            case 2:
                                result = $factory({ classes: ["Anchor", "Model", "Backup", "States"], base_class: "Anchor" });
                                result._model_.fromJSON(node);
                                result._backup_.setup();
                                break;
                            case 3:
                                result = $factory({ classes: ["Union", "Model", "Backup", "States"], base_class: "Union" });
                                result._model_.fromJSON(node);
                                result._backup_.setup();
                                break;
                        }
                        if (node["PATH_ID"] !== undefined && node["PATH_ID"] !== null)
                            result.branchId = parseInt(node["PATH_ID"]);
                        else
                            result.branchId = -1;
                    }
                }
                return result;
            };


            /**
             * Получает ответвления от узла
             * @param titleId {number} - Идентификатор титула
             * @param titlePartId {number} - Идентификатор участка титула
             * @param nodeId {number} - Идентификатор узла
             * @param callback {function} - Коллбэк
             */
            nodes.getBranches = function (titleId, titlePartId, nodeId, callback) {
                if (titleId !== undefined && titlePartId !== undefined && nodeId !== undefined) {
                    var params = {
                        action: "getBranches",
                        data: {
                            titleId: titleId,
                            titlePartId: titlePartId,
                            nodeId: nodeId,
                            sessionId: "test"
                        }
                    };
                    $http.post("serverside/controllers/nodes.php", params)
                        .success(function (data) {
                            if (data !== undefined) {
                                if (data["error_code"] !== undefined) {
                                    var db_error = $factory({ classes: ["DBError"], base_class: "DBError" });
                                    db_error.init(data);
                                    db_error.display();
                                } else {
                                    if (callback !== undefined)
                                        callback(nodeId, data);
                                }
                            }
                        }
                    );
                }
            };


            /**
             * Добавляет узел
             * @param node {object} - Узел, который требуется добавить
             * @param callback {function} - Коллбэк
             */
            nodes.addNode = function (node, callback) {
                if (node !== undefined) {
                    var params = {
                        action: "addNode",
                        data: {
                            nodeTypeId: node.nodeTypeId.value,
                            number: node.number.value,
                            powerLineId: node.powerLineId.value,
                            pylonTypeId: node.pylonTypeId.value,
                            latitude: node.latitude.value,
                            longitude: node.longitude.value
                        }
                    };
                    $http.post("serverside/controllers/nodes.php", params)
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
             * Редактирует узел
             * @param node {object} - Узел, который требуется изменить
             * @param callback {function} -Коллбэк
             */
            nodes.editNode = function (node, callback) {
                if (node !== undefined) {
                    var params = {
                        action: "editNode",
                        data: {
                            nodeId: node.id.value,
                            nodeTypeId: node.nodeTypeId.value,
                            number: node.__class__ === "Pylon" ? node.number.value : undefined,
                            powerLineId: node.__class__ === "Pylon" ? node.powerLineId.value : undefined,
                            pylonTypeId: node.__class__ === "Pylon" ? node.pylonTypeId.value : undefined,
                            latitude: node.latitude.value,
                            longitude: node.longitude.value
                        }
                    };
                    $http.post("serverside/controllers/nodes.php", params)
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
             * Получает список узлов-коннекторов по идентификатору базового узла
             * @param baseNodeId {number} - Идентификатор базового узла
             * @param callback {function} - Коллбэк
             */
            nodes.getConnectionNodesByBaseNodeId = function (baseNodeId, callback) {
                if (baseNodeId !== undefined) {
                    var params = {
                        action: "getConnectionNodesByBaseNodeId",
                        data: {
                            baseNodeId: baseNodeId
                        }
                    };
                    $http.post("serverside/controllers/nodes.php", params)
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
             * Добавляет узел-коннектор к базовому узлу
             * @param connectionNode {object} - Узел-коннектор, который требуется добавить
             * @param basenodeId {number} - Идентификатор базового узла
             * @param callback {function} - Коллбэк
             */
            nodes.addConnectionNode = function (connectionNode, nodeTypeId, baseNodeId, callback) {
                if (connectionNode !== undefined && nodeTypeId !== undefined && baseNodeId !== undefined) {
                    var params = {
                        action: "addConnectionNode",
                        data: {
                            baseNodeId: baseNodeId,
                            nodeTypeId: nodeTypeId,
                            anchorTypeId: nodeTypeId === 2 ? connectionNode.anchorTypeId.value : -1,
                            unionTypeId: nodeTypeId === 3 ? connectionNode.unionTypeId.value : -1
                        }
                    };
                    $http.post("serverside/controllers/nodes.php", params)
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
             * Удаляет узел-коннектор к базовому узлу
             * @param connectionNode {object} - Узел, который требуется удалить
             * @param callback {function} - Коллбэк
             */
            nodes.deleteConnectionNode = function (connectionNode, callback) {
                if (connectionNode !== undefined) {
                    var params = {
                        action: "deleteConnectionNode",
                        data: {
                            connectionNodeId: connectionNode.id.value
                        }
                    };
                    $http.post("serverside/controllers/nodes.php", params)
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

            return nodes;
        }]);
    })
    .run(function ($modules, $nodes) {
        $modules.load($nodes);
    }
);