"use strict";



(function () {
    angular
        .module("krypton.session", [])
        .factory("$session", sessionFactory)
        .run(kryptonSessionRun);



    function kryptonSessionRun ($log, $classes, $session, $factory) {
        $log.log("krypton.session run...");

        $classes.add("CurrentUser", {
            id: new Field({ source: "id", type: "integer", value: 0, default_value: 0, backupable: true}),
            name: new Field({ source: "name", type: "string", value: "", default_value: "", backupable: true }),
            fname: new Field({ source: "fname", type: "string", value: "", default_value: "", backupable: true }),
            //position: new Field({ source: "position", type: "string", value: "", default_value: "", backupable: true }),
            email: new Field({ source: "email", type: "string", value: "", default_value: "", backupable: true }),
            phone: new Field({ source: "phone", type: "string", value: "", default_value: "", backupable: true }),
            isAdmin: new Field({ source: "is_admin", type: "boolean", value: false, default_value: false, backupable: true })
        });

        $classes.add("CurrentSession", {
            userId: new Field({ source: "user_id", type: "integer", value: 0, default_value: 0, backupable: true }),
            token: new Field({ source: "token", type: "string", value: "", default_value: "", backupable: true }),
            start: new Field({ source: "start", type: "integer", value: 0, default_value: 0, backupable: true }),
            end: new Field({ source: "end", type: "integer", value: 0, default_value: 0, backupable: true })
        });

    };



    /******************************
     * $session
     * Сервис текущей сессии приложения
     * @param $log
     ******************************/
    function sessionFactory ($log, $factory) {
        var session = undefined;
        var user = undefined;

        return {
            /**
             * Возвращает объект текущей сессии приложения
             * @returns {undefined}
             */
            getCurrentSession: function () {
                return session;
            },

            /**
             * Устанавливает текущую сессию приложения
             * @param newSession - Объект, содержащий информацию о сессии
             * @returns {boolean} - Возвращает true в случае успех, иначе - false
             */
            setCurrentSession: function (newSession) {
                if (newSession !== undefined) {
                    session = $factory({ classes: ["CurrentSession", "Model", "Backup"], base_class: "CurrentSession" });
                    session._model_.fromAnother(newSession);
                    return true;
                } else
                    return false;
            },

            /**
             * Возвращает объект текущего пользователя приложения
             * @returns {undefined}
             */
            getCurrentUser: function () {
                return user;
            },

            /**
             * Устанавливает текущего пользователя приложения
             * @param newUser - Объект, содержащий информацию о пользователе
             * @returns {boolean} - Возвращает true в случае успех, иначе - false
             */
            setCurrentUser: function (newUser) {
                if (newUser !== undefined) {
                    user = $factory({ classes: ["CurrentUser", "Model", "Backup"], base_class: "CurrentUser" });
                    user._model_.fromAnother(newUser);
                    return true;
                } else
                    return false;
            }
        }

    };
})();
