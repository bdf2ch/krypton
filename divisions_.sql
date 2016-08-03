-- phpMyAdmin SQL Dump
-- version 4.6.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Авг 03 2016 г., 11:36
-- Версия сервера: 5.5.23
-- Версия PHP: 5.5.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `krypton`
--

--
-- Дамп данных таблицы `divisions`
--

INSERT INTO `divisions` (`ID`, `TITLE`, `DEPARTMENT_ID`, `PARENT_ID`, `ORGANIZATION_ID`, `PATH`) VALUES
(-1, 'Филиал ПАО «МРСК Северо-Запада» «Колэнерго»', 0, 0, 8, '-1\\'),
(4, 'ПО «Северные электрические сети»', 0, -1, 8, '-1\\4\\'),
(5, 'ПО «Центральные электрические сети»', 0, -1, 8, '-1\\5\\'),
(6, 'ПО «Аппарат управления»', 0, -1, 8, '-1\\6\\'),
(46, 'Руководство филиала', 0, 6, 8, '-1\\6\\46\\'),
(47, 'Управление экономики и тарифообразования', 0, 6, 8, '-1\\6\\47\\'),
(48, 'Отдел бизнес-планирования', 0, 47, 8, '-1\\6\\47\\48\\'),
(49, 'Сектор тарифообразования', 0, 47, 8, '-1\\6\\47\\49\\'),
(50, 'Финансовый отдел', 0, 6, 8, '-1\\6\\50\\'),
(53, 'Сектор страховой защиты', 0, 50, 8, '-1\\6\\50\\53\\'),
(54, 'Сектор финансов', 0, 50, 8, '-1\\6\\50\\54\\'),
(55, 'Сектор бюджетного контроля', 0, 50, 8, '-1\\6\\50\\55\\'),
(56, 'Управление бухгалтерского и налогового учета и отчетности', 0, 6, 8, '-1\\6\\56\\'),
(57, 'Отдел внутренней бухгалтерской и налоговой отчетности', 0, 56, 8, '-1\\6\\56\\57\\'),
(58, 'Отдел учета расчетов с контрагентами', 0, 56, 8, '-1\\6\\56\\58\\'),
(59, 'Отдел учета имущества', 0, 56, 8, '-1\\6\\56\\59\\'),
(60, 'Отдел учета расчетов с персоналом', 0, 56, 8, '-1\\6\\56\\60\\'),
(61, 'Сектор договорной работы', 0, 56, 8, '-1\\6\\56\\61\\'),
(62, 'Управление правового обеспечения и управления собственностью', 0, 6, 8, '-1\\6\\62\\'),
(63, 'Отдел правового обеспечения', 0, 62, 8, '-1\\6\\62\\63\\'),
(64, 'Отдел управления собственностью', 0, 62, 8, '-1\\6\\62\\64\\'),
(65, 'Управление реализации услуг и учета электроэнергии, энергосбережения и повышения энергоэффективности', 0, 6, 8, '-1\\6\\65\\'),
(66, 'Отдел реализации услуг', 0, 65, 8, '-1\\6\\65\\66\\'),
(67, 'Отдел балансов и учета электроэнергии', 0, 65, 8, '-1\\6\\65\\67\\'),
(68, 'Сектор мониторинга и оптимизации потерь', 0, 65, 8, '-1\\6\\65\\68\\'),
(69, 'Сектор энергосбережения и повышения энергоэффективности', 0, 65, 8, '-1\\6\\65\\69\\'),
(70, 'Отдел взаимодействия с клиентами', 0, 6, 8, '-1\\6\\70\\'),
(71, 'Центр обслуживания клиентов', 0, 70, 8, '-1\\6\\70\\71\\'),
(72, 'Управление перспективного развития и технологического присоединения', 0, 6, 8, '-1\\6\\72\\'),
(73, 'Отдел технологического присоединения', 0, 72, 8, '-1\\6\\72\\73\\'),
(74, 'Сектор перспективного развития', 0, 72, 8, '-1\\6\\72\\74\\'),
(75, 'Управление эксплуатации', 0, 6, 8, '-1\\6\\75\\'),
(76, 'Отдел эксплуатации ЛЭП и РС', 0, 75, 8, '-1\\6\\75\\76\\'),
(77, 'Отдел эксплуатации ПСТ', 0, 75, 8, '-1\\6\\75\\77\\'),
(78, 'Отдел организации ремонтов', 0, 75, 8, '-1\\6\\75\\78\\'),
(79, 'Отдел эксплуатации зданий и сооружений', 0, 75, 8, '-1\\6\\75\\79\\'),
(80, 'Служба релейной защиты и автоматики', 0, 75, 8, '-1\\6\\75\\80\\'),
(81, 'Центр управления сетями', 0, 6, 8, '-1\\6\\81\\'),
(82, 'Диспетчерская служба', 0, 81, 8, '-1\\6\\81\\82\\'),
(83, 'Служба электрических режимов', 0, 81, 8, '-1\\6\\81\\83\\'),
(84, 'Сектор безопасности технологических процессов', 0, 81, 8, '-1\\6\\81\\84\\'),
(85, 'Сектор метрологии и контроля качества электроэнергии', 0, 6, 8, '-1\\6\\85\\'),
(86, 'Отдел технологического развития и инноваций', 0, 6, 8, '-1\\6\\86\\'),
(87, 'Служба производственной безопасности и производственного контроля', 0, 6, 8, '-1\\6\\87\\'),
(88, 'Отдел охраны труда', 0, 87, 8, '-1\\6\\87\\88\\'),
(89, 'Сектор надежности и экологии', 0, 87, 8, '-1\\6\\87\\89\\'),
(90, 'Управление капитального строительства', 0, 6, 8, '-1\\6\\90\\'),
(91, 'Отдел капитального строительства', 0, 90, 8, '-1\\6\\90\\91\\'),
(92, 'Сектор подготовки строительства', 0, 91, 8, '-1\\6\\90\\91\\92\\'),
(93, 'Сектор отчетов', 0, 90, 8, '-1\\6\\90\\93\\'),
(94, 'Группа проектирования', 0, 90, 8, '-1\\6\\90\\94\\'),
(95, 'Отдел инвестиций', 0, 6, 8, '-1\\6\\95\\'),
(96, 'Управление корпоративных и технологических АСУ', 0, 6, 8, '-1\\6\\96\\'),
(97, 'Отдел корпоративных информационных систем управления', 0, 96, 8, '-1\\6\\96\\97\\'),
(98, 'Отдел заказчика', 0, 96, 8, '-1\\6\\96\\98\\'),
(99, 'Отдел автоматизированных систем технологического управления', 0, 96, 8, '-1\\6\\96\\99\\'),
(101, 'Сектор эксплуатации информационных технологий', 0, 96, 8, '-1\\6\\96\\101\\'),
(102, 'Управление по работе с персоналом', 0, 6, 8, '-1\\6\\102\\'),
(103, 'Отдел кадров и социальной политики', 0, 102, 8, '-1\\6\\102\\103\\'),
(104, 'Отдел развития персонала', 0, 102, 8, '-1\\6\\102\\104\\'),
(105, 'Учебно-тренинговый образовательный центр подготовки персонала', 0, 102, 8, '-1\\6\\102\\105\\'),
(106, 'Сектор менеджмента качества', 0, 102, 8, '-1\\6\\102\\106\\'),
(107, 'Служба психофизиологической надежности персонала', 0, 102, 8, '-1\\6\\102\\107\\'),
(109, 'Отдел организации труда и заработной платы', 0, 102, 8, '-1\\6\\102\\109\\'),
(110, 'Служба безопасности', 0, 6, 8, '-1\\6\\110\\'),
(111, 'Отдел мобилизационной подготовки и гражданской обороны', 0, 6, 8, '-1\\6\\111\\'),
(112, 'Сектор по защите государственной тайны', 0, 6, 8, '-1\\6\\112\\'),
(113, 'Управление логистики и МТО', 0, 6, 8, '-1\\6\\113\\'),
(114, 'Отдел логистики', 0, 113, 8, '-1\\6\\113\\114\\'),
(115, 'Отдел механизации и транспорта', 0, 113, 8, '-1\\6\\113\\115\\'),
(116, 'Отдел по связям с общественностью', 0, 6, 8, '-1\\6\\116\\'),
(117, 'Отдел управления делами', 0, 6, 8, '-1\\6\\117\\'),
(118, 'Сектор хозяйственного обеспечения', 0, 117, 8, '-1\\6\\117\\118\\'),
(144, 'Служба учета электроэнергии', 0, 5, 8, ''),
(145, 'Группа технологического присоединения и взаимодействия с клиентами', 0, 5, 8, ''),
(146, 'Производственно-технический отдел', 0, 5, 8, ''),
(147, 'Группа метрологии и контроля качества электроэнергии', 0, 5, 8, ''),
(148, 'Служба диагностики, изоляции и защиты от перенапряжений', 0, 5, 8, ''),
(149, 'Служба эксплуатации зданий и сооружений', 0, 5, 8, ''),
(150, 'Служба механизации и транспорта', 0, 5, 8, ''),
(151, 'Служба производственной безопасности и производственного контроля', 0, 5, 8, ''),
(152, 'Оперативно-диспетчерская служба', 0, 5, 8, ''),
(153, 'Служба релейной защиты и автоматики', 0, 5, 8, ''),
(154, 'Служба подстанций', 0, 5, 8, ''),
(155, 'Служба воздушных линий', 0, 5, 8, ''),
(156, 'Служба распределительных сетей', 0, 5, 8, ''),
(157, 'Отдел материально-технического обеспечения', 0, 5, 8, ''),
(158, 'Группа документационного обеспечения', 0, 5, 8, ''),
(159, 'Служба корпоративных и технологических автоматизированных систем управления', 0, 5, 8, ''),
(160, 'Отдел капитального строительства', 0, 5, 8, ''),
(161, 'Служба учета электроэнергии', 0, 4, 8, ''),
(162, 'Отдел технологического присоединения и взаимодействия с клиентами', 0, 4, 8, ''),
(163, 'Производственно-технический отдел', 0, 4, 8, ''),
(164, 'Отдел метрологии и контроля качества электроэнергии', 0, 4, 8, ''),
(165, 'Служба диагностики, изоляции и защиты от перенапряжений', 0, 4, 8, ''),
(166, 'Служба эксплуатации зданий и сооружений', 0, 4, 8, ''),
(167, 'Служба механизации и транспорта', 0, 4, 8, ''),
(168, 'Служба производственной безопасности и производственного контроля', 0, 4, 8, ''),
(169, 'Оперативно-диспетчерская служба', 0, 4, 8, ''),
(170, 'Служба релейной защиты и автоматики', 0, 4, 8, ''),
(171, 'Служба подстанций', 0, 4, 8, ''),
(172, 'Служба воздушных линий', 0, 4, 8, ''),
(173, 'Служба распределительных сетей', 0, 4, 8, ''),
(174, 'Отдел материально-технического обеспечения', 0, 4, 8, ''),
(175, 'Группа документационного обеспечения', 0, 4, 8, ''),
(176, 'Служба корпоративных и технологических автоматизированных систем управления', 0, 4, 8, ''),
(177, 'Отдел капитального строительства', 0, 4, 8, ''),
(178, 'Служба по работе с дебиторской задолженностью', 0, 4, 8, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
