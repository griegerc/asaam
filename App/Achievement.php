<?php

class App_Achievement extends App_Base
{
    /** @var int */
    public $achievementId;

    /** @var int */
    public $achievementTypeId;

    /** @var int */
    public $heroId;

    /** @var int */
    public $userId;

    /** @var int */
    public $gameId;

    /** @var int */
    public $isAchieved;

    /** @var int */
    public $level;

    /** @var int */
    public $value;

    /** @var int */
    public $gloryFetched;

    /**
     * Instance
     * @param int $achievementId
     * @throws Exception
     */
    public function __construct($achievementId = 0)
    {
        if ((int)$achievementId > 0) {
            $this->_load($achievementId);
        }
    }

    /**
     * Loads a specific achievement from the database
     * @param int $achievementId
     * @throws Exception
     * @return void
     */
    private function _load($achievementId)
    {
        $db = Gpf_Database::getInstance();
        $dbRes = $db->inquiryOne('getAchievementById', (int)$achievementId);

        if (!isset($dbRes['achievementId'])) {
            throw new Exception('Achievement not found', App_Error::ACHIEVEMENT_NOT_FOUND);
        }
        $this->_fillAttributes($dbRes);
    }

    /**
     * Fills a attributes
     * @param array $data
     */
    private function _fillAttributes($data)
    {
        $this->achievementId     = (int)$data['achievementId'];
        $this->achievementTypeId = (int)$data['achievementTypeId'];
        $this->heroId            = (int)$data['heroId'];
        $this->userId            = (int)$data['userId'];
        $this->gameId            = (int)$data['gameId'];
        $this->isAchieved        = (int)$data['isAchieved'];
        $this->level             = (int)$data['level'];
        $this->value             = (int)$data['value'];
        $this->gloryFetched      = (int)$data['gloryFetched'];
    }

    /**
     * Loads an achievement instance by given data
     * @param array $data
     * @return App_Achievement
     * @throws Exception
     */
    public static function loadFromData($data)
    {
        $achievement = new self();
        $achievement->_fillAttributes($data);
        return $achievement;
    }

    /**
     * Fetches the highest level of an achievement for a specific hero
     * @param int $achievementTypeId
     * @param int $heroId
     * @param int $userId
     * @param int $gameId
     * @return App_Achievement
     * @throws Exception
     */
    public static function getMaxLevelByTypeAndHero($achievementTypeId, $heroId, $userId, $gameId)
    {
        $db = Gpf_Database::getInstance();
        $dbRes = $db->inquiryOne('getMaxLevelByTypeAndHero', array($achievementTypeId, $heroId, $userId, $gameId));
        if (isset($dbRes['achievementId'])) {
            return self::loadFromData($dbRes);
        }
        return NULL;
    }

    /**
     * Fetches the highest level of an achievement for a specific hero
     * @param int $achievementTypeId
     * @param int $userId
     * @param int $gameId
     * @return App_Achievement
     * @throws Exception
     */
    public static function getMaxLevelByType($achievementTypeId, $userId, $gameId)
    {
        $db = Gpf_Database::getInstance();
        $dbRes = $db->inquiryOne('getMaxLevelByType', array($achievementTypeId, $userId, $gameId));
        if (isset($dbRes['achievementId'])) {
            return self::loadFromData($dbRes);
        }
        return NULL;
    }

    /**
     * Updates the reached value of and achievement
     * @param int $achievementId
     * @param int $valueOffset
     */
    private static function _updateValue($achievementId, $valueOffset)
    {
        $db = Gpf_Database::getInstance();
        $db->inquiry('achievementUpdateValue', array($valueOffset, $achievementId));
    }

    /**
     * Set an achievement (or achievement level) on the status "achieved"
     * @param int $achievementId
     */
    private static function _setAchieved($achievementId)
    {
        $db = Gpf_Database::getInstance();
        $db->inquiry('achievementSetAchieved', array(self::now(), $achievementId));
    }

    /**
     * Add value to an achievement for a hero/user
     * @param string $achievementTypeIdentifier
     * @param int    $heroId
     * @param int    $userId
     * @param int    $gameId
     * @param int    $value
     * @throws Exception
     * @return bool
     */
    public static function addAchievementData($achievementTypeIdentifier, $heroId, $userId, $gameId, $value)
    {
        $achievementType = App_AchievementType::get($achievementTypeIdentifier, $gameId);
        if (!($achievementType instanceof App_AchievementType)) {
            throw new Exception('Achievement type does not exist for this game', App_Error::ACHIEVEMENT_GAME_MISMATCH);
        }
        if ($userId <= 0) {
            throw new Exception('Invalid userId', App_Error::INVALID_USER_ID);
        }
        if ($gameId <= 0) {
            throw new Exception('Invalid gameId', App_Error::INVALID_GAME_ID);
        }
        if ($value <= 0) {
            throw new Exception('Invalid value', App_Error::INVALID_VALUE);
        }

        $isAchieved = false;
        $wasAchieved = false;
        $db = Gpf_Database::getInstance();
        $db->beginTransaction();
        try {
            $level = 0;
            $achievedValue = 0;
            while ($value > 0) {
                if ($heroId > 0) {
                    $achievement = self::getMaxLevelByTypeAndHero($achievementType->achievementTypeId, $heroId, $userId, $gameId);
                } else {
                    $achievement = self::getMaxLevelByType($achievementType->achievementTypeId, $userId, $gameId);
                }

                if ($achievement instanceof App_Achievement) {
                    $wasAchieved = $achievement->isAchieved;
                    $level = $achievement->level;
                    $achievedValue = $achievement->value;
                }

                if ($wasAchieved == 1 && $level >= $achievementType->getMaxAvailableLevel()) {
                    break; // no further achievement levels available
                }

                if ($achievement == NULL || $achievement->isAchieved == 1) {
                    $level++;
                    $value += $achievedValue;
                    $newAchievementLevel = $achievementType->levels[$level];
                    $newMaxValue = $newAchievementLevel['value'];

                    if ($achievementType->isAccretive === 1 || $value >= $newMaxValue) {
                        if ($heroId > 0) {
                            $db->inquiry('addHeroAchievement', array($achievementType->achievementTypeId, $heroId, $userId, $gameId, $level, min($value, $newMaxValue)));
                        } else {
                            $db->inquiry('addAchievement', array($achievementType->achievementTypeId, $userId, $gameId, $level, min($value, $newMaxValue)));
                        }
                    }
                    if ($value >= $newMaxValue) {
                        self::_setAchieved($db->getLastInsertedId());
                        $isAchieved = true;
                        $value -= $newMaxValue;
                    } else {
                        $value = 0;
                    }
                } else {
                    $maxValue = $achievementType->levels[$level]['value'];
                    if ($value + $achievement->value >= $maxValue) {
                        self::_updateValue($achievement->achievementId, ($maxValue - $achievement->value));
                        self::_setAchieved($achievement->achievementId);
                        $value -= ($maxValue - $achievement->value);
                        $isAchieved = true;
                    } else {
                        self::_updateValue($achievement->achievementId, $value);
                        $value = 0;
                    }
                }
            }

            $db->commit();
        } catch (Exception $ex) {
            $db->rollback();
            throw new Exception('General error', App_Error::GENERAL);
        }

        return $isAchieved;
    }

    /**
     * Prepares and returns the achievements data
     * @param array $data
     * @param int $gameId
     * @return array
     */
    private static function _getAchievements($data, $gameId)
    {
        if (count($data) == 0) {
            return array();
        }

        $achievementTypes = App_AchievementType::getAll($gameId);
        $result = array();

        foreach ($data as $row) {
            $ach = array(
                'achievementId' => (int)$row['achievementId'],
                'isAchieved'    => ((int)$row['isAchieved'] === 1)?true:false,
                'achievedTime'  => (int)$row['achievedTime'],
                'gloryFetched'  => (int)$row['gloryFetched'],
                'value'         => (int)$row['value'],
                'level'         => (int)$row['level'],
            );

            foreach ($achievementTypes as $type) {
                if ($type['achievementTypeId'] == $row['achievementTypeId']) {
                    $ach['achievementTypeIdentifier'] = $type['achievementTypeIdentifier'];
                    $ach['achievementTypeId'] = $type['achievementTypeId'];
                    $ach['category'] = (int)$type['category'];
                    $ach['isVisible'] = (int)$type['isVisible'];
                    foreach ($type['levels'] as $l) {
                        if ($row['level'] == $l['level']) {
                            $ach['gloryReward'] = (int)$l['gloryReward'];
                            $ach['maxValue'] = (int)$l['value'];
                        }
                    }
                }
            }

            ksort($ach);
            $result[] = $ach;
        }

        return $result;
    }

    /**
     * Fetches all achievements for a user/game combination
     * @param int $userId
     * @param int $gameId
     * @return array
     */
    public static function bulkGet($userId, $gameId)
    {
        $db = Gpf_Database::getInstance();
        $dbRes = $db->inquiry('getAchievements', array($userId, $gameId));
        return self::_getAchievements($dbRes, $gameId);
    }

    /**
     * Fetches all achievements for a hero/user/game combination
     * @param int $heroId
     * @param int $userId
     * @param int $gameId
     * @return array
     */
    public static function bulkGetByHero($heroId, $userId, $gameId)
    {
        $db = Gpf_Database::getInstance();
        $dbRes = $db->inquiry('getAchievementsByHero', array($heroId, $userId, $gameId));
        return self::_getAchievements($dbRes, $gameId);
    }

    /**
     * Fetches the glory award for a single achievement
     * @throws Exception
     */
    public function fetchGlory()
    {
        if ($this->gloryFetched > 0) {
            throw new Exception('Glory already fetched', App_Error::GLORY_ALREADY_FETCHED);
        }
        if ($this->isAchieved == 0) {
            throw new Exception('Cannot fetch glory', App_Error::CANNOT_FETCH_GLORY);
        }

        $achievementType = App_AchievementType::getById($this->achievementTypeId);
        $this->_db()->beginTransaction();
        try {
            $this->_db()->inquiry('setGloryFetched', array(self::now(), $this->achievementId));
            $gloryReward = $achievementType->levels[$this->level]['gloryReward'];

            if ($gloryReward > 0) {
                $dbRes = $this->_db()->inquiryOne('getGlory', array($this->userId, $achievementType->gameId));
                if (isset($dbRes['userId'])) {
                    $this->_db()->inquiry('updateGlory', array($gloryReward, $this->userId, $achievementType->gameId));
                } else {
                    $this->_db()->inquiry('addGlory', array($gloryReward, $this->userId, $achievementType->gameId));
                }
                $this->_db()->inquiry('insertGloryLog', array(self::now(), $this->achievementId, $gloryReward));
            }

            $this->_db()->commit();
        } catch (Exception $ex) {
            $this->_db()->rollback();
            throw new Exception('General error', App_Error::GENERAL);
        }
    }
}