# SQL Manager 2007 for MySQL 4.3.3.2
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : whatdoido


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE `whatdoido`
    CHARACTER SET 'latin1'
    COLLATE 'latin1_swedish_ci';

USE `whatdoido`;

#
# Structure for the `category` table : 
#

CREATE TABLE `category` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT COLLATE latin1_swedish_ci NOT NULL,
  `project_id` INTEGER(11) NOT NULL,
  PRIMARY KEY (`id`)

)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `dependency` table : 
#

CREATE TABLE `dependency` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `task1_id` INTEGER(11) NOT NULL,
  `task2_id` INTEGER(11) NOT NULL,
  `type_id` INTEGER(11) NOT NULL COMMENT 'Dependency type: 0 = task 2 begin after task 1, 1 = begin together',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

Create Index Task1IdIndex on dependency(task1_id);
Create Index Task2IdIndex on dependency(task2_id);

#
# Structure for the `location` table : 
#

CREATE TABLE `location` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT COLLATE latin1_swedish_ci,
  `address` TEXT COLLATE latin1_swedish_ci,
  `x` DOUBLE DEFAULT '36.105592' COMMENT 'longitude',
  `y` DOUBLE DEFAULT '-79.262264' COMMENT 'latitude',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `note` table : 
#

CREATE TABLE `note` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `task_id` INTEGER(11) DEFAULT NULL,
  `text` TEXT COLLATE latin1_swedish_ci,
  `author_id` INTEGER(11) DEFAULT NULL,
  `dt` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)

)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `project` table : 
#

CREATE TABLE `project` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT COLLATE latin1_swedish_ci NOT NULL,
  `dto` DATETIME NOT NULL,
  `creator_id` INTEGER(11) NOT NULL,
  `lead_id` INTEGER(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)

)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `projrelease` table : 
#

CREATE TABLE `projrelease` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT COLLATE latin1_swedish_ci,
  `date` DATE DEFAULT NULL,
  `project_id` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY (`id`)

)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `resource` table : 
#

CREATE TABLE `resource` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT COLLATE latin1_swedish_ci,
  `type` INTEGER(11) NOT NULL COMMENT 'Resource type ID. 0 = Human, 1 = machine, etc.',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';


#
# Structure for the `session` table : 
#

CREATE TABLE `session` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'идентификатор состояния смены',
  `login` VARCHAR(20) COLLATE utf8_general_ci DEFAULT NULL COMMENT 'логин, с помощью которого производится попытка входа',
  `password` VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL COMMENT 'пароль, с помощью которого производится попытка входа',
  `user_id` INTEGER(11) DEFAULT NULL COMMENT 'идентификатор пользователя user.id',
  `dtb` DATETIME NOT NULL COMMENT 'дата-время начала смены',
  `dte` DATETIME DEFAULT NULL COMMENT 'дата-время окончания смены, если не null смена считается закрытой',
  `error_id` INTEGER(11) DEFAULT NULL COMMENT 'идентификатор ошибки',
  PRIMARY KEY (`id`)

)ENGINE=MyISAM
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
COMMENT='состояние сессии сотрудника; ; ; InnoDB free: 17408 kB';

#
# Structure for the `status` table : 
#

CREATE TABLE `status` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT COLLATE latin1_swedish_ci,
  `color` TEXT COLLATE latin1_swedish_ci,
  PRIMARY KEY (`id`)

)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `subscription` table : 
#

CREATE TABLE `subscription` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER(11) NOT NULL,
  `project_id` INTEGER(11) NOT NULL,
  PRIMARY KEY (`id`)

)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `task` table : 
#

CREATE TABLE `task` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` TEXT COLLATE latin1_swedish_ci NOT NULL,
  `status_id` INTEGER(11) NOT NULL DEFAULT '1',
  `project_id` INTEGER(11) NOT NULL,
  `creator_id` INTEGER(11) NOT NULL,
  `owner_id` INTEGER(11) NOT NULL,
  `location_id` INTEGER(11) NOT NULL,
  `release_id` INTEGER(11) NOT NULL,
  `category_id` INTEGER(11) NOT NULL,
  `priority` INTEGER(11) NOT NULL,
  `description` TEXT COLLATE latin1_swedish_ci,
  `teststeps` TEXT COLLATE latin1_swedish_ci,
  `start_time` DATETIME DEFAULT NULL COMMENT 'Task should start not earlier then this time. NULL if task can start at any time.',
  `end_time` DATETIME DEFAULT NULL COMMENT 'Deadline. NULL if task does not have a deadline. Deadline has a preference over start_time in case of conflict.',
  `timest` TIME DEFAULT '01:00:00',
  `timest_precision` INTEGER(11) NOT NULL DEFAULT '20',
  `progress` INTEGER(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)

)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `task_history` table : 
#

CREATE TABLE `task_history` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `task_id` INTEGER(11) DEFAULT NULL,
  `user_id` INTEGER(11) NOT NULL,
  `dto` DATETIME NOT NULL,
  `action_id` INTEGER(11) NOT NULL,
  `name` TEXT COLLATE latin1_swedish_ci,
  `description` TEXT COLLATE latin1_swedish_ci,
  `steps` TEXT COLLATE latin1_swedish_ci,
  `release_id` INTEGER(11) DEFAULT NULL,
  `status_id` INTEGER(11) DEFAULT NULL,
  `progress` INTEGER(11) DEFAULT NULL,
  `owner_id` INTEGER(11) DEFAULT NULL,
  `category_id` INTEGER(11) DEFAULT NULL,
  `timest` TIME DEFAULT NULL,
  `timest_precision` INTEGER(11) DEFAULT NULL,
  `priority_id` INTEGER(11) DEFAULT NULL,
  `note_id` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY (`id`)

)ENGINE=MyISAM
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `taskrepeat` table : 
#

CREATE TABLE `taskrepeat` (
  `task_id` INTEGER(11) NOT NULL,
  `repeat_type` TEXT NOT NULL COMMENT 'Specify one of: yearly, monthly, weekly, daily, etc...',
  `mask` INTEGER(11) DEFAULT '0' COMMENT 'Means different things depending on the repeat type',
  `start_dt` DATETIME DEFAULT NULL,
  `stop_dt` DATETIME DEFAULT NULL,
  PRIMARY KEY (`task_id`)
)ENGINE=InnoDB
CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';

#
# Structure for the `taskresource` table : 
#

CREATE TABLE `taskresource` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `task_id` INTEGER(11) NOT NULL,
  `resource_id` INTEGER(11) NOT NULL,
  `util` INTEGER(11) DEFAULT '100' COMMENT 'Resource utilization in this task in percents',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci';


#
# Structure for the `user` table : 
#

CREATE TABLE `user` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(20) COLLATE utf8_general_ci DEFAULT NULL,
  `password` VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL,
  `firstname` TEXT COLLATE utf8_general_ci,
  `lastname` TEXT COLLATE utf8_general_ci,
  `email` TEXT COLLATE utf8_general_ci,
  PRIMARY KEY (`id`),
  KEY `login` (`login`)

)ENGINE=MyISAM
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
COMMENT='InnoDB free: 17408 kB';

#
# Data for the `status` table  (LIMIT 0,500)
#

INSERT INTO `status` (`id`, `name`, `color`) VALUES
  (1,'new','#FFFF99'),
  (2,'in process','#BBBBCC'),
  (3,'ready to test','#00FF00'),
  (4,'resolved','#EEEEFF'),
  (5,'closed','#FFFFFF');
COMMIT;

#
# Data for the `user` table  (LIMIT 0,500)
#
-- user admin
-- password 12345
INSERT INTO `user` (`id`, `login`, `password`, `firstname`, `lastname`, `email`) VALUES
  (1,'admin','8cb2237d0679ca88db6464eac60da96345513964','Admin','Admin','admin@arcatechsystems.com');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;