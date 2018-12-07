<?php

class Controllers_Api extends Controllers_Abstract
{
    /**
     * Default action
     * @return void
     */
    public function indexAction()
    {
		$this->output = 'There is no achievement, only ASAAM!';
    }

    /**
     * Fetches a full list of all achievement types including all stored meta data.
     */
    public function getAchievementTypesAction()
    {
        $gameId = (int)$this->_getParam('gameId', 0);
        $achievementTypes = App_AchievementType::getAll($gameId);

        $this->output = $achievementTypes;
    }

    /**
     * A new achievement type is added to the system.
     */
    public function addAchievementTypeAction()
    {
        $achievementTypeIdentifier = $this->_cleanString($this->_getParam('achievementTypeIdentifier'));
        $achievementTypeData = json_decode($this->_cleanString($this->_getParam('achievementTypeData')), true);
        $gameId = (int)$this->_getParam('gameId', 0);

        if ($gameId <= 0) {
			$this->_setError('Invalid gameId', App_Error::INVALID_GAME_ID);
			return;
        }
        if (!App_AchievementType::isValidIdentifier($achievementTypeIdentifier)) {
			$this->_setError('Invalid achievement type identifier', App_Error::INVALID_TYPE_IDENTIFIER);
			return;
        }

		$achievementType = App_AchievementType::get($achievementTypeIdentifier, $gameId);
        if ($achievementType instanceof App_AchievementType) {
			$this->_setError('Achievement type already present', App_Error::ACHIEVEMENT_TYPE_EXISTS);
			return;
        }

        if (!isset($achievementTypeData['levels']) || !is_array($achievementTypeData['levels']) || count($achievementTypeData['levels']) == 0) {
			$this->_setError('Achievement level missing', App_Error::ACHIEVEMENT_LEVEL_MISSING);
			return;
		}

		if ((int)$achievementTypeData['isAccretive'] == 0 && count($achievementTypeData['levels']) != 1) {
			$this->_setError('Non-accretive achievements can only have one level', App_Error::ACHIEVEMENT_LEVEL_MISMATCH);
			return;
		}

        try {
			App_AchievementType::add($achievementTypeIdentifier, $achievementTypeData, $gameId);
			$this->output['success'] = true;
		} catch (Exception $ex) {
			$this->_setError($ex->getMessage(), $ex->getCode());
		}
    }

    /**
     * Increases the data for a specific achievement for the hero/user.
     */
    public function addDataAction()
    {
		$heroId = (int)$this->_getParam('heroId', 0);
		$userId = (int)$this->_getParam('userId', 0);
		$gameId = (int)$this->_getParam('gameId', 0);
		$value = (int)$this->_getParam('value', 0);
		$achievementTypeIdentifier = $this->_getParam('achievementTypeIdentifier', 0);

		try {
			$hasAchieved = App_Achievement::addAchievementData($achievementTypeIdentifier, $heroId, $userId, $gameId, $value);
			$this->output['success'] = true;
			$this->output['hasAchieved'] = $hasAchieved;
		} catch (Exception $ex) {
			$this->_setError($ex->getMessage(), $ex->getCode());
		}
    }

    /**
     * Fetches a list of achievements a hero has reached in a specific game.
     */
    public function getHeroAchievementsAction()
    {
		$heroId = (int)$this->_getParam('heroId', 0);
		$userId = (int)$this->_getParam('userId', 0);
		$gameId = (int)$this->_getParam('gameId', 0);
		$this->output = App_Achievement::bulkGetByHero($heroId, $userId, $gameId);
    }

    /**
     * Fetches a list of achievements a user has reached in a specific game.
     */
    public function getAchievementsAction()
    {
		$userId = (int)$this->_getParam('userId', 0);
		$gameId = (int)$this->_getParam('gameId', 0);
		$this->output = App_Achievement::bulkGet($userId, $gameId);
    }

    /**
     * Fetches glory from an earned achievement and grants it to the user.
     */
    public function fetchGloryAction()
    {
        $achievementId = (int)$this->_getParam('achievementId', 0);
        $userId = (int)$this->_getParam('userId', 0);
        $gameId = (int)$this->_getParam('gameId', 0);

        try {
            $achievement = new App_Achievement($achievementId);
        } catch (Exception $ex) {
            $this->_setError('Achievement not found', App_Error::ACHIEVEMENT_NOT_FOUND);
            return;
        }

        if ($userId != $achievement->userId || $gameId != $achievement->gameId) {
            $this->_setError('Invalid params', App_Error::INVALID_PARAMS);
            return;
        }

        try {
			$achievement->fetchGlory();
		} catch (Exception $ex) {
        	$this->_setError($ex->getMessage(), $ex->getCode());
			return;
		}

		$this->output['success'] = true;
    }

    /**
     * Returns the glory amount a user earned for a specific game.
     */
    public function getGloryAction()
    {
        $userId = (int)$this->_getParam('userId', 0);
        $gameId = (int)$this->_getParam('gameId', 0);

        $dbRes = $this->_db()->inquiryOne('getGlory', array($gameId, $userId));

        $result = array();
        if (isset($dbRes['gloryAmount'])) {
            $result['gloryAmount'] = (int)$dbRes['gloryAmount'];
        }

        $this->output = $result;
    }
}