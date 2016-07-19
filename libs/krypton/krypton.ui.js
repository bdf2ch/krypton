(function () {
    angular
        .module("krypton.ui", [])
            .factory("$dateTimePicker", dateTimePickerFactory)
            .directive("uiDateTimePicker", dateTimePickerDirective)
            .directive("uiModelField", modelFieldDirective)
            .directive("modelGrid", modelGridDirective)
            .directive("columns", columnsDirective)
            .directive("column", columnDirective)
            .directive("columnControl", columnControlDirective)
            .factory("$hierarchy", hierarchyFactory)
            .directive("hierarchy", hierarchyDirective)
            .directive("modelList", modelListDirective)
            .directive("centered", centeredDirective)
            .factory("$modals", modalsFactory)
            .directive("modal", modalDirective)
            .run(kryptonUIRun);
    
    
    
    function kryptonUIRun ($log, $classes, $rootScope, $modals) {
        $log.log("krypton.ui run...");
        /**
         * DateTimePicker
         * Набор свойств и методов, описывающих элемент интрефейса - календарь
         */
        $classes.add("DateTimePicker", {
            __dependencies__: [],
            id: "",
            element: 0,
            title: "",
            value: 0,
            isModal: false,
            isOpened: false,
            isTimeEnabled: false,
            isTroughNavigationEnabled: false,
            scope: {},

            open: function () {
                this.isOpened = true;
                this.scope.open();
            },

            close: function () {
                this.isOpened = false;
                this.scope.close();
            }
        });

        $rootScope.modals = $modals;
    };


    /**
     * dateTimePicker directive
     * Виджет выбора даты / времени
     */
    function dateTimePickerDirective ($log, $errors, $compile, $window, $document, $dateTimePicker) {
        return {
            restrict: "A",
            require: "ngModel",
            scope: {
                ngModel: "=",
                dateTimePickerModal: "=",
                dateTimePickerEnableTime: "=",
                dateTimePickerEnableMinutes: "=",
                dateTimePickerThroughNavigation: "="
            },
            link: function (scope, element, attrs, controller) {

                var template =
                    "<div class='toolbar'>" +
                        "<div class='control'><button class='width-100 blue' ng-class='{ \"very-big\": settings.isModal === true }' ng-click='prev()'>&larr;</button></div>" +
                        "<div class='content'>" +
                            "<select class='width-60 no-border' ng-if='isInTimeSelectMode === false' ng-model='month' ng-options='month[0] as month[1] for month in months'></select>" +
                            "<select class='width-40 no-border' ng-if='isInTimeSelectMode === false' ng-model='year' ng-options='year as year for year in years'></select>" +
                            "<span class='selected-date width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === false && settings.isModal === false'>{{ value.format('DD.MM.YYYY, HH ч.') }}</span>" +
                            "<span class='selected-date width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === false && settings.isModal === true'>{{ value.format('DD MMM YYYY, HH ч.') }}</span>" +
                            "<span class='selected-date width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === true && settings.isModal === false'>{{ value.format('DD.MM.YYYY, HH:mm') }}</span>" +
                            "<span class='selected-date width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === true && settings.isModal === true'>{{ value.format('DD MMM YYYY, HH:mm') }}</span>" +
                        "</div>" +
                        "<div class='control'><button class='width-100 blue' ng-class='{ \"very-big\": settings.isModal === true }' ng-click='next()'>&rarr;</button></div>" +
                    "</div>" +
                    "<div class='weekdays'>" +
                        "<div class='day width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === false'>часы</div>" +
                        "<div class='day width-100' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === true'>минуты</div>" +
                        "<div class='day' ng-if='isInTimeSelectMode === false' ng-repeat='weekday in weekdays track by $index'>" +
                            "<span ng-if='settings.isModal === true'>{{ weekday[1] }}</span><span ng-if='settings.isModal === false'>{{ weekday[0] }}</span>" +
                        "</div>" +
                    "</div>" +
                    "<div class='days-container' style='height:{{ height + \"px\" }}; max-height:{{ height }};'>" +
                        "<div class='day' ng-if='isInTimeSelectMode === false' ng-class='{\"sunday\": ($index + 1) % 7 === 0, \"not-this-month\": day.month() !== month, \"current\": day.date() === now.date() && day.month() === value.month()}' ng-repeat='day in days track by $index' ng-click='select(day.unix())'>{{ day.date() }}</div>" +
                        "<div style='line-height: {{ height / 4 + \"px\" }};' class='hour' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === false' ng-class='{\"current\": value.hours() === $index}' ng-repeat='hour in hours track by $index' ng-click='select($index)'>{{ hour }}</div>" +
                        "<div style='line-height: {{ height / 3 + \"px\" }};' class='minute' ng-if='isInTimeSelectMode === true && isInMinutesSelectMode === true' ng-class='{\"current\": value.minutes() === ($index * 5)}' ng-repeat='minute in minutes track by $index' ng-click='select(minute)'>{{ minute }}</div>" +
                    "</div>";

                var height = scope.height = 0;
                var calendarHeight = scope.calendarHeight = 0;
                var ctrl = scope.ctrl = controller;
                var days = scope.days = [];
                var weekdays = scope.weekdays = [
                    ["Пн", "Понедельник"], ["Вт", "Вторник"],
                    ["Ср", "Среда"], ["Чт", "Четверг"] ,
                    ["Пт", "Пятница"], ["Сб", "Суббота"],
                    ["Вс", "Воскресение"]
                ];
                var months = scope.months =  [
                    [0, "Январь"] ,[1, "Февраль"], [2, "Март"],
                    [3, "Апрель"], [4, "Май"], [5, "Июнь"],
                    [6, "Июль"], [7, "Август"], [8, "Сентябрь"],
                    [9, "Октябрь"], [10, "Ноябрь"], [11, "Декабрь"]
                ];
                var hours = scope.hours = [
                    "00", "01", "02", "03",
                    "04", "05", "06", "07",
                    "08", "09", "10", "11",
                    "12", "13", "14", "15",
                    "16", "17", "18", "19",
                    "20", "21", "22", "23"
                ];
                var minutes = scope.minutes = [
                    "00", "05", "10", "15",
                    "20", "25", "30", "35",
                    "40", "45", "50", "55"
                ];

                var now = scope.now = moment(new Date());
                var date = scope.date = moment(now);
                var value = scope.value = moment(now);
                var day = scope.day = value.date();
                var month = scope.month = date.month();
                var year = scope.year = moment(value).year();
                //var hour = scope.hour = moment(value).hours();
                //var minute = scope.minute = moment(value).minutes();
                var years = scope.years = [];

                for (var i = moment(value).year() - 5; i < moment(value).year() + 5; i++) {
                    years.push(i);
                    if (i === moment(value).year())
                        selectedYear = i;
                }

                var isInTimeSelectMode = scope.isInTimeSelectMode = false;
                var isInMinutesSelectMode = scope.isInMinutesSelectMode = false;

                var settings = scope.settings = {
                    // Модальный режим отображения виджета
                    isModal: scope.dateTimePickerModal !== null && scope.dateTimePickerModal !== undefined ? true : false,
                    isOpened: false,
                    // Режим выбора времени
                    isTimeEnabled: scope.dateTimePickerEnableTime !== null && scope.dateTimePickerEnableTime !== undefined ? true : false,
                    // Сквозная навигация по часам и минутам
                    isTroughNavigationEnabled: scope.dateTimePickerThroughNavigation !== null && scope.dateTimePickerThroughNavigation !== undefined ? true : false,
                    title: "",
                    element: element
                };


                $log.info("settings = ", scope.settings);


                var recalculate = function (monthNumber) {
                    if (monthNumber !== undefined) {
                        var daysInMonth = moment(monthNumber + "-" + scope.year, "MM-YYYY").daysInMonth();
                        var weekDayOfFirstDay = moment("01." + monthNumber + "." + scope.year, "DD.MM.YYYY").weekday();
                        var weekDayOfLastDay = moment(daysInMonth + "." + monthNumber + "." + scope.year, "DD.MM.YYYY").weekday();
                        var lastDayInSelectedMonth = "";
                        $log.log("in month number " + monthNumber + " - " + daysInMonth + " days");
                        $log.log("first day in month = ", weekDayOfFirstDay);
                        $log.log("last day in month = ", weekDayOfLastDay);

                        days.splice(0, days.length);
                        if (weekDayOfFirstDay > 0) {
                            var start = moment("01." + monthNumber + "." + scope.year + " 00:00", "DD.MM.YYYY HH:mm").subtract(weekDayOfFirstDay, "days");
                            $log.log("first day in calendar = ", moment(start).format("DD.MM.YYYY"));
                            for (var x = 0; x < weekDayOfFirstDay; x++) {
                                var day = moment(start).add(x, "days");
                                days.push(day);
                            }
                        }
                        for (var i = 1; i <= daysInMonth; i++) {
                            var day = moment(i + "." + monthNumber + "." + scope.year + " 00:00", "DD.MM.YYYY HH:mm");
                            days.push(day);
                            if (moment(day).date() === daysInMonth) {
                                lastDayInSelectedMonth = day;
                                $log.log("last day = ", moment(lastDayInSelectedMonth).format("DD.MM.YYYY"));
                            }

                            $log.log("last day in month2 = ", weekDayOfLastDay);

                        }
                        if (weekDayOfLastDay < 6) {
                            //$log.log("last day in calendar = ", moment(start).format("DD.MM.YYYY"));
                            var start = moment(lastDayInSelectedMonth).add(1, "days");
                            for (var i = 1; i <= (6 - weekDayOfLastDay); i++) {
                                var day = moment(lastDayInSelectedMonth).add(i, "days");
                                days.push(day);
                            }
                        }
                    } else
                        return $errors.add(
                            ERROR_TYPE_DEFAULT,
                            "krypton.ui -> dateTimePicker directive: Не задан параметр - порядковый номер месяца"
                        );
                };


                var redraw = function (elm) {
                    if (elm !== undefined) {
                        var elementWidth = angular.element(element).prop("clientWidth");
                        var elementHeight = angular.element(element).prop("clientHeight");
                        var elementLeft = angular.element(element).prop("offsetLeft");
                        var elementTop = angular.element(element).prop("offsetTop");
                        var containerWidth = angular.element(elm).prop("clientWidth");
                        var containerHeight = angular.element(elm).prop("clientHeight");
                        var elementScrollTop = 0;
                        var elementScrollLeft = 0;
                        var windowWidth = $window.innerWidth;
                        var windowHeight = $window.innerHeight;
                        var left = 0;
                        var top = 0;

                        var parent = element[0].offsetParent;
                        //$log.log("parent = ", parent);
                        while (parent) {
                            //elementTop = elementTop + parent.offsetTop;
                            //elementLeft = elementLeft + parent.offsetLeft;
                            elementScrollLeft = elementScrollLeft + parent.scrollLeft;
                            elementScrollTop = elementScrollTop + parent.scrollTop;
                            parent = parent.offsetParent;
                        }
                        //$log.log("containerTop = ", elementScrollTop);
                        //$log.log("containerLeft = ", elementScrollLeft);
                        

                            //return {top: Math.round(top), left: Math.round(left), offsetX: Math.round(offsetX), offsetY: Math.round(offsetY)};
                        //};

                        //$log.log(angular.element($document).parent());
                        
                        if (scope.settings.isModal === true) {
                            left = (windowWidth / 2) - angular.element(elm).prop("clientWidth") / 2 + "px";
                            top = (windowHeight / 2) - ((angular.element(elm).prop("clientHeight")) / 2) + "px"
                        } else {
                            if (containerWidth > elementWidth) {
                                if ((elementLeft > (containerWidth - elementWidth) / 2) && (elementLeft < (windowWidth - elementLeft) + containerWidth / 2))
                                    left = elementLeft - ((containerWidth - elementWidth) / 2);
                            } else
                                left = angular.element(element).prop("offsetLeft") + "px";

                            if ((elementTop - containerHeight) + 10 < 0) {
                                top = elementTop + elementHeight + 10 + "px";
                            } else
                                top = angular.element(elm).prop("clientHeight") + elementScrollTop - 10 + "px";
                        }
                        angular.element(elm).css("left", left);
                        angular.element(elm).css("top", top);

                        if (scope.isInTimeSelectMode === true)
                            scope.height = scope.calendarHeight;
                        else {
                            scope.height = "auto";
                            scope.calendarHeight = scope.settings.isModal === true ? containerHeight - 80 : containerHeight - 55;
                        }
                        return true;
                    } else
                        return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive: Не задан параметр - HTML-элемент");
                };


                
                controller.$parsers.push(function (value) {

                });

                controller.$formatters.push(function (value) {
                    return scope.settings.isTimeEnabled === true ? moment.unix(value).format("DD MMM YYYY, HH:mm") : moment.unix(value).format("DD MMM YYYY");
                });


               


                scope.prev = function () {
                    if (scope.settings.isTimeEnabled === true && scope.isInTimeSelectMode === true) {
                        if (scope.isInMinutesSelectMode === false) {
                            if (scope.settings.isTroughNavigationEnabled === false) {
                                if (scope.value.hours() > 0 && scope.value.hours() <= 23)
                                    scope.value.subtract(1, "hours");
                                else if (scope.value.hours() === 0)
                                    scope.value.hours(23);
                            } else
                                scope.value.subtract(1, "hours");
                            $log.log(scope.value.format("DD.MM.YYYY HH:mm"));
                        } else {
                            if (scope.settings.isTroughNavigationEnabled === false) {
                                if (scope.value.minutes() > 0 && scope.value.minutes() <= 59)
                                    scope.value.subtract(5, "minutes");
                                else if (scope.value.minutes() === 0)
                                    scope.value.minutes(55);
                            } else
                                scope.value.subtract(5, "minutes");
                            $log.log(scope.value.format("DD.MM.YYYY HH:mm"));
                        }
                    } else {
                        scope.date.subtract(1, "months");
                        $log.log("currentDate = " + scope.value.format("DD.MM.YYYY"));
                        scope.month = scope.date.month();
                        scope.year = scope.date.year();
                        recalculate(scope.date.month() + 1);
                    }
                };


                scope.next = function () {
                    if (scope.settings.isTimeEnabled === true && scope.isInTimeSelectMode === true) {
                        if (scope.isInMinutesSelectMode === false) {
                            if (scope.settings.isTroughNavigationEnabled === false) {
                                if (scope.value.hours() >= 0 && scope.value.hours() < 23)
                                    scope.value.add(1, "hours");
                                else if (scope.value.hours() === 23)
                                    scope.value.hours(0);
                            } else
                                scope.value.add(5, "minutes");
                        } else
                            scope.value.add(5, "minutes");
                    } else {
                        scope.date.add(1, "months");
                        moment(scope.date).day(1);
                        $log.log("currentDate = " + moment(scope.date).format("DD.MM.YYYY"));
                        scope.month = moment(scope.date).month();
                        scope.year = moment(scope.date).year();
                        recalculate(scope.date.month() +1);
                    }
                };


                scope.select = function (value) {
                    if (value !== undefined) {
                        $log.log("selected value = ", value);
                        if (scope.settings.isTimeEnabled === true) {
                            if (scope.isInTimeSelectMode === false) {
                                var temp = moment.unix(value).hours(0).minutes(0).seconds(0);
                                scope.month = temp.month();
                                scope.day = temp.date();
                                scope.value.month(scope.month).date(scope.day).hours(0).minutes(0).seconds(0);
                                scope.ngModel = scope.value.unix();
                                scope.isInTimeSelectMode = true;
                                $log.log(scope.value.format("DD.MM.YYYY HH:mm"), scope.value.unix());
                            } else {
                                if (scope.isInMinutesSelectMode === false) {
                                    scope.value.hours(value).minutes(0).seconds(0);
                                    scope.ngModel = scope.value.unix();
                                    scope.isInMinutesSelectMode = true;
                                    $log.log(scope.value.format("DD.MM.YYYY HH:mm"), scope.value.unix());
                                } else {
                                    scope.value.minutes(parseInt(value));
                                    scope.ngModel = scope.value.unix();
                                    $log.log(scope.value.format("DD.MM.YYYY HH:mm"), scope.value.unix());
                                    scope.close();
                                }
                            }
                        } else {
                            var temp = moment.unix(value).hours(0).minutes(0).seconds(0);
                            scope.month = temp.month();
                            scope.day = temp.date();
                            scope.value.month(scope.month).date(scope.day).hours(0).minutes(0).seconds(0);
                            scope.ngModel = scope.value.unix();
                            $log.log(scope.value.format("DD.MM.YYYY HH:mm"), scope.value.unix());
                            scope.value = moment(new Date());
                            scope.close();
                        }
                    } else
                        $log.error("value = ", value);
                };


                var instance = $dateTimePicker.push(scope);
                var container = document.createElement("div");
                container.setAttribute("id", instance.id);
                container.className = "ui-date-time-picker2";
                if (scope.settings.isModal === true) {
                    container.classList.add("modal");
                    var fog = document.getElementsByClassName("krypton-ui-fog");
                    if (fog.length === 0) {
                        var fogElement = document.createElement("div");
                        fogElement.className = "krypton-ui-fog";
                        document.body.appendChild(fogElement);
                    }
                }
                container.innerHTML = template;
                document.body.appendChild(container);
                $compile(container)(scope);
                angular.element(element).css("cursor", "pointer");
                recalculate(scope.date.month() + 1);


                scope.open = function () {
                    if (scope.settings.isModal === true) {
                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        document.body.style.overflow = "hidden";
                        fog[0].classList.add("visible");
                    }
                    angular.element(container).css("display", "block");
                    scope.settings.isOpened = true;
                    redraw(container);
                };


                scope.close = function () {
                    if (scope.settings.isModal === true) {
                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        fog[0].classList.remove("visible");
                        document.body.style.overflow = "auto";
                    }
                    angular.element(container).css("display", "none");
                    scope.settings.isOpened = false;
                    scope.isInMinutesSelectMode = false;
                    scope.isInTimeSelectMode = false;
                    scope.value = new moment(new Date());
                    scope.$apply();
                };


                container.addEventListener("DOMSubtreeModified", function () {
                    redraw(container);
                }, false);


                angular.element($window).bind("resize", function () {
                    redraw(container);
                });


                angular.element($document).bind("mousedown", function (event) {
                    if (scope.settings.isOpened === true && container.contains(event.target) === false && event.target !== element[0])
                        scope.close();
                });


                element.on("mousedown", function () {
                    if (scope.settings.isOpened === false)
                        scope.open();
                });


                element.on("keydown", function (event) {
                    event.preventDefault();
                });

                angular.element(angular.element(element).parent()).on("scroll", function () {
                    $log.log("parent scrolled");
                    redraw(container);
                });
            }
        }
    };




    function dateTimePickerFactory ($log, $window, $document, $http, $errors, $factory, $compile, $rootScope) {
        var instances = [];
        var template = "";
        var isTemplateLoading = false;
        
        return {

            push: function (scope) {
                if (scope !== undefined) {
                    var instance = $factory({ classes: ["DateTimePicker"], base_class: "DateTimePicker" });
                    instance.id = "dateTimePicker" + instances.length;
                    instance.isModal = scope.settings.isModal;
                    instance.isOpened = scope.settings.isOpened;
                    instance.element = scope.settings.element;
                    instance.scope = scope;
                    instances.push(instance);
                    $log.log(instances);
                    return instance;
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive: Не задан параметр - объект с настройками директивы");
            },


            show: function (elementId) {
                $log.log("instances before = ", instances);
                if (elementId !== undefined) {
                    var length = instances.length;
                    for (var i = 0; i < length; i++) {
                        if (instances[i].element.getAttribute("id") === elementId)
                            $log.log("Element with id = " + elementId + " found!");
                    }
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> dateTimePicker directive : Не задан параметр - идентификатор HTML_элемента");
            },







            /**
             * Добавляет новый элемент в стек
             * @param parameters - Набор параметров инициализации
             * @returns {*}
             */
            add: function (parameters) {
                if (parameters !== undefined) {
                    if (typeof parameters === "object") {
                        if (parameters.element !== undefined) {

                            var element = document.getElementById(parameters.element);
                            $log.log("element = ", element);
                            if (element !== undefined && element !== null) {
                                var picker = $factory({classes: ["DateTimePicker"], base_class: "DateTimePicker"});
                                picker.id = "dateTimePicker" + instances.length;
                                picker.element = element;

                                //var instance = this.exists(angular.element(picker.element).prop("id"));

                                //if (element.classList.contains("ng-isolate-scope") === true)
                                    element.setAttribute("ui-date-time-picker", "");
                                ///element.setAttribute("ui-date-time-picker-opened", false);
                                //element.setAttribute("ng-if", "isOpened === true");

                                for (var param in parameters) {
                                    if (picker.hasOwnProperty(param)) {
                                        switch (param) {
                                            case "modelValue":
                                                picker.modelValue = parameters[param];
                                                //element.setAttribute("date-time-picker-model-value", dateTimePicker.modelValue);
                                                break;
                                            case "isModal":
                                                if (typeof parameters[param] === "boolean") {
                                                    picker.isModal = parameters[param];
                                                    //if (picker.isModal === true)
                                                    //    element.setAttribute("date-time-picker-modal", picker.isModal);
                                                } else
                                                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Неверно задан тип параметра - модальный режим");
                                                break;
                                            case "title":
                                                picker.title = parameters[param] !== undefined && parameters[param] !== "" ? parameters[param] : "";
                                                //if (picker.title !== "")
                                                //    element.setAttribute("date-time-picker-title", picker.title);
                                                break;
                                        }
                                    }
                                }


                                instances.push(picker);
                                $log.log(instances);
                                //element.setAttribute("ui-date-time-picker", "");
                                $log.info("dtp = ", picker);
                                $compile(picker.element)($rootScope.$new());

                                return picker;
                            } else
                                return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Элемент с идентификатором '" + parameters[param] + "' не найден");
                        } else
                            return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Не задан целевой элемент");
                    } else 
                        return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Неверно задан тип параметра инициализации");
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> add: Не заданы параметры инициализации");
            },

            
            
            open: function (elementId) {
                if (elementId !== undefined) {
                    var length = instances.length;
                    $log.log("instances defore", instances);
                    for (var i = 0; i < length; i++) {
                        var instanceFound = false;
                        if (angular.element(instances[i].element).prop("id") === elementId) {
                            instanceFound = true;
                            $log.log("element with id = " + elementId + " found");
                            $log.log(instances[i]);
                            instances[i].scope.open();
                        }
                        if (instanceFound === false)
                            $log.log("Element " + elementId + " not found");
                    }
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> open: Не задан параметр - идентификатор элемента");
            },


            /**
             * Проверяет на наличие экземпляра по идентификатору элемента
             * @param elementId - Идкнтификатор элемента
             * @returns {*}
             */
            exists: function (elementId) {
                if (elementId !== undefined) {
                    $log.log("elementId = ", elementId.toString());
                    var length = instances.length;
                    for (var i = 0; i < length; i++) {
                        $log.log("instance element = ", instances[i].element.getAttribute("id"));
                        if (instances[i].element.getAttribute("id").toString() === elementId) {
                            $log.log("founded instance = ", instances[i]);
                            return instances[i];
                        }
                    }
                    return false;
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> exists: Не задан параметр - идентификатор элкмкнта");
            },


            getTemplate: function () {
                return template;
            },


            setTemplate: function (tpl) {
                if (tpl !== undefined)
                    template = tpl;
                else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> setTemplate: Не задан параметр - содержимое шаблона");
            },

            loading: function (flag) {
                if (flag !== undefined) {
                    if (typeof flag === "boolean") {
                        isTemplateLoading = flag;
                        return isTemplateLoading;
                    } else
                        return $errors.add(ERROR_TYPE_DEFAULT, "$dateTimePicker -> loading: Неверно задан тип параметр - флаг процесса загрузки шаблона");
                } else
                    return isTemplateLoading;
            }


        }
    }
    
    
    
    
    function modelFieldDirective ($log) {
        return {
            restrict: "E",
            require: "ngModel",
            template:
                "<div class='ui-data-field {{ class }}'>" +
                    "<div class='input-container'><input type='text'  ng-model='ngModel' ng-change='onChange()'/></div>" +
                    "<div class='controls-container'>" +
                        "<button class='green' title='{{ okTitle }}' ng-class='{\"disabled\": isOkButtonDisabled === true}' ng-disabled='isOkButtonDisabled === true'>&#10003;</button>" +
                        "<button class='red' title='{{ cancelTitle }}' ng-click='cancel()'>&times;</button>" +
                    "</div>" +
                "</div>",
            scope: {
                ngModel: "=",
                //class: "@",
                modelFieldCancelTitle: "@",
                modelFieldOkTitle: "@",
                modelFieldOnCancel: "&",
                modelFieldOnChange: "&"
            },

            link: function (scope, element, attrs, ctrl) {
                scope.controller = ctrl;
                var okTitle = scope.okTitle = scope.modelFieldOkTitle !== undefined && scope.modelFieldOkTitle !== null ? scope.modelFieldOkTitle : "";
                var cancelTitle = scope.cancelTitle = scope.modelFieldCancelTitle !== undefined && scope.modelFieldCancelTitle !== null ? scope.modelFieldCancelTitle : "";
                var isChanged = scope.isChanged = false;
                var isOkButtonDisabled = scope.isOkButtonDisabled = true;
                var oldValue = angular.copy(scope.ngModel);

                
                
                scope.onChange = function () {
                    $log.log("changed");
                    isChanged = true;
                    scope.isOkButtonDisabled = false;
                    if (scope.modelFieldOnChange !== null && scope.modelFieldOnChange !== undefined && typeof scope.modelFieldOnChange === "function") {
                        scope.modelFieldOnChange();
                    }
                    $log.log("changed model = ", scope.ngModel);
                };
                
                

                scope.cancel = function () {
                    $log.log("onCancel");
                    if (scope.modelFieldOnCancel !== null && scope.modelFieldOnCancel !== undefined) {
                        scope.modelFieldOnCancel();
                    }
                    if (isChanged === true)
                        scope.ngModel = oldValue;
                    scope.isChanged = false;
                    scope.isOkButtonDisabled = true;
                };
            }

        }
    };
    
    
    
    
    function modelGridDirective ($log, $errors) {
        return {
            restrict: "E",
            require: "ngModel",
            template:
                "<table class='stripped'>" +
                    "<thead>" +
                        "<tr>" +
                            "<th ng-repeat='header in headers track by $index'>{{ header.title }}</th>" +
                        "</tr>" +
                    "</thead>" +
                    "<tbody>" +
                        "<tr ng-repeat='row in ngModel track by $index'>" +
                            "<td ng-repeat='header in headers track by $index'>{{ row[header.property].value }}</td>" +
                        "</tr>" +
                    "</tbody>" +
                "</table>",
            scope: {
                ngModel: "=",
                columns: "@"
            },
            link: function (scope, element, attrs, ctrl) {
                $log.log("ngModel = ", scope.ngModel);

                var headers = scope.headers = [];

                if (scope.ngModel !== null && scope.ngModel !== undefined) {
                    if (typeof scope.ngModel !== "object" && scope.ngModel.constructor !== "array")
                        return $errors.add(ERROR_TYPE_ENGINE, "krypton.ui -> model-grid -> Источник данных (ngModel) не является массивом");
                    else {
                        for (var property in scope.ngModel[0]) {
                            //$log.log(property + " = " + typeof scope.ngModel[0][property]);
                            //$log.log(property + " = " + scope.ngModel[0][property].constructor);
                            //if (typeof property === "object") {
                                if (scope.ngModel[0][property].constructor !== undefined && scope.ngModel[0][property].constructor === Field) {
                                    if (property !== "__class__" && property !== "init_functions" && property !== "_model_" && property !== "_states_" && property !== "_backup_") {
                                        var header = {
                                            title: "",
                                            property: property
                                        };
                                        if (scope.ngModel[0][property].displayable === true) {
                                            header.title = scope.ngModel[0][property].title !== "" ? scope.ngModel[0][property].title : property;
                                            headers.push(header);
                                        } else {
                                            header.title = property;
                                            scope.headers.push(property);
                                        }
                                    }
                                }
                            //} else {
                                //scope.headers.push(property);
                            //}
                        }
                        $log.log("headers = ", scope.headers);
                    }
                }
            }
        }
    };







    function columnsDirective () {
        return {
            restrict: "E",
            transclude: true,
            scope: {
                id: "@"
            },
            template:
                "<div class='krypton-columns'>" +
                    "<div class='columns-row' ng-transclude></div>" +
                "</div>",
            controller: function ($scope) {
                var columns = $scope.columns = [];

                this.add = function (column) {
                    column.isMaximized = false;
                    column.isMinimized = false;
                    columns.push(column);
                };

                this.maximize = function (id) {
                    if (id !== undefined) {
                        var length = columns.length;
                        for (var i = 0; i < length; i++) {
                            if (columns[i].id === id) {
                                columns[i].currentWidth = 100;
                                columns[i].isMaximized = true;
                                if (columns[i].onMaximize !== undefined && typeof columns[i].onMaximize === "function")
                                    columns[i].onMaximize();
                            } else {
                                columns[i].currentWidth = 0;
                                columns[i].isMinimized = true;
                            }
                        }
                    }
                };

                this.restore = function () {
                    var length = columns.length;
                    for (var i = 0; i < length; i ++) {
                        columns[i].currentWidth = columns[i].width;
                        columns[i].isMaximized = false;
                        columns[i].isMinimized = false;
                        if (columns[i].onMinimize !== undefined && typeof columns[i].onMinimize === "function")
                            columns[i].onMinimize();
                    }
                };

                //$columns.register($scope);

            }
        }
    };




    function columnDirective () {
        return {
            restrict: "E",
            require: "^columns",
            transclude: true,
            template: 
                "<div class='columns-column' style='width: {{currentWidth}}%;'>" +
                    "<div class='column-header' ng-show='isMinimized === false'>" +
                        "<div class='left'>" +
                            "<span class='header-caption' ng-show='showCaption === true'>{{ caption }}</span>" +
                            "<button class='small transparent' ng-show='showMaximizeButton === true && isMaximized === false' ng-click='max()' title='Развернуть колонку'><span class='fa fa-arrows-h' area-hidden='true'></span></button>" +
                            "<button class='small transparent' ng-show='isMaximized' ng-click='min()' title='Свернуть колонку'><span class='fa fa-long-arrow-right' area-hidden='true'></span></button>" +
                        "</div>" +
                        "<div class='right'>" + 
                            "<button ng-repeat='control in controls' ng-show='control.isVisible' class='{{ \"small rounded \" +  control.class }}' ng-click='control.action()' title='{{ control.title }}'><span class='{{ control.content }}'></span></button>" +
                        "</div>" +
                "   </div>" +
                    "<div class='column-content' ng-show='isMinimized === false' ng-transclude></div>" +
                "</div>",
            replace: true,
            scope: {
                id: "@",
                caption: "@",
                width: "@",
                maximizable: "@",
                onMaximize: "&",
                onMinimize: "&"
            },
            controller: function ($scope) {
                var controls = $scope.controls = [];

                this.addControl = function (control) {
                    if (control !== undefined) {
                        controls.push(control);
                    }
                };
            },
            link: function (scope, element, attrs, ctrl) {
                var showCaption = scope.showCaption = false;
                var showMaximizeButton = scope.showMaximizeButton = false;
                var currentWidth = scope.currentWidth = parseInt(scope.width);

                scope.showCaption = scope.caption !== undefined && scope.caption !== "" ? true : false;
                scope.showMaximizeButton = scope.maximizable !== undefined && scope.maximizable === "1" ? true : false;

                scope.scroll = function (anchor) {
                    if (anchor !== undefined) {
                        var column_content = angular.element(element).children()[1];
                        var anchor_element = document.getElementById(anchor);
                        if (anchor_element !== undefined && anchor_element !== null) {
                            column_content.scrollTop = anchor_element.offsetTop - 5;
                            return true;
                        } else
                            return false;
                    }
                };

                scope.max = function () {
                    ctrl.maximize(scope.id);
                };

                scope.min = function () {
                    ctrl.restore();
                };

                ctrl.add(scope);
            }
        }
    };



    function columnControlDirective () {
        return {
            restrict: "E",
            require: "^column",
            //template:
            //    "<button class='{{ class }}'><span class='{{ caption }}'></span></button>",
            transclude: true,
            scope: {
                content: "@",
                action: "=",
                class: "@",
                icon: "@",
                title: "@",
                ngShow: "="
            },
            link: function (scope, element, attrs, ctrl) {
                scope.isVisible = scope.ngShow !== undefined ? scope.ngShow : true;
                scope.$watch("ngShow", function (value) {
                    if (value !== undefined)
                        scope.isVisible = value;
                });
                ctrl.addControl(scope);
            }
        }
    };




    function hierarchyFactory ($log, $errors) {
        var items = [];

        return {

            register: function (scope) {
                if (scope === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$hierarchy -> register: Не задан параметр - scope регистрируемой иерархии");
                    return false;
                }

                items.push(scope);
                $log.info("registered", items);

                return true;
            },

            getById: function (id) {
                if (id === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$hierarchy -> get: Не задан параметр - идентификатор иерархического списка");
                    return false;
                }

                var length = items.length;
                for (var i = 0; i < length; i++) {
                    if (items[i].id === id)
                        return items[i];
                }

                return false;
            },

            add: function (hierarchyId, item) {
                if (hierarchyId === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$hierarchy -> add: Не задан параметр - идентификатор иерархического списка");
                    return false;
                }

                if (item === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$hierarchy -> add: Не задан параметр - добавляемый элемент иерархического списка");
                    return false;
                }


                var hierarchy = this.getById(hierarchyId);
                if (hierarchy === false) {
                    $errors.add(ERROR_TYPE_ENGINE, "$hierarchy -> add: Иерархический список с идентификатором '" + hierarchyId + "' не найден");
                    return false;
                }

                if (item[hierarchy.key] === undefined) {
                    $errors.add(ERROR_TYPE_ENGINE, "$hierarchy -> add: Добавляемый элемент иерархического списка не содержит ключевого поля (" + hierarchy.key + ")");
                    return false;
                }

                if (item[hierarchy.parentKey] === undefined) {
                    $errors.add(ERROR_TYPE_ENGINE, "$hierarchy -> add: Добавляемый элемент иерархического списка не содержит ключевого поля (" + hierarchy.parentKey + ")");
                    return false;
                }

                item._hierarchy_ = {};
                item._hierarchy_.expanded = false;
                item._hierarchy_.havChildren = false;
                item._hierarchy_.children = [];

                hierarchy.source.push(item);
                return true;

                //var length = hierarchy.source.length;
                //for (var x = 0; x < length; x++) {
                //    var current = hierarchy.source[x];
                //    var currentKey = current[hierarchy.key].constructor === Field ? current[hierarchy.key].value : current[hierarchy.key];
                //    if (currentKey === itemId) {
                //        current._hierarchy_.children.push(item);
                //        return true;
                //    }
                //}

                //$errors.add(ERROR_TYPE_ENGINE, "$hierarchy -> add: Элемент иерархического списка с идентификатором '" + itemId + "' не найден");
                //return false;



            },

            update: function (hierarchyId, itemIKey) {
                if (hierarchyId == undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$hierarchy -> update: Не задан параметр - идентификатор иерархического списка");
                    return false;
                }


                var length = items.length;
                for (var i = 0; i < length; i++) {
                    if (items[i].id === hierarchyId) {

                        if (itemKey === undefined) {

                        } else {
                            
                        }

                    }
                }
            }
        }
    };




    function hierarchyDirective ($log, $errors, $compile, $templateCache, $parse, $hierarchy, $sce) {


        return {
            restrict: "E",
            scope: {
                source: "=",
                class: "@",
                key: "@",
                parentKey: "@",
                displayField: "@",
                onSelect: "="
            },
            template:
                /*
                "<ul class='{{ \"krypton-ui-hierarchy root \" + class }}'>" +
                    "<li ng-repeat='node in initial track by $id(node)' ng-init='this.children = getChildren(node)' ng-click='select(node)'>" +
                        "<div class='item-controls'>" +
                            "<span class='expand fa fa-plus-circle' ng-click='expand(node)' ng-show='node.children.length > 0 && node.expanded === false'></span>" +
                            "<span class='collapse fa fa-minus-circle' ng-if='node.expanded === true' ng-click='collapse(node)'></span>" +
                        "</div>" +
                        "<div class='item-content'>{{ node.display }}</div>" +

                        "<div  ng-include=\"\'hierarchy.html'\"></div>" +
                    "</li>" +
                "</ul>",
                */
                "<div class='krypton-ui-tree'>" +
                    "<div class='container root'>" +
                        "<div class='tree-item' ng-class='{ \"with-children\": node._hierarchy_.haveChildren === true, \"expanded\": node._hierarchy_.expanded === true, \"active\": node._states_.selected() === true }' ng-repeat='node in initial track by $index'>" +
                            //"<div class='item-lines'>" +
                            //    "<div class='top'></div>" +
                            //    "<div class='bottom'></div>" +
                            //"</div>" +
                            //"<div class='item-controls'>" +
                            //    "<span class='expand fa fa-plus-circle' ng-click='expand(node)' ng-show='node.children.length > 0 && node.expanded === false'></span>" +
                            //    "<span class='collapse fa fa-minus-circle' ng-if='node.expanded === true' ng-click='collapse(node)'></span>" +
                            //"</div>" +
                            //"<div class='item-content'>{{ node.display }}</div>" +
                            //"<div ng-include=\"\'hierarchy.html'\"></div>" +
                            "<div class='tree-item-content' ng-click='expand(node)'>" +
                                "<div class='item-label' ng-class='{ \"active\": node._states_.selected() === true }' ng-click='select(node, $event)'>" +
                                "<span ng-if='node[displayField].type !== undefined'>{{ node[displayField].value }}</span>" +
                                "<span ng-if='node[displayField].type === undefined'>{{ node[displayField] }}</span>" +
                                "</div>" +
                                "<div class='item-controls'>" +
                                    "<span class='expand fa fa-chevron-down' ng-click='expand(node)' ng-show='node._hierarchy_.haveChildren === true && node._hierarchy_.expanded === false'></span>" +
                                    "<span class='collapse fa fa-chevron-up' ng-if='node._hierarchy_.expanded === true' ng-click='collapse(node)'></span>" +
                                "</div>" +

                            "</div>" +
                            "<div ng-include=\"\'hierarchy'\" ng-show='node._hierarchy_.expanded === true'></div>" +
                            //"<div ng-bind-html='parsedTemplate' ng-show='node.expanded === true'></div>" +
                        "</div>" +
                    "</div>" +
                "</div>",
            controller: function ($scope) {
                this.items = [];

                $log.log("HIERARCHY CONTROLLER START", this.items);

                this.register = function (scope) {
                    if (scope === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> hierarchy -> controller -> register: Не задан параметр - scope регистрируемой иерархии");
                        return false;
                    }

                    this.items.push(scope);
                    $log.info("registered", this.items);

                    return true;
                };


                this.findById = function (id) {
                    if (id === undefined) {
                        $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> hierarchy -> controller -> findById: Не задан параметр - идентификатор иерархического списка");
                        return false;
                    }

                    var length = this.items.length;
                    for (var i = 0; i < length; i++) {
                        if (this.items[i].id === id)
                            return this.items[i];
                    }

                    return false;
                };


                this.redraw = function () {

                };
            },
            link: function (scope, element, attrs, ctrl) {

                var template =
                    "<div class='container nested' >" +
                    "<div class='tree-item' ng-class='{ \"with-children\": node._hierarchy_.haveChildren === true, \"expanded\": node._hierarchy_.expanded === true, \"active\": node._states_.selected() === true }' ng-repeat='node in node._hierarchy_.children track by $index'>" +
                    //"<div class='item-lines'>" +
                    //    "<div class='top'></div>" +
                    //    "<div class='bottom'></div>" +
                    //"</div>" +
                    //"<div class='item-controls'>" +
                    //    "<span class='expand fa fa-plus-circle' ng-click='expand(node)' ng-show='node.children.length > 0 && node.expanded === false'></span>" +
                    //    "<span class='collapse fa fa-minus-circle' ng-if='node.expanded === true' ng-click='collapse(node)'></span>" +
                    //"</div>" +
                    //"<div class='item-content'>{{ node.display }}</div>" +
                    //"<div ng-init='this.children = getChildren(node)' ng-include=\"\'hierarchy.html'\"></div>" +
                    "<div class='tree-item-content' ng-click='expand(node)'>" +
                    "<div class='item-label' ng-class='{ \"active\": node._states_.selected() === true }' ng-click='select(node, $event)'>{{ node.display }}</div>" +
                    "<div class='item-controls'>" +
                    "<span class='expand fa fa-chevron-down' ng-click='expand(node)' ng-show='node._hierarchy_.haveChildren === true && node._hierarchy_.expanded === false'></span>" +
                    "<span class='collapse fa fa-chevron-up' ng-if='node._hierarchy_.expanded === true' ng-click='collapse(node)'></span>" +
                    "</div>" +
                    "</div>" +
                    "<div ng-show='node._hierarchy_.expanded === true' ng-include=\"\'hierarchy'\"></div>" +
                    //"<div ng-init='this.children = getChildren(node)' ng-bind-html='parsedTemplate' ng-show='node.expanded === true'></div>" +
                    "</div>" +
                    "</div>";

                var parsedTemplate = scope.parsedTemplate = template;
                //$log.log("parsed = ", parsedTemplate);

                var current = scope.current = undefined;

                scope.$watch("current", function (newVal, oldVal) {
                    //$log.log("hierarchy length = ", newVal.length);
                    //$log.log("collection changed");
                    //$log.log("old = ", oldVal.length, ", new = ", newVal.length);
                    //if (newVal !== oldVal) {
                        //$log.log("old = ", oldVal, ", new = ", newVal);
                    $log.log("current = ", scope.current);
                    //$log.log("new = ", newVal);
                    //if (oldVal !== newVal) {
                    //    $log.log("not equal");
                    //    init();
                    //}
                        //$compile(element)(scope);
                       // scope.$digest();
                        //scope.source = val;
                    //}
                });


                scope.$watchCollection("current._hierarchy_.children", function (val) {
                   $log.log("children of current changed");
                });

                /*
                var template =
                    "<ul class='{{ \"krypton-ui-hierarchy nested \" + class }}' ng-if='node.expanded === true'>" +
                        "<li ng-repeat='node in children' ng-init='children = getChildren(node)' ng-click='select(node)'>" +
                    "<div class='item-lines'>" +
                    "<div class='lines-top'></div><div class='lines-bottom'></div>" +
                    "</div>" +
                    "<div class='item-controls'>" +
                    "<span class='expand fa fa-plus-circle' ng-class='{\"invisible\": node.children.length === 0}' ng-if='node.expanded === false' ng-click='expand(node)'></span>" +
                    "<span class='collapse fa fa-minus-circle' ng-if='node.expanded === true' ng-click='collapse(node)'></span>" +
                    "</div>" +
                    "<div class='item-content'>{{ node.display }}</div>" +
                            "<div ng-init='this.children = getChildren(node)' ng-include=\"\'hierarchy.html'\"></div>" +
                        "</li>" +
                    "</ul>";
                */


                if (attrs.id === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> hierarchy directive: Не задан параметр - идентификатор иерархического списка (аттрибут id)");
                    return false;
                }

                if (attrs.source === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> hierarchy directive: Не задан параметр - источник данных иерархического списка (аттрибут source)");
                    return false;
                }

                $log.log("source = ", scope.source);

                if (scope.source.constructor === undefined || scope.source.constructor !== Array) {
                    $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> hierarchy directive: Источник данных не является массивом");
                    return false;
                }


                $templateCache.put("hierarchy", template);
                var id = scope.id = attrs.id;
                var stack = scope.stack = [];
                var initial = scope.initial = [];

                var findByKey = function (key) {
                    if (key !== undefined) {
                        var length = scope.stack.length;
                        for (var i = 0; i < length; i++) {
                            if (scope.stack[i][scope.key] !== undefined) {
                                if (scope.stack[i][scope.key] === key)
                                    return scope.stack[i];
                            }
                        }
                        return false;
                    }
                };


                scope.getChildren = function (node) {
                    if (node !== undefined) {
                        var result = [];
                        var length = scope.source.length;
                        for (var i = 0; i < length; i++) {
                            var temp = scope.source[i];
                            var nodeKey = node[scope.key].constructor === Field ? node[scope.key].value : node[scope.key];
                            var parentKey = temp[scope.parentKey].constructor === Field ? temp[scope.parentKey].value : temp[scope.parentKey];
                            if (parentKey === nodeKey) {
                                result.push(scope.source[i]);
                            }
                        }
                        $log.log(node.id.value + " children = ", result);
                        return result;
                    }
                };


                scope.expand = function (node) {
                    if (node !== undefined) {
                        var length = scope.source.length;
                        for (var i = 0; i < length; i ++) {
                            var nodeKey = node[scope.key].constructor === Field ? node[scope.key].value : node[scope.key];
                            var tempKey = scope.source[i][scope.key].constructor === Field ? scope.source[i][scope.key].value : scope.source[i][scope.key];
                            if (nodeKey === tempKey) {
                                if (node._hierarchy_.haveChildren === true && node._hierarchy_.expanded === false)
                                    scope.source[i]._hierarchy_.expanded = true;
                                else if (node._hierarchy_.haveChildren === true && node._hierarchy_.expanded === true)
                                    this.collapse(node);
                            }

                        }
                    }
                };

                
                scope.collapse = function (node) {
                    if (node !== undefined) {
                        var length = scope.source.length;
                        for (var i = 0; i < length; i ++) {
                            var nodeKey = node[scope.key].constructor === Field ? node[scope.key].value : node[scope.key];
                            var tempKey = scope.source[i][scope.key].constructor === Field ? scope.source[i][scope.key].value : scope.source[i][scope.key];
                            if (nodeKey === tempKey)
                                scope.source[i]._hierarchy_.expanded = false;
                        }
                    }
                };


                scope.select = function (node, event) {

                    event.stopPropagation();
                    if (node !== undefined) {
                        $log.log(node);
                        var length = scope.source.length;
                        for (var i = 0; i < length; i++) {
                            //if (angular.equals(node, stack[i])) {
                            var nodeId = node[scope.key].constructor === Field ? node[scope.key].value : node[scope.key];
                            var stackId = scope.source[i][scope.key].constructor === Field ? scope.source[i][scope.key].value : scope.source[i][scope.key];
                            if (nodeId === stackId){
                                $log.log("equals");

                                //if (node._hierarchy_.haveChildren === true) {
                                //    if (node._hierarchy_.expanded === false)
                                //        this.expand(node);
                                //    else {
                                        //if (node._states_.selected() === false) {
                                        //    node._states_.selected(true);
                                        //} else
                                        //    node._states_.selected(false);
                                //        this.collapse(node);
                                //    }
                                //} else {
                                    if (scope.source[i]._states_.selected() === true) {
                                        scope.source[i]._states_.selected(false);
                                        scope.current = undefined;
                                        if (scope.onSelect !== undefined)
                                            scope.onSelect(undefined);
                                    } else {
                                        scope.source[i]._states_.selected(true);
                                        if (scope.onSelect !== undefined) {
                                            scope.onSelect(node);
                                            scope.current = node;
                                        }
                                    }
                                //}


                            } else {
                               
                                    scope.source[i]._states_.selected(false);
                            }
                        }
                    }
                    //node._states_.selected(true);
                    
                };


                var isInStack = function (id) {
                    if (id !== undefined) {
                        var length = scope.stack.length;
                        for (var i = 0; i < length; i++) {
                            var item = scope.stack[i];
                            var itemId = item[scope.key].constructor === Field ? item[scope.key].value : item[scope.key];
                            if (itemId === id)
                                return scope.stack[i];
                        }
                        return false;
                    }
                };


                var init = function () {
                    //$log.log("source = ", scope.source);
                    //stack.splice(0, stack.length);
                    initial.splice(0, initial.length);

                    //scope.source = $parse(scope.source);
                    //$log.log("ngModel = ", scope.source);

                    if (scope.source.constructor === undefined || scope.source.constructor !== Array) {
                        $errors.add(ERROR_TYPE_ENGINE, "krypton.ui -> hierarchy -> Источник данных (ngModel) не является массивом");
                        return false;
                    }

                    if (scope.key === undefined && scope.key === "") {
                        $errors.add(ERROR_TYPE_ENGINE, "krypton.ui -> hierarchy -> Не задан аттрибут - поле связи иерархии");
                        return false;
                    }

                    if (scope.parentKey === undefined && scope.parentKey === "") {
                        $errors.add(ERROR_TYPE_ENGINE, "krypton.ui -> hierarchy -> Не задан аттрибут - поле родительской связи");
                        return false;
                    }

                    var length = scope.source.length;
                    for (var i = 0; i < length; i++) {
                        var temp = scope.source[i];

                        if (temp[scope.key] == undefined) {
                            $log.info("krypton.ui -> hierarchy -> Не найдено поле связи в источнике данных (" + scope.key + ")");
                            continue;
                        }
                        var firstKey = temp[scope.key].constructor === Field ? temp[scope.key].value : temp[scope.key];

                        if (temp[scope.parentKey] === undefined) {
                            $log.info("krypton.ui -> hierarchy -> Не найдено поле связи в источнике данных (" + scope.key + ")");
                            continue;
                        }
                        var firstParentKey = temp[scope.parentKey].constructor === Field ? temp[scope.parentKey].value : temp[scope.parentKey];

                        if (temp._hierarchy_ !== undefined) {
                            temp._hierarchy_.children.splice(0, temp._hierarchy_.children.length);
                            temp._hierarchy_.havChildren = false;
                        } else {
                            temp._hierarchy_ = {};
                            temp._hierarchy_.expanded = false;
                            temp._hierarchy_.haveChildren = false;
                            temp._hierarchy_.children = [];
                        }


                        if (temp[scope.displayField] !== undefined && temp[scope.displayField] !== "") {
                            temp.display = temp[scope.displayField].constructor === Field ? temp[scope.displayField].value : temp[scope.displayField];
                        }

                        if (firstParentKey === 0 || firstParentKey === "")
                            scope.initial.push(temp);


                        for (var x = 0; x < length; x ++) {
                            var temp2 = scope.source[x];
                            var secondParentKey = temp2[scope.parentKey].constructor === Field ? temp2[scope.parentKey].value : temp2[scope.parentKey];

                            if (firstKey === secondParentKey) {
                                temp._hierarchy_.children.push(temp2);
                                temp._hierarchy_.haveChildren = true;
                            }

                        }

                        //scope.stack.push(temp);

                    }
                };

                var update = function () {
                    var length = scope.source.length;
                    for (var i = 0; i < length; i++) {
                        var temp = scope.source[i];
                        var firstKey = temp[scope.key].constructor === Field ? temp[scope.key].value : temp[scope.key];
                        var firstParentKey = temp[scope.parentKey].constructor === Field ? temp[scope.parentKey].value : temp[scope.parentKey];
                        for (var x = 0; x < length; x++) {

                        }

                    }
                };

                //$compile(scope.parsedTemplate)(scope);
                //$compile(element)(scope);
                init();
                //$hierarchy.register(scope);
                //ctrl.register(scope);

                var item = $hierarchy.getById(attrs.id);
                $log.log("item = ", item);
                //if (item !== false) {
                    //scope = item;
                    //scope.$apply();
                //    scope.initial = item.initial;
                //    scope.stack = item.stack;
                //} else {
                    //init();
                //    $hierarchy.register(scope);
                //}

            }
        }
    };




    function modelListDirective ($log, $errors) {
        return {
            restrict: "E",
            require: "ngModel",
            template:
                "<div class='krypton-ui-model-list'>" +
                    "<div class='model-item' ng-class='{\"selected\" : model._states_.selected() === true}' ng-repeat='model in ngModel track by $id(model)' ng-click='select(model)'>" +
                        "<div class='model-item-icon'></div>" +
                        "<div class='model-item-content'>" +
                            "<div class='model-item-model item-primary-label' ng-if='primaryLabel !== undefined && primaryLabel !== \"\" && model[primaryLabel] !== undefined'>{{ model[primaryLabel].value }}</div>" +
                            "<div class='model-item-secondary-label' ng-if='secondaryLabel !== undefined && secondaryLabel !== \"\" && model[secondaryLabel] !== undefined'>{{ model[secondaryLabel].value }}</div>" +
                        "</div>" +
                    "</div>" +
                "</div>",
            scope: {
                ngModel: "=",
                modelId: "@",
                primaryLabel: "@",
                secondaryLabel: "@",
                onSelect: "="
            },
            link: function (scope, element, attrs, ctrl) {

                if (scope.ngModel.constructor !== undefined && scope.ngModel.constructor === Array ) {

                } else
                    return $errors.add(ERROR_TYPE_ENGINE, "krypton.ui -> model-list ->  Источник данных (ngModel) не является массивом");

                scope.select = function (model) {
                    if (scope.modelId !== undefined && scope.modelId !== "") {
                        if (model[scope.modelId] !== undefined) {
                            var selectedModelIdValue = model[scope.modelId].constructor === Field ? model[scope.modelId].value : model[scope.modelId];
                            var length = scope.ngModel.length;
                            for (var i = 0; i < length; i++) {
                                if (scope.ngModel[i][scope.modelId] !== undefined) {
                                    var modelIdValue = scope.ngModel[i][scope.modelId].constructor === Field ? scope.ngModel[i][scope.modelId].value : scope.ngModel[i][scope.modelId];
                                    if (modelIdValue === selectedModelIdValue) {
                                        if (scope.ngModel[i]._states_ !== undefined)
                                            scope.ngModel[i]._states_.selected(true);
                                        if (scope.onSelect !== undefined)
                                            scope.onSelect(model);
                                    } else
                                        if (scope.ngModel[i]._states_ !== undefined)
                                            scope.ngModel[i]._states_.selected(false);
                                }
                            }
                        }
                    }
                };
                
            }
        }
    };
    
    
    
    function modalsFactory ($log, $errors, $compile, $window) {
        var items = [];
        var currentModal = undefined;

        var redraw = function (elm) {
            if (elm !== undefined) {
                var left = ($window.innerWidth / 2) - angular.element(elm).prop("clientWidth") / 2 + "px";
                var top = ($window.innerHeight / 2) - ((angular.element(elm).prop("clientHeight")) / 2) + "px";

                angular.element(elm).css("left", left);
                angular.element(elm).css("top", top);
            }
        };

        return {

            /**
             * Возвращает массив со всеми модальными окнами
             * @returns {Array}
             */
            getAll: function () {
                return items;
            },

            /**
             * Возвращает scope модального окна с заданным идентификатором
             * @param id {string} - идентификатор модального окна
             * @returns {scope}
             */
            getById: function (id) {
                if (id === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$modals -> getById: Не задан параметр - идентификатор модального окна");
                    return false;
                }

                var length = items.length;
                for (var i = 0; i < length; i++) {
                    if (items[i].id === id)
                        return items[i];
                }

                return false;
            },

            /**
             * Регистирует модальное окно
             * @param scope {scope} - scope добавляемого модального окна
             * @returns {boolean}
             */
            register: function (modal) {
                if (modal === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$modals -> register: Не задан парметр - объект модального окна");
                    return false;
                }

                items.push(modal);
                //$log.log("modals = ", items);

                return true;
            },

            /**
             * Открывает модальное окно с заданным идентификатором
             * @param id {string} - идентификатор модального окна
             * @returns {boolean}
             */
            open: function (id) {
                if (id === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "$modals -> open: Не задан параметр - идентификатор модального окна");
                    return false;
                }

                var length = items.length;
                var found = false;
                for (var i = 0; i < length; i++) {
                    if (items[i].id === id) {
                        found = true;
                        currentModal = items[i];
                        var modal = document.getElementsByClassName("krypton-ui-modal")[0];
                        var header = document.getElementById("krypton-ui-modal-caption");
                        var content = document.getElementById("krypton-ui-modal-content");
                        header.innerHTML = items[i].caption;
                        content.innerHTML = items[i].content;
                        $compile(modal)(items[i].scope);
                        angular.element(modal).css("display", "block");

                        var fog = document.getElementsByClassName("krypton-ui-fog");
                        document.body.style.overflow = "hidden";
                        fog[0].classList.add("visible");
                        redraw(modal);

                        if (items[i].width !== 0)
                            angular.element(modal).css("width", items[i].width + "px");
                        if (items[i].height !== 0)
                            angular.element(modal).css("height", items[i].height + "px");

                        modal.addEventListener("DOMSubtreeModified", function () {
                            redraw(modal);
                        }, false);

                        angular.element($window).bind("resize", function () {
                            redraw(modal);
                        });
                    }
                }

                if (found === false) {
                    $errors.add(ERROR_TYPE_ENGINE, "$modals -> open: Модальное окно с идентификатором '" + id + "' не найдено");
                    return false;
                } else
                    return true;
            },

            /**
             * Закрывает модальное окно с заданным идентификатором
             * @returns {boolean}
             */
            close: function () {
                if (currentModal !== undefined) {
                    var modal = document.getElementsByClassName("krypton-ui-modal")[0];
                    if (currentModal.onClose !== undefined)
                        currentModal.onClose();
                    var fog = document.getElementsByClassName("krypton-ui-fog");
                    document.body.style.overflow = "hidden";
                    fog[0].classList.remove("visible");
                    angular.element(modal).css("display", "none");
                    currentModal = undefined;
                    return true;
                }

                return false;
            }
        }
    };
    
    
    
    function  modalDirective ($log, $modals, $compile, $errors, $window, $sce) {
        return {
            restrict: "A",
            scope: false,
            link: function (scope, element, attrs, ctrl) {
                if (attrs.modalId === undefined) {
                    $errors.add(ERROR_TYPE_DEFAULT, "krypton.ui -> modal: Не задан идентификатор модального окна - аттрибут 'modal-id'");
                    return false;
                }


                var modal  = {
                    id: attrs.modalId,
                    isVisible: false,
                    caption: attrs.modalCaption !== undefined ? attrs.modalCaption : "",
                    content: $sce.trustAsHtml(element[0].innerHTML),
                    scope: scope,
                    width: attrs.modalWidth !== undefined && !isNaN(attrs.modalWidth) ? parseInt(attrs.modalWidth) : 0,
                    height: attrs.modalHeight !== undefined && !isNaN(attrs.modalHeight) ? parseInt(attrs.modalHeight) : 0,
                    onClose: attrs.modalOnClose !== undefined && typeof scope.$eval(attrs.modalOnClose) === "function" ? scope.$eval(attrs.modalOnClose) : undefined,
                    onOpen: attrs.modalOnOpen !== undefined && typeof scope.$eval(attrs.modalOnOpen) === "function" ? scope.$eval(attrs.modalOnOpen) : undefined
                };


                element[0].innerHTML = "";
                element[0].classList.add("ng-hide");
                $modals.register(modal);


                var check = document.getElementsByClassName("krypton-ui-modal");
                if (check.length === 0) {
                    var modal = document.createElement("div");
                    modal.className = "krypton-ui-modal";
                    var header = document.createElement("div");
                    header.className = "modal-header";
                    var headerCaption = document.createElement("span");
                    headerCaption.setAttribute("id", "krypton-ui-modal-caption");
                    headerCaption.className = "modal-caption";
                    var headerClose = document.createElement("span");
                    headerClose.className = "modal-close fa fa-times right";
                    headerClose.setAttribute("title", "Закрыть");
                    headerClose.setAttribute("ng-click", "modals.close()");
                    header.appendChild(headerCaption);
                    header.appendChild(headerClose);
                    var content = document.createElement("div");
                    content.setAttribute("id", "krypton-ui-modal-content");
                    content.className = "modal-content";
                    document.body.appendChild(modal);
                    modal.appendChild(header);
                    modal.appendChild(content);
                }


                var fog = document.getElementsByClassName("krypton-ui-fog");
                if (fog.length === 0) {
                    var fogElement = document.createElement("div");
                    fogElement.className = "krypton-ui-fog";
                    document.body.appendChild(fogElement);
                }
            }
        }    
    };
    
    
    
    
    
    function centeredDirective ($window, $log) {
        return {
            restrict: "A",
            link: function (scope, element, attrs) {

                var redraw = function () {
                    var left = ($window.innerWidth / 2) - angular.element(element).prop("clientWidth") / 2 + "px";
                    var top = ($window.innerHeight / 2) - ((angular.element(element).prop("clientHeight")) / 2) + "px";
                    angular.element(element).css("left", left);
                    angular.element(element).css("top", top);
                };


                angular.element($window).bind("resize", function () {
                    redraw();
                });

                redraw();

                element[0].addEventListener("DOMSubtreeModified", function () {
                    redraw();
                }, false);


            }
        }
    };

})();