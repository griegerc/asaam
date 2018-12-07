<?php

define('API_BASE_URL', 'http://asaam.lan/api/');
define('AUTH_SECRET',  'soSecret');

/*
$cases = array(
	array(
		'action' => 'addAchievementType/',
		'params' => array(
			'achievementTypeIdentifier', 	'FIGHTER',
			'gameId', 						'12',
			'achievementTypeData', 			'{"category":0,"isAccretive":0,"isVisible":1,"levels":[{"level":1,"value":10,"glory":3}]}')
	),
	array(
		'action' => 'addAchievementType/',
		'params' => array(
			'achievementTypeIdentifier', 	'DEFENDER',
			'gameId', 						'12',
			'achievementTypeData', 			'{"category":1,"isAccretive":1,"isVisible":1,"levels":[{"level":1,"value":10,"glory":3},{"level":2,"value":20,"glory":6},{"level":3,"value":30,"glory":9}]}')
	),
	array(
		'action' => 'addAchievementType/',
		'params' => array(
			'achievementTypeIdentifier', 	'TANK',
			'gameId', 						'12',
			'achievementTypeData', 			'{"category":1,"isAccretive":1,"isVisible":1,"levels":[{"level":1,"value":100,"glory":2},{"level":2,"value":250,"glory":5}]}')
	),
	array(
		'action' => 'addAchievementType/',
		'params' => array(
			'achievementTypeIdentifier', 	'FIGHTER',
			'gameId', 						'15',
			'achievementTypeData', 			'{"category":0,"isAccretive":0,"isVisible":1,"levels":[{"level":1,"value":10,"glory":3}]}')
	),
);*/

/*
$cases = array(
    array(
        'action' => 'getGlory/',
        'params' => array()
    ),
    array(
        'action' => 'getGlory/',
        'params' => array('userId', '102')
    ),
    array(
        'action' => 'getGlory/',
        'params' => array('gameId', '120')
    ),
    array(
        'action' => 'getGlory/',
        'params' => array('userId', '102','gameId', '120')
    ),
    array(
        'action' => 'getGlory/',
        'params' => array('userId', '7', 'gameId', 1')
    ),
    array(
        'action' => 'getGlory/',
        'params' => array('userId', '1', 'gameId', '7')
    ),
);*/

/*
$cases = array(
    array(
        'action' => 'fetchGlory/',
        'params' => array()
    ),
    array(
        'action' => 'fetchGlory/',
        'params' => array('achievementId', '39586')
    ),
    array(
        'action' => 'fetchGlory/',
        'params' => array('userId', '1', 'gameId', '7')
    ),
    array(
        'action' => 'fetchGlory/',
        'params' => array('achievementId', '2', 'userId', '51115', 'gameId', '111')
    ),
    array(
        'action' => 'fetchGlory/',
        'params' => array('achievementId', '2','userId', '912','gameId', '7')
    ),
    array(
        'action' => 'fetchGlory/',
        'params' => array('achievementId', '2', 'userId', '912', 'gameId', '7')
    ),
);
*/

/*
$cases = array(
    array(
        'action' => 'getAchievements/',
        'params' => array()
    ),
    array(
        'action' => 'getAchievements/',
        'params' => array('gameId', '7')
    ),
    array(
        'action' => 'getAchievements/',
        'params' => array('userId', '912')
    ),
    array(
        'action' => 'getAchievements/',
        'params' => array('userId', '912', 'gameId', '7')
    )
);
*/

/*
$cases = array(
    array(
        'action' => 'getHeroAchievements/',
        'params' => array()
    ),
    array(
        'action' => 'getHeroAchievements/',
        'params' => array('gameId', '7')
    ),
    array(
        'action' => 'getHeroAchievements/',
        'params' => array('userId', '912')
    ),
    array(
        'action' => 'getHeroAchievements/',
        'params' => array('heroId', '624')
    ),
    array(
        'action' => 'getHeroAchievements/',
        'params' => array('heroId', '624', 'userId', '912', 'gameId', '7')
    )
);*/

/*
$cases = array(
    array(
        'action' => 'getAchievementTypes/',
        'params' => array()
    ),
    array(
        'action' => 'getAchievementTypes/',
        'params' => array('gameId', '71')
    ),
    array(
        'action' => 'getAchievementTypes/',
        'params' => array('gameId', '7')
    ),
);*/

$cases = array(
    array(
        'action' => 'addData/',
        'params' => array()
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'BLA')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'BLA', 'gameId', '99')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'userId', '5')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'userId', '5', 'value', '0')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'userId', '5', 'value', '-1')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'userId', '5', 'value', 'ASB')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'userId', '5', 'value', '1')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'userId', '5', 'value', '5')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'userId', '5', 'value', '10')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'userId', '5', 'value', '100')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'DEFENDER', 'gameId', '12', 'userId', '5', 'value', '1')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'DEFENDER', 'gameId', '12', 'userId', '5', 'value', '5')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'DEFENDER', 'gameId', '12', 'userId', '5', 'value', '10')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'DEFENDER', 'gameId', '12', 'userId', '5', 'value', '100')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'heroId', '9', 'userId', '5', 'value', '1')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'heroId', '9', 'userId', '5', 'value', '5')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'heroId', '9', 'userId', '5', 'value', '10')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'FIGHTER', 'gameId', '12', 'heroId', '9', 'userId', '5', 'value', '100')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'DEFENDER', 'gameId', '12', 'heroId', '9', 'userId', '5', 'value', '1')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'DEFENDER', 'gameId', '12', 'heroId', '9', 'userId', '5', 'value', '5')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'DEFENDER', 'gameId', '12', 'heroId', '9', 'userId', '5', 'value', '10')
    ),
    array(
        'action' => 'addData/',
        'params' => array('achievementTypeIdentifier', 'DEFENDER', 'gameId', '12', 'heroId', '9', 'userId', '5', 'value', '100')
    )
);



function sendRequest($action, $params)
{
	$time = time();
	$token = md5(uniqid($time));
	$url = API_BASE_URL . $action . implode('/', $params);

	print $url . PHP_EOL;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'X-Auth-Token: '.$token,
		'X-Auth-Signature: '.md5($token.AUTH_SECRET.$time),
		'X-Auth-Timestamp: '.$time
	));

	$result = curl_exec($ch);
    curl_close($ch);

	if ($result !== false) {
        print $result . PHP_EOL;
	} else {
		print 'ERROR: Cannot fetch data!' . PHP_EOL;
	}

	print '--------------------------------------------------------------------------------' . PHP_EOL;
}

foreach ($cases as $case) {
	sendRequest($case['action'], $case['params']);
}