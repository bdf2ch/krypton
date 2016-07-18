"use strict";


/* Константы кодов ошибок на бэкенде */
const ERROR_TYPE_DEFAULT = 1;
const ERROR_TYPE_ENGINE = 2;
const ERROR_TYPE_DATABASE = 3;
const ERROR_TYPE_LDAP = 4;

/* Константы типов данных в моделях на бэкенде */
const DATA_TYPE_INTEGER = 1;
const DATA_TYPE_STRING = 2;
const DATA_TYPE_FLOAT = 3;
const DATA_TYPE_BOOLEAN = 4;



/**
 * Класс поля модели данных
 * @param parameters {Object} - Параметры инициализации создаваемого объекта
 * @constructor
 */
function Field (parameters) {
    var isChanged = false;
    var beforeChange = "";

    this.source = "";           // Наименование поля-источника данных в БД
    this.value = undefined;     // Значение поля
    this.default_value = "";    // Значение поля по умолчанию
    this.backupable = false;    // Флаг, требуется ли резервировать значение поля
    this.displayable = true;    // Флаг, требуется ли отображать поле
    this.required = false;      // Флаг, является ли поле обязательным для заполнения
    this.title = "";
    this.isValid = false;
    this.raw = false;
    this.type = undefined;
    
    this._fromAnother_ = function (parameters) {
        if (parameters == undefined || parameters === null) {
            $errors.add(ERROR_TYPE_DEFAULT, "Field -> fromAnother: Не задан параметр - параметры инициализации");
            return false;
        } else {
            for (var param in parameters) {
                if (this.hasOwnProperty(param))
                    this[param] = parameters[param];
            }
        }
    };

    this._change_ = function (flag) {
        if (flag !== undefined && typeof flag === "boolean")
            isChanged = flag;
        return isChanged;
    };

    this._backup_ = function (value) {
        if (value !== undefined)
            beforeChange = value;
        else {
            switch (this.type) {
                case DATA_TYPE_STRING:
                    this.value = beforeChange.toString();
                    break;
                case DATA_TYPE_INTEGER:
                    this.value = parseInt(beforeChange);
                    break;
                case DATA_TYPE_FLOAT:
                    this.value =+ parseFloat(beforeChange).toFixed(6);
                    break;
                case DATA_TYPE_BOOLEAN:
                    this.value = new Boolean(beforeChange);
                    break;
                default:
                    this.value = beforeChange;
                    break;
            }
            this._change_(false);
        }
    };

    if (parameters !== undefined) {
        for (var param in parameters) {
            if (this.hasOwnProperty(param)) {
                this[param] = parameters[param];
            }
        }
        beforeChange = this.value;
    }
};



function isField (obj) {
    if (obj === undefined && obj === null) {
        $errors.add(ERROR_TYPE_DEFAULT, "isField: Не задан параметр - объект для проверки на экзмпляр класса Field");
        return false;
    } else {
        if (typeof obj === "object") {
            if (obj.hasOwnProperty("type") && obj.hasOwnProperty("source") && obj.hasOwnProperty("value")) {
                return true;
            } else
                return false;
        } else
            return false;
    }
};



(function () {
    angular
        .module("krypton", [])
        .factory("$classes", classesFactory)
        .factory("$factory", factoryFactory)
        .factory("$errors", errorsFactory)
        .factory("$settings", settingsFactory)
        .factory("$extensions", extensionsFactory)
        .factory("$session", sessionFactory)
        .factory("$users", usersFactory)
        .factory("$application", applicationFactory)
        .directive("uploader", uploaderDirective)
        .filter("unique", uniqueFilter)
        .config(function ($provide, $routeProvider) {


            /*******************
             * $navigation
             * Сервис управления меню
             *******************/
            $provide.factory("$navigation", function ($log, $classes, $rootScope, $errors) {

                /**
                 * Menu
                 * набор свойств и методов, описывающих пункт меню
                 */
                $classes.add("Menu", {
                    __dependencies__: [],
                    id: "",
                    parentId: "",
                    url: "",
                    title: "",
                    description: "",
                    icon: "",
                    templateUrl: "",
                    controller: "",
                    isActive: false,
                    isDefault: false,


                    init: function (parameters) {
                        if (parameters !== undefined) {
                            for (var param in parameters) {
                                if (this.hasOwnProperty(param))
                                    this[param] = parameters[param];
                            }
                            return this;
                        } else
                            return $errors.add(ERROR_TYPE_DEFAULT, "$classes -> Menu -> init: Не задан параметр - параметры инициализации");
                    }
                });

                $rootScope.$on("$routeChangeSuccess", function (prev, next) {
                    var length = items.length;
                    for (var i = 0; i < length; i++) {
                        if (items[i].url === next.$$route.originalPath) {
                            items[i].isActive = true;
                            currentMenuItem = items[i];
                        } else
                            items[i].isActive = false;
                    }
                });

                var items = [];
                var currentMenuItem = undefined;

                return {
                    init: function () {
                        if (window.krypton !== null && window.krypton !== undefined) {
                            if (window.krypton.admin !== null && window.krypton.admin !== undefined) {

                            }
                        }
                    },

                    getAll: function () {
                        return items;
                    },

                    getCurrent: function () {
                        return currentMenuItem;
                    },

                    /**
                     * Добавляет пункт меню
                     * @param menuItem {FactoryObject} - Добавляемый пункт меню
                     * @returns {*}
                     */
                    add: function (menuItem) {
                        if (menuItem !== undefined) {
                            if (menuItem.__class__ !== undefined && menuItem.__class__ === "Menu") {
                                $routeProvider
                                    .when(menuItem.url, {
                                        templateUrl: menuItem.templateUrl,
                                        controller: menuItem.controller
                                    });
                                if (menuItem.isDefault === true) {
                                    $routeProvider
                                        .when("/", {
                                            templateUrl: menuItem.templateUrl,
                                            controller: menuItem.controller
                                        });
                                    currentMenuItem = menuItem;
                                }
                                items.push(menuItem);
                                return menuItem;
                            } else
                                $errors.add(ERROR_TYPE_DEFAULT, "$navigation -> add: Параметр не является экземпляром класса Menu");
                        } else
                            return $errors.add(ERROR_TYPE_DEFAULT, "$navigation -> add: Не задан параметр - пункт меню, который требуется добавить");
                    }
                }
            });

        })
        .run(kryptonRun);




    /******************************
     * $classes
     * Сервис классов приложения
     ******************************/
    function classesFactory ($log) {
        var items = {};

        /**
         * Добавляет класс в стек классов
         * @param className - Наименование класса
         * @param classDefinition - Определение класса
         * @returns {boolean}
         */
        var add = function (className, classDefinition) {
            if (className !== undefined && classDefinition !== undefined && typeof(classDefinition) == "object") {
                items[className] = classDefinition;
                $log.info("Класс " + className + " добавлен в стек классов");
                return true;
            } else
                return false;
        };



        /********************
         * Model
         * Набор свойст и методов, описывающих модель данных
         ********************/
        add("Model", {
            __dependencies__: [],
            _model_: {
                __instance__: "",
                errors: [],
                db_table: "",

                /**
                 * Производит инициализацию модели данных на основе JSON-данных
                 * @param JSONdata {JSON} - Набор JSON-данных
                 * @returns {number} - Возвращает количество полей, проинициализированных из JSON-данных
                 */
                fromJSON: function (JSONdata) {
                    var result = 0;
                    for (var data in JSONdata) {

                        for (var prop in this.__instance__) {

                            if (this.__instance__[prop].constructor === Field && this.__instance__[prop].source === data) {

                                if (JSONdata[data] !== "") {

                                    if (this.__instance__[prop].type !== undefined) {
                                        switch (this.__instance__[prop].type) {
                                            case DATA_TYPE_STRING:
                                                this.__instance__[prop].value = JSONdata[data].toString();
                                                this.__instance__[prop]._backup_(JSONdata[data].toString());
                                                break;
                                            case DATA_TYPE_INTEGER:
                                                if (!isNaN(JSONdata[data])) {
                                                    this.__instance__[prop].value = parseInt(JSONdata[data]);
                                                    this.__instance__[prop]._backup_(parseInt(JSONdata[data]));
                                                } else {
                                                    $log.error("$classes [Model]: Значение поля '" + data + "' в наборе JSON-данных не является числовым значением, свойству объекта присвоен 0");
                                                    this.__instance__[prop].value = 0;
                                                    this.__instance__[prop]._backup_(0);
                                                }
                                                break;
                                            case DATA_TYPE_FLOAT:
                                                if (!isNaN(JSONdata[data])) {
                                                    this.__instance__[prop].value = +parseFloat(JSONdata[data]).toFixed(6);
                                                    this.__instance__[prop]._backup_(+parseFloat(JSONdata[data]).toFixed(6));
                                                } else {
                                                    $log.error("$classes [Model]: Значение поля '" + data + "' в наборе JSON-данных не является числовым значением, свойству объекта присвоен 0");
                                                    this.__instance__[prop].value = 0.0;
                                                    this.__instance__[prop]._backup_(0.0);
                                                }
                                                break;
                                            case DATA_TYPE_BOOLEAN:
                                                if (!isNaN(JSONdata[data])) {
                                                    var value = parseInt(JSONdata[data]);
                                                    if (value === 1 || value === 0) {
                                                        this.__instance__[prop].value = value === 1 ? true : false;
                                                        this.__instance__[prop]._backup_(this.__instance__[prop].value);
                                                    } else {
                                                        $log.error("$classes [Model]: Значение поля '" + data + "' в наборе JSON-данных не является интрепретируемым в логическое значение, свойству объекта присвоен false");
                                                        this.__instance__[prop].value = false;
                                                        this.__instance__[prop]._backup_(false);
                                                    }
                                                } else {
                                                    var value = JSONdata[data].toString().toLowerCase();
                                                    if (value === "true" || value === "false") {
                                                        this.__instance__[prop].value = value === "true" ? true : false;
                                                        this.__instance__[prop]._backup_(this.__instance__[prop].value);
                                                    } else {
                                                        $log.error("$classes [Model]: Значение поля '" + data + "' в наборе JSON-данных не является интрепретируемым в логическое значение, свойству объекта присвоен false");
                                                        this.__instance__[prop].value = false;
                                                        this.__instance__[prop]._backup_(false);
                                                    }
                                                }
                                                break;
                                        }
                                    } else {
                                        /* Для совместимости */
                                        if (isNaN(JSONdata[data]) === false) {
                                            if (JSONdata[data] !== null) {

                                                if (JSONdata[data].constructor === Boolean) {
                                                    this.__instance__[prop].value = JSONdata[data];
                                                } else
                                                    this.__instance__[prop].value = parseInt(JSONdata[data]);

                                            }
                                        } else {
                                            this.__instance__[prop].value = JSONdata[data];
                                        }
                                    }
                                } else
                                    this.__instance__[prop].value = "";

                                result++;
                            }

                        }

                    }
                    if (this.__instance__["onInitModel"] !== undefined) {
                        if (this.__instance__["onInitModel"].constructor === Function) {
                            this.__instance__.onInitModel();
                        }
                    }
                    return result;
                },


                /**
                 * Производит инициализацию модели данных на основе другого объекта, копируя значения совпадающих свойств
                 * @param obj {object} - Объект, на основе которого требуется произвести инициализацию
                 */
                fromAnother: function (obj) {
                    if (obj === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "Model -> fromAnother: Не задан параметр - объект, на основе которого трекуется провести инициализацию");
                        return false;
                    } else {

                        for (var anotherProp in obj) {
                            if (obj[anotherProp] === null) {
                                $log.info("Model -> fromAnother: Отсутствует значение (null) свойста '" + anotherProp + "' объекта, на основе которого требуется провести инициализаццию");
                                return false;
                            } else {

                                if (this.__instance__.hasOwnProperty(anotherProp)) {
                                    if (isField(this.__instance__[anotherProp])) {
                                        if (this.__instance__[anotherProp].type !== undefined) {
                                            switch (this.__instance__[anotherProp].type) {
                                                case DATA_TYPE_STRING:
                                                    if (isField(obj[anotherProp])) {
                                                        this.__instance__[anotherProp]._fromAnother_(obj[anotherProp]);
                                                        this.__instance__[anotherProp]._backup_(obj[anotherProp].value.toString());
                                                    } else {
                                                        this.__instance__[anotherProp].value = obj[anotherProp].toString();
                                                        this.__instance__[anotherProp]._backup_(obj[anotherProp].toString());
                                                    }
                                                    break;
                                                case DATA_TYPE_INTEGER:
                                                    var value = isField(obj[anotherProp]) ? obj[anotherProp].value : obj[anotherProp];


                                                    if (!isNaN(value)) {
                                                        if (isField(obj[anotherProp])) {
                                                            this.__instance__[anotherProp]._fromAnother_(obj[anotherProp]);
                                                            this.__instance__[anotherProp]._backup_(parseInt(obj[anotherProp].value));
                                                        } else {
                                                            this.__instance__[anotherProp].value = parseInt(obj[anotherProp]);
                                                            this.__instance__[anotherProp]._backup_(parseInt(obj[anotherProp]));
                                                        }
                                                    } else {
                                                        $log.info("$classes -> Model: Значение поля '" + anotherProp + "' в наборе JSON-данных не является числовым значением, свойству объекта присвоен 0");
                                                        this.__instance__[anotherProp].value = 0;
                                                        this.__instance__[anotherProp]._backup_(0);
                                                    }
                                                    break;
                                                case DATA_TYPE_FLOAT:
                                                    if (!isNaN(obj[anotherProp])) {
                                                        if (isField(obj[fromAnother()])) {
                                                            this.__instance__[anotherProp]._fromAnother_(obj[anotherProp]);
                                                            this.__instance__[anotherProp]._backup_(+parseFloat(obj[anotherProp].value).toFixed(6));
                                                        } else {
                                                            this.__instance__[anotherProp].value = +parseFloat(obj[anotherProp]).toFixed(6);
                                                            this.__instance__[anotherProp]._backup_(+parseFloat(obj[anotherProp]).toFixed(6));
                                                        }
                                                    } else {
                                                        $log.info("$classes -> Model: Значение поля '" + anotherProp + "' в наборе JSON-данных не является числовым значением, свойству объекта присвоен 0");
                                                        this.__instance__[anotherProp].value = 0.0;
                                                        this.__instance__[anotherProp]._backup_(0.0);
                                                    }
                                                    break;
                                                case DATA_TYPE_BOOLEAN:
                                                    if (!isNaN(obj[anotherProp])) {
                                                        var value = isField(obj[anotherProp]) ? parseInt(obj[anotherProp].value) : parseInt(obj[anotherProp]);
                                                        if (value === 1 || value === 0) {
                                                            this.__instance__[anotherProp].value = value === 1 ? true : false;
                                                            this.__instance__[anotherProp]._backup_(this.__instance__[anotherProp].value);
                                                        } else {
                                                            $log.info("$classes -> Model: Значение поля '" + anotherProp + "' в наборе JSON-данных не является интрепретируемым в логическое значение, свойству объекта присвоен false");
                                                            this.__instance__[anotherProp].value = false;
                                                            this.__instance__[anotherProp]._backup_(false);
                                                        }
                                                    } else {
                                                        var value = isField(obj[anotherProp]) ? obj[anotherProp].value.toString().toLowerCase() : obj[anotherProp].toString().toLowerCase();

                                                        if (value === "true" || value === "false") {
                                                            this.__instance__[anotherProp].value = value === "true" ? true : false;
                                                            this.__instance__[anotherProp]._backup_(this.__instance__[anotherProp].value);
                                                        } else {
                                                            $log.info("$classes -> Model: Значение поля '" + anotherProp + "' в наборе JSON-данных не является интрепретируемым в логическое значение, свойству объекта присвоен false");
                                                            this.__instance__[anotherProp].value = false;
                                                            this.__instance__[anotherProp]._backup_(false);
                                                        }
                                                    }
                                                    break;
                                            }

                                        } else {
                                            if (isField(obj[anotherProp])) {
                                                this.__instance__[anotherProp].value = obj[anotherProp].value;
                                                this.__instance__[anotherProp]._backup_(obj[anotherProp].value);
                                            } else {

                                                this.__instance__[anotherProp].value = obj[anotherProp];
                                                this.__instance__[anotherProp]._backup_(obj[anotherProp]);
                                            }
                                        }

                                    } else {
                                        if (isField(obj[anotherProp]))
                                            this.__instance__[anotherProp] = obj[anotherProp].value;
                                        else
                                            this.__instance__[anotherProp] = obj[anotherProp];
                                    }
                                }
                            }
                        }
                    }

                    if (this.__instance__["onInitModel"] !== undefined) {
                        if (this.__instance__["onInitModel"].constructor === Function) {
                            this.__instance__.onInitModel();
                        }
                    }
                },


                /**
                 * Возвращает значения полей модели данных в виде строки
                 * @returns {string} - Возвращает значения полей модели в виде строки
                 */
                toString: function () {
                    var result = {};
                    for (var prop in this.__instance__) {
                        if (this.__instance__[prop].constructor === Field) {
                            result[prop] = this.__instance__[prop];
                        }
                    }
                    return JSON.stringify(result);
                },


                /**
                 * Производит сброс значений полей модели данных к значениям по умолчанию
                 * @returns {number} - Возвращает количество полей, чьи значения были установлены в значение по
                 *                      умолчанию
                 */
                reset: function () {
                    var result = 0;
                    for (var prop in this.__instance__) {
                        if (this.__instance__[prop].constructor === Field &&
                            this.__instance__[prop].default_value !== undefined) {
                            this.__instance__[prop].value = this.__instance__[prop].default_value;
                            result++;
                        }
                    }
                    return result;
                },


                _init_: function () {
                    for (var prop in this.__instance__) {
                        if (this.__instance__[prop].constructor === Field &&
                            this.__instance__[prop].default_value !== undefined) {
                            this.__instance__[prop].value = this.__instance__[prop].default_value;
                        }
                    }
                }
            }
        });



        /******************************
         * Backup
         * Набор свойств и методов, реализующих резервное копирование данных объекта
         ******************************/
        add("Backup", {
            __dependencies__: [],
            _backup_: {
                __instance__: "",
                data: {},

                /**
                 * Устанавливает резервные значения для полей, помеченных для бэкапа
                 * @returns {number} - Возвращает количество полей, для короых созданы резервные значения
                 */
                setup: function () {
                    var result = 0;
                    for (var prop in this.__instance__) {
                        if (this.__instance__[prop].constructor === Field && this.__instance__[prop].backupable !== undefined && this.__instance__[prop].backupable === true && this.__instance__[prop] !== null) {
                            if (this.__instance__[prop].type !== undefined) {
                                switch (parseInt(this.__instance__[prop].type)) {
                                    case DATA_TYPE_STRING:
                                        this.data[prop] = this.__instance__[prop].value.toString();
                                        break;
                                    case DATA_TYPE_INTEGER:
                                        this.data[prop] = parseInt(this.__instance__[prop].value);
                                        break;
                                    case DATA_TYPE_FLOAT:
                                        this.data[prop] = +parseFloat(this.__instance__[prop].value).toFixed(6);
                                        break;
                                    case DATA_TYPE_BOOLEAN:
                                        this.data[prop] = this.__instance__[prop].value;
                                        break;
                                }
                            } else {
                                this.data[prop] = this.__instance__[prop].value;
                                result++;
                            }
                        }
                    }
                    if (this.__instance__._init_ !== undefined)
                        this.__instance__._init_();
                    return result;
                },

                /**
                 * Восстанавливает резервные значения полей, занесенных в бэкап
                 * @returns {number} Возвращает количество полей, для которых восстановлены резервные значения
                 */
                restore: function () {
                    var result = 0;
                    for (var prop in this.data) {
                        if (this.__instance__[prop] !== undefined &&
                            this.__instance__[prop].constructor === Field &&
                            this.__instance__[prop].backupable === true) {
                            this.__instance__[prop].value = this.data[prop];
                            result++;
                        }
                    }
                    if (this.__instance__.onInitModel !== undefined)
                        this.__instance__.onInitModel();
                    if (this.__instance__.onRestoreBackup !== undefined && typeof (this.__instance__.onRestoreBackup === "function"))
                        this.__instance__.onRestoreBackup();
                    return result;
                },

                toString: function () {
                    return JSON.stringify(this.data);
                }
            }
        });


        /**
         * File
         * Набор свойств и методов, описывающих файл
         */
        add("File", {
            _dependencies__: [],
            title: new Field({ source: "title", type: DATA_TYPE_STRING, default_value: "", value: "", backupable: true, displayable: true }),
            type: new Field({ source: "type", type: DATA_TYPE_STRING, default_value: "", value: "", backupable: true, displayable: true }),
            size: new Field({ source: "size", type: DATA_TYPE_INTEGER, default_value: 0, value: 0, backupable: true, displayable: true }),
            url: new Field({ source: "url", type: DATA_TYPE_STRING, default_value: "", value: "", backupable: true, displayable: true })
        });




        /******************************
         * Collection
         * Набор свойств и методов, описывающих коллекцию элементов
         ******************************/
        add("Collection", {
            items: [],
            selectedItems: [],
            allowMultipleSelect: false,
            allowMultipleSearch: false,

            /**
             * Возвращает количество элементов в коллекции
             * @returns {Number} - Возвращает размер коллекции
             */
            size: function () {
                return this.items.length;
            },

            /**
             * Выводит в консоль все элементы коллекции
             * @returns {Number} - Возвращает количество элементов в коллекции
             */
            display: function () {
                var result = this.items.length;
                if (console !== undefined) {
                    console.log(this.items);
                }
                return result;
            },

            /**
             * Включает / выключает режим поиска нескольких элементов коллекции
             * @param flag {boolean} - Флаг, включения / выключения режима поиска нескольких элементов коллекции
             * @returns {boolean} - Возвращает флаг, включен ли режим поиска нескольких элементов коллекции
             */
            multipleSearch: function (flag) {
                var result = false;
                if (flag !== undefined) {
                    if (flag.constructor === Boolean) {
                        this.allowMultipleSearch = flag;
                        result = this.allowMultipleSearch;
                    } else
                        $log.error("$classes 'Collection': Параметр должен быть типа Boolean");
                } else
                    $log.error("$classes 'Collection': Не указан параметр при установке режима поиска нескольких элементов");
                return result;
            },

            /**
             * Возвращает элемент коллекции, поле field которого равен value
             * @param field {String} - Наименование поля
             * @param value - Значение искомого поля
             * @returns {boolean/Any} - Возвращает искомый элемент коллекции, в противном случае false
             */
            find: function (field, value) {
                var result = false;
                var temp_result = [];
                var length = this.items.length;

                /* Если требуется найти элемент коллекции по значению поля */
                if (field !== undefined && value !== undefined) {
                    //console.log("finding item by field and value");
                    for (var i = 0; i < length; i++) {
                        if (this.items[i][field] !== undefined) {
                            if (this.items[i][field].constructor === Field) {
                                if (this.items[i][field].value === value) {
                                    if (this.allowMultipleSearch === true) {
                                        temp_result.push(this.items[i]);
                                    } else {
                                        temp_result.splice(0, temp_result.length);
                                        temp_result.push(this.items[i]);
                                        result = this.items[i];
                                    }
                                }
                            } else {
                                if (this.items[i][field] === value) {
                                    if (this.allowMultipleSearch === true) {
                                        temp_result.push(this.items[i]);
                                    } else {
                                        temp_result.splice(0, temp_result.length);
                                        temp_result.push(this.items[i]);
                                        result = this.items[i];
                                    }
                                }
                            }
                        }
                    }
                }

                /* Если требуется найти элемент коллекции по значению */
                if (field !== undefined && value === undefined) {
                    //console.log("finding item by value");
                    for (var i = 0; i < length; i++) {
                        if (this.items[i] === field) {
                            if (this.allowMultipleSearch === true) {
                                temp_result.push(this.items[i]);
                                result = this.items[i];
                            } else {
                                temp_result.splice(0, temp_result.length);
                                temp_result.push(this.items[i]);
                            }
                        }
                    }
                }

                if (temp_result.length === 0)
                    return false;
                else if (temp_result.length === 1)
                    return temp_result[0];
                else if (temp_result.length > 1)
                    return temp_result;
            },


            /**
             * Добавляет элемент в конец коллекции
             * @param item {Any} - Элемент, добавляемый в коллекцию
             * @returns {boolean / Number} - Возвращает новую длину коллекции, false в случае некорректного завершения
             */
            append: function (item) {
                var result = false;
                if (item !== undefined) {
                    this.items.push(item);
                }
                return item;
            },

            /**
             * Удаляет элементы по значению поля и по значению
             * @param field {String} - Наименование поля
             * @param value {Any} - Значение поля
             * @returns {Number} - Возвращает количество удаленных элементов
             */
            delete: function (field, value) {
                var result = 0;
                var length = this.items.length;

                /* Если требуется удалить элементы коллекции по полю и его значению */
                if (field !== undefined && value !== undefined) {
                    console.log("deleting by field and value");
                    for (var i = 0; i < this.items.length; i++) {
                        if (this.items[i][field] !== undefined) {
                            if (this.items[i][field].constructor === Field) {
                                if (this.items[i][field].value === value) {
                                    this.items.splice(i, 1);
                                    result++;
                                }
                            } else {
                                if (this.items[i][field] === value) {
                                    this.items.splice(i, 1);
                                    result++;
                                }
                            }
                        }
                    }
                }

                /* Если требуется удалить элементы по значению */
                if (field !== undefined && value === undefined) {
                    console.log("deleting by value");
                    for (var i = 0; i < length; i++) {
                        if (this.items[i] === field) {
                            this.items.splice(i, 1);
                            result++;
                        }
                    }
                }

                return result;
            },

            /**
             *
             */
            clear: function () {
                this.items.splice(0, this.items.length);
                return true;
            },

            /**
             * Включает / выключает режим выбора нескольких элементов коллекции
             * @param flag {boolean} - Флаг включения / выключения режима выбора нескольких элементов коллекции
             * @returns {boolean} - Возвращает флаг, включен ли режим выбора нескольких элементов коллекции
             */
            multipleSelect: function (flag) {
                if (flag !== undefined) {
                    if (flag.constructor === Boolean) {
                        this.allowMultipleSelect = flag;
                        return this.allowMultipleSelect;
                    } else
                        $log.error("$classes 'Collection': Параметр должен быть типа Boolean");
                } else
                    $log.error("$classes 'Collection': Не указан параметр при установке режима выбора нескольких элементов");
            },

            /**
             * Помечает элемент коллекции как выбранный
             * @param field {string} - Наименование поля элемента коллекции
             * @param value {any} - Значение поля элемента коллекции
             * @returns {number}
             */
            select: function (field, value) {
                var result = [];
                if (field !== undefined && value !== undefined) {
                    var length = this.items.length;
                    for (var i = 0; i < length; i++) {
                        if (this.items[i].hasOwnProperty(field)) {
                            var item = undefined;
                            if (this.items[i][field].constructor === Field) {
                                if (this.items[i][field].value === value) {
                                    console.log("element found", this.items[i]);
                                    item = this.items[i];
                                }
                            } else {
                                if (this.items[i][field] === value)
                                    item = this.items[i];
                            }
                            if (item !== undefined) {
                                if (this.allowMultipleSelect === true) {
                                    result.push(item);
                                    this.selectedItems.push(item);
                                    if (item._states_ !== undefined)
                                        item._states_.selected(true);
                                } else {
                                    result.splice(0, result.length);
                                    this.selectedItems.splice(0, this.selectedItems.length);
                                    result.push(this.items[i]);
                                    this.selectedItems.push(this.items[i]);
                                    if (item._states_ !== undefined)
                                        item._states_.selected(true);
                                    for (var x = 0; x < length; x++) {
                                        if (this.items[x] !== item) {
                                            if (this.items[x]._states_ !== undefined)
                                                this.items[x]._states_.selected(false);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                console.log("selectedItems = ", this.selectedItems);

                if (result.length === 0)
                    return false;
                if (result.length === 1)
                    return result[0];
                if (result.length > 1)
                    return result;

            },

            deselect: function (item) {
                if (item !== undefined) {
                    var length = this.items.length;
                    for (var i = 0; i < length; i++) {
                        if (this.items[i] === item) {
                            var selectedLength = this.selectedItems.length;
                            if (item._states_ !== undefined)
                                item._states_.selected(false);
                            for (var x = 0; x < selectedLength; x++) {
                                if (this.selectedItems[x] === item) {
                                    this.selectedItems.splice(x, 1);
                                }
                            }
                        }
                    }
                }
                console.log(this.selectedItems);
                return this.selectedItems;
            }
        });



        /******************************
         * States
         * Набор свойст и методов, описывающих состояние объекта
         ******************************/
        add("States", {
            __dependencies__: [],
            _states_: {
                isActive: false,        // Флаг, сигнализирующий, активен ли объект
                isSelected: false,      // Флаг, сигнализирующий, выбран ли объект
                isChanged: false,       // Флаг, сигнализирующий, был ли изменен объект
                isLoaded: true,         // Флаг, сигнализирующий был ли объект загружен
                isLoading: false,       // Флаг, сигнализирующий, находится ли объект в режиме загрузки
                isInEditMode: false,    // Флаг, сигнализирующий, находится ли объект в режиме редактирования
                isInDeleteMode: false,  // Флаг, сигнализирующий, находится ли объект в режиме удаления

                /**
                 * Устанавливает / отменяет режим активного объекта
                 * @param flag {Boolean} - Флаг активности / неактивности объекта
                 * @returns {boolean} - Возвращает флаг активности / неактивности объекта
                 */
                active: function (flag) {
                    if (flag !== undefined && flag.constructor === Boolean)
                        this.isActive = flag;
                    return this.isActive;
                },

                /**
                 * Устанавливает / отменяет режим редактирования объекта
                 * @param flag {Boolean} - Флаг нахождения объекта в режиме редактирования
                 * @returns {boolean} - Возвращает флаг нахождения объекта в режиме редактирования
                 */
                editing: function (flag) {
                    if (flag !== undefined && flag.constructor === Boolean)
                        this.isInEditMode = flag;
                    return this.isInEditMode;
                },

                /**
                 * Устанавливает / отменяет режим удаления объекта
                 * @param flag {boolean} - Флаг нахождения объекта в режиме удаления
                 * @returns {boolean} - Возвращает флаг нахождения объекта в режиме удаления
                 */
                deleting: function (flag) {
                    if (flag !== undefined && flag.constructor === Boolean)
                        this.isInDeleteMode = flag;
                    return this.isInDeleteMode;
                },

                /**
                 * Устанавливает / отменяет режим измененного объекта
                 * @param flag {boolean} - Флаг, был ли объект изменен
                 * @returns {boolean} - Возвращает флаг, был ли объект изменен
                 */
                changed: function (flag) {
                    if (flag !== undefined && flag.constructor === Boolean)
                        this.isChanged = flag;
                    return this.isChanged;
                },

                /**
                 * Устанавливает / отменяет режим выбранного объекта
                 * @param flag {boolean} - Флаг, был ли выбран объект
                 * @returns {boolean} - Возвращает флаг, был ли выбран объект
                 */
                selected: function (flag) {
                    if (flag !== undefined && flag.constructor === Boolean) {
                        this.isSelected = flag;
                        //console.log("selected = ", this.isSelected);
                    }
                    return this.isSelected;
                },

                /**
                 * Устанавливает / отменяет режим загруженного объекта
                 * @param flag {boolean} - Флаг, был ли объект загружен
                 * @returns {boolean} - Возвращает флаг, был ли объект загружен
                 */
                loaded: function (flag) {
                    if (flag !== undefined && flag.constructor === Boolean)
                        this.isLoaded = flag;
                    return this.isLoaded;
                },

                /**
                 * Устанавливает / отменяет режим загруженного объекта
                 * @param flag {boolean} - Флаг, был ли объект загружен
                 * @returns {boolean} - Возвращает флаг, был ли объект загружен
                 */
                loading: function (flag) {
                    if (flag !== undefined && flag.constructor === Boolean)
                        this.isLoading = flag;
                    return this.isLoading;
                }
            }
        });


        return {
            /**
             * Добавляет класс в стек классов
             * @param className - Наименование класса
             * @param classDefifnition - Определение класса
             * @returns {boolean}
             */
            add: function (className, classDefinition) {
                add(className, classDefinition);
            },

            /**
             * Возвращает все классы из стека
             * @returns {{}}
             */
            getAll: function () {
                return items;
            }
        };
    };



    /******************************
     * $factory
     * Сервис фабрики объектов
     ******************************/
    function factoryFactory ($log, $classes) {
        
        function FactoryObject (parameters) {
            
            var clone = function _clone(obj) {
                if (obj instanceof Array) {
                    var out = [];
                    for (var i = 0, len = obj.length; i < len; i++) {
                        var value = obj[i];
                        out[i] = (value !== null && typeof value === "object") ? _clone(value) : value;
                    }
                } else {
                    var out = new obj.constructor();
                    for (var key in obj) {
                        if (obj.hasOwnProperty(key)) {
                            var value = obj[key];
                            out[key] = (value !== null && typeof value === "object") ? _clone(value) : value;
                        }
                    }
                }
                return out;
            };

            var _clone = function (it) {
                return this._clone({
                    it: it
                }).it;
            };


            var classes = $classes.getAll();
            var addClass = function (className, destination) {
                if (className !== undefined && destination !== undefined) {
                    if (classes.hasOwnProperty(className)) {
                        destination.init_functions = [];
                        for (var prop in classes[className]) {
                            if (prop !== "__dependencies__") {
                                if (classes[className][prop].constructor !== Function) {
                                    destination[prop] = clone(classes[className][prop]);
                                    if (destination[prop]["__instance__"] !== undefined)
                                        destination[prop]["__instance__"] = destination;
                                    if (destination[prop]["_init_"] !== undefined && destination[prop]["_init_"].constructor === Function)
                                        destination.init_functions.push(destination[prop]["_init_"]);
                                    if (destination["_init_"] !== undefined && destination["_init_"].constructor === Function)
                                        destination.init_functions.push(destination["_init_"]);

                                } else {
                                    destination[prop] = classes[className][prop];
                                    if (prop === "_init_")
                                        destination.init_functions.push(destination[prop]);
                                }
                            }
                        }
                    }
                }
            };


            if (parameters !== undefined) {
                if (parameters.hasOwnProperty("classes")) {
                    if (parameters["classes"].constructor === Array) {
                        for (var parent in parameters["classes"]) {
                            var class_name = parameters["classes"][parent];
                            addClass(class_name, this);
                        }
                    }
                }
                if (parameters.hasOwnProperty("base_class")) {
                    if (classes.hasOwnProperty(parameters["base_class"])) {
                        this.__class__ = parameters["base_class"];
                    }
                }
            }

        };

        return function (parameters) {
            var obj = new FactoryObject(parameters);
            if (obj.init_functions.length > 0) {
                for (var i = 0; i < obj.init_functions.length; i++) {
                   // console.log("init func = ", obj.init_functions[i]);
                    obj.init_functions[i].call(obj);
                }
            }
            //return new FactoryObject(parameters);
            return obj;
        };
    };



    /********************
     * $errors
     * Сервис, реализующий управление ошибками в ходе выполнения приложения
     ********************/
    function errorsFactory ($log, $classes, $factory) {
        /**
         * Error
         * Набор свойств и методов, описывающий ошибку
         */
        $classes.add("Error", {
            __dependencies__: [],
            type: new Field({ source: "typeId", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            message: new Field({ source: "message", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            timestamp: new Field({ source: "timestamp", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 })
        });

        var items = [];

        return {
            /**
             * Добавляет ошибку в стек
             * @param errorType - Тип ошибки
             * @param errorMessage - Текст ошибки
             * @returns {*} - Error / false
             */
            add: function (errorType, errorMessage) {
                if (errorType !== undefined) {
                    if (!isNaN(errorType)) {
                        if (errorMessage !== undefined) {
                            var tempError = $factory({ classes: ["Error", "Model"], base_class: "Error" });
                            tempError.type.value = errorType;
                            tempError.message.value = errorMessage;
                            tempError.timestamp.value = Math.floor(Date.now() / 1000);
                            items.push(tempError);
                            $log.error(moment.unix(tempError.timestamp.value).format("DD.MM.YYYY HH:mm") + ": " + tempError.message.value);
                            return tempError;
                        } else {
                            $log.error("$errors -> add: Не задан параметр - текст ошибки");
                            return false;
                        }
                    } else {
                        $log.error("$errors -> add: Неверно задан тип параметра - тип ошибки");
                        return false;
                    }
                } else {
                    $log.error("$errors -> add: Не задан параметр - тип ошибки");
                    return false;
                }
            },

            /**
             * Возвращает стек ошибок
             * @returns {Array}
             */
            getAll: function () {
                return items;
            },

            /**
             * Проверяет, является ли объект экземпляром ошибки
             * @param error - Объект для проверки
             * @returns {boolean}
             */
            isError: function (error) {
                if (error !== undefined) {
                    if (typeof error === "object" && error.type !== undefined && error.message !== undefined && error.timestamp !== undefined) {
                        this.add(error.type, error.message);
                        return true;
                    } else
                        return false;
                } else {
                    var tempError = $factory({ classes: ["Error", "Model"], base_class: "Error" });
                    tempError.type.value = ERROR_TYPE_DEFAULT;
                    tempError.message.value = "$errors -> isError: Не задан параметр - объект для проверки";
                    tempError.timestamp.value = Math.floor(Date.now() / 1000);
                    items.push(tempError);
                    return false;
                }
            },

            init: function () {
                if (window.krypton !== undefined && window.krypton !== null) {
                    if (window.krypton.errors !== undefined) {
                        var length = window.krypton.errors.length;
                        for (var i = 0; i < length; i++) {
                            var error = $factory({ classes: ["Error", "Model"], base_class: "Error" });
                            error._model_.fromAnother(window.krypton.errors[i]);
                            items.push(error);
                            $log.error(error.message.value);
                        }
                    }
                }
            }
        }
    };





    /**
     * $settings
     * CСервис управления настройками приложения
     */
    function settingsFactory ($log, $classes, $factory) {
        /**
         * Setting
         * Набор свойств и методов, описывающих настройку приложения
         */
        $classes.add("Setting", {
            __dependencies__: [],
            extensionId: new Field({ source: "module_id", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            code: new Field({ source: "code", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            title: new Field({ source: "title", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            description: new Field({ source: "description", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            dataType: new Field({ source: "data_type", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            isSystem: new Field({ source: "is_system", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            value: new Field({ source: "value", type: DATA_TYPE_STRING, value: "", default_value: "" })
        });

        var items = [];

        return {

            init: function () {
                if (window.krypton !== null && window.krypton !== undefined) {
                    if (window.krypton.settings !== null) {
                        var length = window.krypton.settings.length;
                        for (var i = 0; i < length; i++) {
                            var setting = $factory({ classes: ["Setting", "Model"], base_class: "Setting" });
                            setting._model_.fromAnother(window.krypton.settings[i]);
                            items.push(setting);
                        }
                        $log.log("settings = ", items);
                    }
                }
            },

            getAll: function () {
                return items;
            }
        }
    };





    /**
     * $extensions
     * Сеовис управления загруженными расширениями
     */
    function extensionsFactory ($log, $classes, $factory) {

        $classes.add("Extension", {
            __dependencies__: [],
            id: new Field({ source: "id", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            description: new Field({ source: "description", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            url: new Field({ source: "url", type: DATA_TYPE_STRING, value: "", default_value: "" })
        });

        var items = [];

        return {
            init: function () {
                if (window.krypton !== null && window.krypton !== undefined) {
                    if (window.krypton.extensions !== null && window.krypton.extensions !== undefined) {
                        var length = window.krypton.exyensions.length;
                        for (var i = 0; i < length; i++) {
                            var extension = $factory({ classes: ["Extension", "Model"], base_class: "Extension" });
                            extension._model_.fromAnother(window.krypron.extensions[i]);
                            items.push(extension);
                        }
                    }
                }
            },

            getAll: function () {
                return items;
            }


        }
    };



    /**
     * $session
     * Сервис для управления текущей сессией и текущим пользователем сессии
     */
    function sessionFactory ($log, $classes, $factory) {
        /********************
         * Session
         * Набор свойств и методов, описывающих текущую сессию пользователя
         ********************/
        $classes.add("Session", {
            __dependencies__: [],
            userId: new Field({ source: "user_id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            token: new Field ({ source: "token", type: DATA_TYPE_STRING, value: "", default_value: "" }),
            start: new Field({ source: "start", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            end: new Field({ source: "end", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 })
        });

        /**
         * UserGroup
         * Набор свойств и методов, описывающих группу пользователей
         */
        $classes.add("UserGroup", {
            __dependencies__: [],
            id: new Field({ source: "id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0 }),
            title: new Field({ source: "title", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true })
        });

        /********************
         * User
         * Набор свойств и методов, описывающих пользователя
         ********************/
        $classes.add("User", {
            __dependencies__: [],
            id: new Field({ source: "id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, backupable: true, displayable: true, title: "#"}),
            userGroupId: new Field({ source: "user_group_id", type: DATA_TYPE_INTEGER, value: 0, default_value: 0, displayable: false }),
            name: new Field({ source: "name", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true, displayable: true, title: "Имя" }),
            fname: new Field({ source: "fname", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true, displayable: true, title: "Отчество" }),
            surname: new Field({ source: "surname", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true, displayable: true, title: "Фамилия" }),
            position: new Field({ source: "position", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            email: new Field({ source: "email", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            phone: new Field({ source: "phone", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            mobile: new Field({ source: "mobile_phone", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            photo: new Field({ source: "photo_url", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            isAdmin: new Field({ source: "is_admin", type: DATA_TYPE_BOOLEAN, value: false, default_value: false, backupable: true }),
            fio: "",
            phones: [],
            
            onInitModel: function () {
                this.fio = this.surname.value + " " + this.name.value + " " + this.fname.value;
                this.search = this.fio + " " + this.email.value;
                this.phones = this.phone.value.replace(/\s/g, '').split(",");
            }
        });

        var session = undefined;
        var user = undefined;
        var groups = [];

        return {

            init: function () {
                session = $factory({ classes: ["Session", "Model"], base_class: "Session" });
                user = $factory({ classes: ["User", "Model", "States"], base_class: "User" });

                if (window.krypton !== undefined && window.krypton !== null) {
                    if (window.krypton.session !== null) {
                        session._model_.fromAnother(krypton.session);
                        $log.log("updated session = ", session);
                    }
                    if (window.krypton.user !== null) {
                        $log.log("USER !== NULL");
                        user._model_.fromAnother(krypton.user);
                        $log.log("updated user = ", user);
                    }
                    if (window.krypton.user !== null) {
                        var length = window.krypton.userGroups;
                        for (var i = 0; i < length; i++) {
                            var userGroup = $factory({ classes: ["UserGroup", "Model", "Backup", "States"], base_class: "UserGroup" });
                            userGroup._model_.fromAnother(window.krypton.userGroups[i]);
                            userGroup._backup_.setup();
                            groups.push(userGroup);
                        }
                        $log.log("user groups = ", groups);
                    }
                }
            },

            /**
             * Возвращает объект текущей сессии
             * @returns {*}
             */
            getCurrentSession: function () {
                return session;
            },

            /**
             * Возвращает объект пользователя текущей сессии
             * @returns {*}
             */
            getCurrentUser: function () {
                return user;
            },

            getUserGroups: function () {
                return groups;
            }
        }
    };



    /******************************
     * $users
     * Сервис для управления пользователями
     ******************************/
    function usersFactory ($log, $factory) {
        var users = [];
        var groups = [];
        var currentGroup = undefined;
        var currentUser = undefined;

        return {
            init: function () {
                if (window.krypton !== null && window.krypton !== undefined) {
                    if (window.krypton.users !== null && window.krypton.users !== undefined) {
                        var length = window.krypton.users.length;
                        for (var i = 0; i < length; i++) {
                            var user = $factory({ classes: ["User", "Model", "States", "Backup"], base_class: "User" });
                            user._model_.fromAnother(window.krypton.users[i]);
                            user._backup_.setup();
                            users.push(user);
                        }
                        $log.log("users = ", users);
                    }

                    if (window.krypton.userGroups !== null && window.krypton.userGroups !== undefined) {
                        var length = window.krypton.userGroups.length;
                        for (var i = 0; i < length; i++) {
                            var group = $factory({ classes: ["UserGroup", "Model", "States", "Backup"], base_class: "UserGroup" });
                            group._model_.fromAnother(window.krypton.userGroups[i]);
                            group._backup_.setup();
                            groups.push(group);
                        }
                        $log.log("groups = ", groups);
                    }
                }
            },
            
            groups: {

                /**
                 * Возвращает массив всех группами пользователей
                 * @returns {Array}
                 */
                getAll: function () {
                    return groups;
                },

                /**
                 * Возвращает текщую группу пользователей
                 * @returns {UserGroup / undefined}
                 */
                getCurrent: function () {
                    return currentGroup;
                },

                /**
                 * Выбираент группу пользователей
                 * @param groupId {number} - идентификатор группы пользователей
                 * @returns {UserGroup / boolean}
                 */
                select: function (groupId) {
                    if (groupId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$users -> selectGroup: Не задан параметр - идентификатор группы пользователей");
                        return false;
                    }

                    var length = groups.length;
                    for (var i = 0; i < length; i++) {
                        if (groups[i].id.value === groupId) {
                            if (groups[i]._states_.selected() === false) {
                                groups[i]._states_.selected(true);
                                currentGroup = groups[i];
                            } else {
                                groups[i]._states_.selected(false);
                                currentGroup = undefined;
                            }
                        } else
                            groups[i]._states_.selected(false);
                    }
                },

                /**
                 * Удаляет группу пользователей
                 * @param groupId {number} - идентификатор группы пользователей
                 * @returns {boolean}
                 */
                delete: function (groupId) {
                    if (groupId === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "$users -> groups -> delete: Не задан параметр - идентификатор группы пользователей");
                        return false;
                    }

                    var length = groups.length;
                    for (var i = 0; i < length; i++) {
                        if (groups[i].id.value === groupId) {
                            groups.splice(i, 1);
                            currentGroup = undefined;
                            return true;
                        }
                    }

                    return false;
                }
            },

            

            

            

            getUsers: function () {
                return users;
            },
            
            addGroup: function (group) {
                if (group === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$users -> addGroup: Не задан параметр - объект с информацией о добавляемой группе пользователей");
                    return false;
                }
                
                groups.push(group);
                return true;
            }
        }
    };


    
    /******************************
     * $application
     * Сервис приложения
     ******************************/
    function applicationFactory ($log, $classes, $factory) {
        /**
         * Application
         * Набор свойств и метлодов, описывающих приложение
         */
        $classes.add("Application", {
            __dependencies__: [],
            title: new Field({ source: "title", type: DATA_TYPE_STRING, value: "", default_value: "", back: true }),
            description: new Field({ source: "description", type: DATA_TYPE_STRING, value: "", default_value: "", backupable: true }),
            inDebugMode: new Field({ source: "is_in_debug_mode", type: DATA_TYPE_BOOLEAN, value: false, default_value: false, backupable: true }),
            inConstructionMode: new Field({ source: "is_in_construction_mode", type: DATA_TYPE_BOOLEAN, value: false, default_value: false, backupable: true })
        });

        var app = $factory({ classes: ["Application", "Model", "States", "Backup"], base_class: "Application" });

        return {
            init: function () {
                if (window.krypton !== null && window.krypton !== undefined) {
                    if (window.krypton.application !== null && window.krypton.application !== undefined) {
                        app._model_.fromAnother(window.krypton.application);
                        app._backup_.setup();
                        $log.log("app = ", app);
                    }
                }
            },

            get: function () {
                return app;
            }
        }
    };



    function uniqueFilter ($log) {
        return function (items, filterOn) {
            if (filterOn === false) {
                return items;
            }

            if ((filterOn || angular.isUndefined(filterOn)) && angular.isArray(items)) {
                var hashCheck = {}, newItems = [];

                var extractValueToCompare = function (item) {
                    if (angular.isObject(item) && angular.isString(filterOn)) {
                        return item[filterOn];
                    } else {
                        return item;
                    }
                };

                angular.forEach(items, function (item) {
                    var valueToCheck, isDuplicate = false;

                    for (var i = 0; i < newItems.length; i++) {
                        if (angular.equals(extractValueToCompare(newItems[i]), extractValueToCompare(item))) {
                            isDuplicate = true;
                            break;
                        }
                    }
                    if (!isDuplicate) {
                        newItems.push(item);
                    }

                });
                items = newItems;
            }
            $log.log("rsulkt = ", items);
            return items;
        }
    };



    function uploaderDirective ($log, $http, $parse) {
        return {
            restrict: "A",
            scope: {
                uploaderUrl: "@",
                uploaderData: "=",
                uploaderOnCompleteUpload: "=",
                uploaderOnBeforeUpload: "="
            },
            link: function (scope, element, attrs) {
                var fd = new FormData();

                /**
                 * Отслеживаем выбор файла для загрузки
                 */
                element.bind("change", function () {
                    //var fd = new FormData();
                    angular.forEach(element[0].files, function (file) {
                        $log.log(file);
                        fd.append("file", file);
                    });

                    /* Если задан коллбэк onBeforeUpload - выполняем его */
                    $log.log(scope.uploaderOnBeforeUpload);
                    if (scope.uploaderOnBeforeUpload !== undefined) {
                        scope.$apply(scope.uploaderOnBeforeUpload);
                        scope.upload();
                    } else
                        scope.upload();

                    /* Если заданы данные для отправки на сервер - добавляем их в данные формы для отправки */
                    if (scope.uploaderData !== undefined) {
                        $log.log(scope.uploaderData);
                        for (var param in scope.uploaderData) {
                            fd.append(param, scope.uploaderData[param]);
                        }
                    }

                });

                /**
                 * Отправляет данные на сервер
                 */
                scope.upload = function () {
                    if (fd.has("file")) {
                        element.prop("disabled", "disabled");
                        $http.post(scope.uploaderUrl, fd,
                            {
                                transformRequest: angular.identity,
                                headers: {
                                    "Content-Type": undefined
                                }
                            }
                        ).success(function (data) {
                                $log.log(data);
                                element.prop("disabled", "");
                                if (scope.uploaderOnCompleteUpload !== undefined)
                                    scope.uploaderOnCompleteUpload(data);
                                fd.delete("file");
                                fd = new FormData();
                            }
                        );    
                    }
                };

            }
        }
    };



    /********************
     * Запуск основного модуля библиотеки
     ********************/
    function kryptonRun ($log, $application, $errors, $settings, $session) {
        $log.log("krypton.js run...");
        //$application.init();
        //$errors.init();
        //$session.init();
        //$settings.init();
    };


}) ();
