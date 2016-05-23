(function () {
    angular
        .module("krypton.ui", [])
            .factory("$calendar", calendarFactory)
            .directive("uiCalendar", calendarDirective);
    angular.module("krypton.ui").run(kryptonUIRun);
    
    
    
    function kryptonUIRun ($log, $classes) {
        $log.log("krypton.ui run...");
        /**
         * Calendar
         * Набор свойств и методов, описывающих элемент интрефейса - календарь
         */
        $classes.add("Calendar", {
            __dependencies__: [],
            id: 0,
            parentElement: 0,
            modelValue: 0,
            isVisible: false,
            isModal: false
        });
    };



    function calendarDirective () {
        return {
            restrict: "A",
            requires: "ngModel",
            templateUrl: "templates/ui/calendar.html",
            scope: {
                //id: 0,
                //isVisible: false,
                //isModal: false
                uiCalendarModelValue: "&"
            },
            link: function (scope, element, attrs, controller) {
                var days = scope.days = new Array(35);
                var weekdays = scope.weekdays = ["Пн", "Вт", "Ср", "Чт" , "Пт", "Сб", "Вс"];
            }
        }
    };


    function calendarFactory ($log, $window, $document, $errors, $factory, $compile, $rootScope) {
        var instances = [];


        
        return {

            add: function (parameters) {
                if (parameters !== undefined) {
                    if (typeof parameters === "object") {
                        var tempCalendar = $factory({classes: ["Calendar"], base_class: "Calendar"});
                        for (var param in parameters) {
                            if (tempCalendar.hasOwnProperty(param)) {
                                switch (param) {
                                    case "modelValue":
                                        tempCalendar.modelValue = parameters[param];
                                        break;
                                    case "isModal":
                                        if (typeof parameters[param] === "boolean")
                                            tempCalendar.isModal = parameters[param];
                                        else
                                            return $errors.add(ERROR_TYPE_DEFAULT, "$calendar -> add: Неверно задан тип параметра - модальный режим");
                                        break;
                                }
                            }
                        }
                        tempCalendar.id = "calendar" + instances.length;
                        instances.push(tempCalendar);
                        $log.info("cal = ", tempCalendar);
                        
                        var calendarBody = document.createElement("div");
                        calendarBody.className = "krypton-ui-calendar";
                        calendarBody.setAttribute("ui-calendar-model-value", tempCalendar.modelValue);
                        calendarBody.setAttribute("ui-calendar", "");
                        document.body.appendChild(calendarBody);
                        $compile(calendarBody)($rootScope.$new());

                        return tempCalendar;
                    } else 
                        return $errors.add(ERROR_TYPE_DEFAULT, "$calendar -> add: Неверно задан тип параметра инициализации");                     
                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$calendar -> add: Не заданы параметры инициализации");
            },

            open: function (instance) {
                if (instance !== undefined) {

                } else
                    return $errors.add(ERROR_TYPE_DEFAULT, "$calendar -> open: Не задан параметр - объект календаря");
            }


        }
    }
})();