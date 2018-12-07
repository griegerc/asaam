<?php

class App_DatabaseStmt
{
    public static $sqlStatements = array(

        'getAllAchievementTypes' =>
            'SELECT * FROM `achievementTypes` WHERE `gameId` = ?;',

		'getAchievementTypeData' =>
			'SELECT * FROM `achievementTypes` WHERE `achievementTypeIdentifier` = ? AND `gameId` = ?;',

		'getAchievementTypeById' =>
			'SELECT * FROM `achievementTypes` WHERE `achievementTypeId` = ?;',

		'addAchievementType' =>
			'INSERT INTO `achievementTypes` 
			(`achievementTypeIdentifier`, `gameId`, `category`, `isAccretive`, `isVisible`, `creationTime`)
			VALUES(?, ?, ?, ?, ?, ?);',



		'getAchievementLevels' =>
			'SELECT * FROM `achievementTypeLevels` WHERE `achievementTypeId` = ?;',

		'addAchievementTypeLevel' =>
			'INSERT INTO `achievementTypeLevels` 
			(`achievementTypeId`, `level`, `value`, `gloryReward`)
			VALUES (?, ?, ?, ?);',



		'getGlory' =>
			'SELECT * FROM `glory` WHERE `gameId` = ? AND `userId` = ? LIMIT 1;',

		'updateGlory' =>
			'UPDATE `glory` SET `gloryAmount` = `gloryAmount` + ? WHERE `userId` = ? AND `gameId` = ?;',

		'addGlory' =>
			'INSERT INTO `glory` (`gloryAmount`, `userId`, `gameId`) VALUES (?, ?, ?);',



		'insertGloryLog' =>
			'INSERT INTO `gloryLog`
			(`logTime`, `achievementId`, `gloryOffset`)
			VALUES (?, ?, ?);',



		'addAchievement' =>
			'INSERT INTO `achievements` 
			(`achievementTypeId`, `userId`, `gameId`, `level`, `value`) 
			VALUES(?, ?, ?, ?, ?);',

		'addHeroAchievement' =>
			'INSERT INTO `achievements` 
			(`achievementTypeId`, `heroId`, `userId`, `gameId`, `level`, `value`) 
			VALUES(?, ?, ?, ?, ?, ?);',

		'getAchievementById' =>
            'SELECT * FROM `achievements` WHERE `achievementId` = ?;',

		'getAchievements' =>
			'SELECT * FROM `achievements` WHERE `heroId` IS NULL AND `userId` = ? AND `gameId` = ?;',

		'getAchievementsByHero' =>
			'SELECT * FROM `achievements` WHERE `heroId` = ? AND `userId` = ? AND `gameId` = ?;',

		'getMaxLevelByTypeAndHero' =>
            'SELECT * FROM `achievements` 
            WHERE `achievementTypeId` = ? AND `heroId` = ? AND `userId` = ? AND `gameId` = ? 
            ORDER BY `level` DESC
            LIMIT 1;',

		'getMaxLevelByType' =>
            'SELECT * FROM `achievements` 
            WHERE `heroId` IS NULL AND `achievementTypeId` = ? AND `userId` = ? AND `gameId` = ? 
            ORDER BY `level` DESC
            LIMIT 1;',

		'setGloryFetched' =>
			'UPDATE `achievements` SET `gloryFetched` = ? WHERE `achievementId` = ?;',

		'achievementUpdateValue' =>
			'UPDATE `achievements` SET `value` = `value` + ? WHERE `achievementId` = ?;',

		'achievementSetAchieved' =>
			'UPDATE `achievements` SET `isAchieved` = 1, `achievedTime` = ? WHERE `achievementId` = ?;'
	);
}