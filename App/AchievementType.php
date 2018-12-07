<?php

class App_AchievementType extends App_Base
{
	/** @var int */
	public $achievementTypeId;

	/** @var string */
	public $achievementTypeIdentifier;

	/** @var int */
	public $gameId;

	/** @var int */
	public $category;

	/** @var int */
	public $isAccretive;

	/** @var int */
	public $isVisible;

	/** @var int */
	public $creationTime;

	/** @var array */
	public $levels;

    /**
     * Instance
     * @param int $achievementTypeId
     */
	public function __construct($achievementTypeId = 0)
	{
		if ((int)$achievementTypeId > 0) {
			$this->_load($achievementTypeId);
		}
	}

	/**
	 * Loads a specific achievement type from the database
	 * @param int $achievementTypeId
	 * @throws Exception
	 * @return void
	 */
	private function _load($achievementTypeId)
	{
		$db = Gpf_Database::getInstance();
		$dbRes = $db->inquiryOne('getAchievementTypeById', (int)$achievementTypeId);
		if (!isset($dbRes['achievementTypeId'])) {
			throw new Exception('Achievement type not found', App_Error::ACHIEVEMENT_TYPE_NOT_FOUND);
		}

		$dbResLevels = $db->inquiry('getAchievementLevels', (int)$achievementTypeId);
		$this->_fillAttributes($dbRes, $dbResLevels);
	}

	/**
	 * Fills a attributes
	 * @param array $data
	 * @param array $dataLevels
	 */
	private function _fillAttributes($data, $dataLevels = array())
	{
		$this->achievementTypeId          = (int)$data['achievementTypeId'];
		$this->achievementTypeIdentifier  = $data['achievementTypeIdentifier'];
		$this->gameId                     = (int)$data['gameId'];
		$this->category                   = (int)$data['category'];
		$this->isAccretive                = (int)$data['isAccretive'];
		$this->isVisible                  = (int)$data['isVisible'];
		$this->creationTime               = (int)$data['creationTime'];
		$this->levels = array();
		foreach ($dataLevels as $l) {
			$this->levels[$l['level']] = $l;
		}
	}

    /**
     * Loads an achievement type instance by given data
     * @param array $data
     * @param array $dataLevels
     * @return App_AchievementType
     */
	public static function loadFromData($data, $dataLevels = array())
	{
		$achievementType = new self();
		$achievementType->_fillAttributes($data, $dataLevels);
		return $achievementType;
	}

    /**
     * Fetches an achievement type by its ID
     * @param string $achievementTypeId
     * @return App_AchievementType
     * @throws Exception
     */
	public static function getById($achievementTypeId)
	{
		$db = Gpf_Database::getInstance();
		$dbRes = $db->inquiryOne('getAchievementTypeById', (int)$achievementTypeId);
		$dbResLevels = $db->inquiry('getAchievementLevels', (int)$achievementTypeId);
		if (isset($dbRes['achievementTypeId'])) {
			return self::loadFromData($dbRes, $dbResLevels);
		}
		return NULL;
	}

    /**
     * Fetches an achievement type by its identifier and gameId
     * @param string $achievementTypeIdentifier
     * @param int $gameId
     * @return App_AchievementType
     */
	public static function get($achievementTypeIdentifier, $gameId)
	{
		$db = Gpf_Database::getInstance();
		$dbRes = $db->inquiryOne('getAchievementTypeData', array($achievementTypeIdentifier, (int)$gameId));
		if (!isset($dbRes['achievementTypeId'])) {
			return NULL;
		}
		$dbResLevels = $db->inquiry('getAchievementLevels', (int)$dbRes['achievementTypeId']);
		return self::loadFromData($dbRes, $dbResLevels);
	}

	/**
	 * Checks if a given achievementTypeIdentifier is valid for the system
	 * @param string $achievementTypeIdentifier
	 * @return bool
	 */
	public static function isValidIdentifier($achievementTypeIdentifier)
	{
	    if (strlen($achievementTypeIdentifier) < 1) {
			return false;
		}
		if (strlen($achievementTypeIdentifier) > 50) {
			return false;
		}
		$pattern = "/([_a-zA-Z0-9\\-]+)/";
		if (preg_match($pattern, $achievementTypeIdentifier) == 0) {
			return false;
		}
		return true;
	}

	/**
	 * Returns the maximal available achievement level
	 * @return int
	 */
	public function getMaxAvailableLevel()
	{
		return (int)max(array_keys($this->levels));
	}

	/**
	 * Fetches all achievement types for a specific game
	 *
	 * @param int $gameId
	 * @return array
	 */
	public static function getAll($gameId)
	{
		$achievementTypes = array();
		$db = Gpf_Database::getInstance();
		$dbRes = $db->inquiry('getAllAchievementTypes', $gameId);

		$i = 0;
		foreach ($dbRes as $dbRow) {
			$achievementTypes[$i] = array(
				'achievementTypeIdentifier' => $dbRow['achievementTypeIdentifier'],
				'achievementTypeId'         => (int)$dbRow['achievementTypeId'],
				'category'                  => (int)$dbRow['category'],
				'isAccretive'               => (int)$dbRow['isAccretive'],
				'isVisible'                 => (int)$dbRow['isVisible']
			);

			$dbResLevel = $db->inquiry('getAchievementLevels', (int)$dbRow['achievementTypeId']);
			if (is_array($dbResLevel) && count($dbResLevel)) {
				$achievementTypes[$i]['levels'] = array();
				foreach ($dbResLevel as $dbRowLevel) {
					$achievementTypes[$i]['levels'][] = array(
						'level' => (int)$dbRowLevel['level'],
						'value' => (int)$dbRowLevel['value'],
						'gloryReward' => (int)$dbRowLevel['gloryReward']
					);
				}
			}

			$i++;
		}

		return $achievementTypes;
	}

	/**
	 * Adds a new achievement type to the database
	 * @param string $achievementTypeIdentifier
	 * @param array $achievementTypeData
	 * @param int $gameId
	 * @throws Exception
	 */
	public static function add($achievementTypeIdentifier, $achievementTypeData, $gameId)
	{
		$db = Gpf_Database::getInstance();
		$db->beginTransaction();
		try {
			$category = 0;
			if (isset($achievementTypeData['category']) && (int)$achievementTypeData['category'] > 0) {
				$category = (int)$achievementTypeData['category'];
			}
			$isAccretive = 0;
			if (isset($achievementTypeData['isAccretive']) && (int)$achievementTypeData['isAccretive'] > 0) {
				$isAccretive = 1;
			}
			$isVisible = 0;
			if (isset($achievementTypeData['isVisible']) && (int)$achievementTypeData['isVisible'] > 0) {
				$isVisible = 1;
			}

			$data = array(
				$achievementTypeIdentifier,
				$gameId,
				$category,
				$isAccretive,
				$isVisible,
				self::now()
			);
			$db->inquiry('addAchievementType', $data);

			$achievementTypeId = $db->getLastInsertedId();
			foreach ($achievementTypeData['levels'] as $l) {
				$db->inquiry('addAchievementTypeLevel', array($achievementTypeId, (int)$l['level'], (int)$l['value'], (int)$l['glory']));
			}

			$db->commit();
		} catch (Exception $ex) {
			$db->rollback();
			throw new Exception('General error', App_Error::GENERAL);
		}
	}
}