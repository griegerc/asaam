SET FOREIGN_KEY_CHECKS = 0;
SET AUTOCOMMIT = 0;
START TRANSACTION;

CREATE TABLE `achievementTypes` (
  `achievementTypeId` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `achievementTypeIdentifier` char(50) NOT NULL,
  `gameId` SMALLINT UNSIGNED NOT NULL,
  `category` SMALLINT UNSIGNED DEFAULT NULL,
  `isAccretive` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  `isVisible` TINYINT UNSIGNED NOT NULL DEFAULT '1',
  `creationTime` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`achievementTypeId`),
        KEY `achievementTypeIdentifier` (`achievementTypeIdentifier`),
        KEY `gameId` (`gameId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `achievementTypeLevels` (
  `achievementTypeId` SMALLINT UNSIGNED NOT NULL,
  `level` TINYINT UNSIGNED NOT NULL,
  `value` INT NOT NULL,
  `gloryReward` SMALLINT UNSIGNED NOT NULL,
        PRIMARY KEY (`achievementTypeId`,`level`),
        CONSTRAINT `achievementTypeLevels_ibfk_1`
            FOREIGN KEY (`achievementTypeId`)
            REFERENCES `achievementTypes` (`achievementTypeId`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `achievements` (
  `achievementId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `achievementTypeId` SMALLINT UNSIGNED NOT NULL,
  `heroId` INT UNSIGNED DEFAULT NULL,
  `userId` INT UNSIGNED NOT NULL,
  `gameId` SMALLINT UNSIGNED NOT NULL,
  `isAchieved` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `achievedTime` INT DEFAULT NULL,
  `level` TINYINT UNSIGNED NOT NULL,
  `value` INT NOT NULL,
  `gloryFetched` INT DEFAULT NULL,
        PRIMARY KEY (`achievementId`),
        KEY `achievementTypeId` (`achievementTypeId`),
        CONSTRAINT `achievements_ibfk_1`
            FOREIGN KEY (`achievementTypeId`)
            REFERENCES `achievementTypes` (`achievementTypeId`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `glory` (
  `userId` INT UNSIGNED NOT NULL,
  `gameId` SMALLINT UNSIGNED NOT NULL,
  `gloryAmount` INT UNSIGNED NOT NULL DEFAULT '0',
        PRIMARY KEY (`userId`,`gameId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `gloryLog` (
  `logId` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `logTime` INT UNSIGNED NOT NULL,
  `achievementId` INT UNSIGNED NOT NULL,
  `gloryOffset` SMALLINT UNSIGNED NOT NULL,
        PRIMARY KEY (`logId`),
        KEY `achievementId` (`achievementId`),
        CONSTRAINT `gloryLog_ibfk_1`
            FOREIGN KEY (`achievementId`)
            REFERENCES `achievements` (`achievementId`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;