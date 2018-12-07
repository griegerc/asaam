# ASAAM - Achievement system as a microservice
    Author: Christian Grieger
    Version: 1.0 (2018-12-07)
    License: GNU General Public License v3.0 (see file "license")
This microserver (webservice) is intended to serve as an achievement system for online (browser) games. 
It provides an easy to use interface with JSON as an exchange format of the data.

## Definitions
 - **Game**: A virtual realm for achievements (so, this system can be used for different games at once).
 - **User**: The person who can earn the achievements.
 - **Hero**: The virtual playing entity which is bound to a user and a game. There can be more than one hero per game.
 - **Achievement type identifier**:  Unique string representation of an achievement type; Only letters, numbers, hyphen and 
   underscore is allowed (e.g. "final-Fight_18" or "FINAL_FIGHT-18"); the length of the identifier has to be between 1 and 50 characters.
 - **Glory**: Amount of points the user gets when he earns achievements or achievement levels.
 
## Features
 - **Game achievements**: Achievements which can be earned within a game
 - **Hero achievements**: Achievements which can be earned by a specific hero within a game
 - **Hidden achievements**: Achievements can be shown at the moment they are available or hidden until they are earned by the hero/user
 - **Accretive achievements**: A flag which could be set for an achievement type, if the data can be collected over time or have to 
   be fulfilled at once
 - **Achievements levels**: Achievement types can have levels
 - **Achievement categories**: Achievement types can be grouped into categories
 - **Glory reward**: The is an option for achievements to grant the user a "glory" award when achieving it:
   - Glory reward: Player gets additional glory points when reaching an achievement
   - No glory: Player gets NO glory points when reaching an achievement

# Authentication
If you want to use the API you have to authenticate with setting the following http request headers:
 - **X-Auth-Token**: random generated string (e.g. "52173ae1cd3d52d7a3e54b88c001f9a5")
 - **X-Auth-Signature**: MD5 hash calculated like: md5(token + secret + timestamp)
 - **X-Auth-Timestamp**: current unix timestamp (e.g. "1543004482555")

The secret where the signature is generated from is a strong password string which only the client and the server knows.

If the timestamp is older than 10 seconds, the server will not authenticate the client!

# API functions
All functions which could return errors will provide them in JSON format:
<pre>
{
    "errorMessage": [MESSAGE]
    "errorCode": [CODE]
}
</pre>

## getAchievementTypes
Fetches a full list of all achievement types for a specific game including all stored meta data.
### Parameters
 - `(int) gameId`: game-ID to which the achievement should be grouped to.
### Return values
List of achievement types, e.g.:
<pre>
[
   {
      "achievementTypeIdentifier":"FIGHTER",
      "achievementTypeId": 17,
      "category": 7,
      "isAccretive": 1,
      "isVisible": 1,
      "levels":[
         {
            "level": 1,
            "value": 10,
            "gloryReward": 1
         },
         {
            "level": 2,
            "value": 25,
            "gloryReward": 3
         },
         {
            "level": 3,
            "value": 75,
            "gloryReward": 9
         }
      ]
   },
   {
      "achievementTypeIdentifier": "DEFENDER",
      "achievementTypeId": 22,
      "category": 0,
      "isAccretive": 0,
      "isVisible": 1,
      "levels":
      [
         {
            "level": 1,
            "value": 100,
            "gloryReward": 1
         }
      ]
   }
]
</pre>

## addAchievementType
A new achievement type is added to the system. There has to be at least one level even for "non-levelled"-achievements.
### Parameters
 - `(string) achievementTypeIdentifier`: Unique string identifier for the achievement type
 - `(string) achievementTypeData`:
    JSON encoded achievement type data, e.g.:
    <pre>
    {
        "category": 3,
        "isAccretive": 0|1,
        "isVisible": 0|1,
        "levels": 
        [
            {"level": 1, "value": 10, "glory": 3 },
            {"level": 2, "value": 25, "glory": 5 },
            {"level": 3, "value": 50, "glory": 7 }
        ]
    }
    </pre>
 - `int gameId`: game ID to which the achievement is bound
### Return values
Returns `{"success": true}` if the operation was successful.
### Exceptions
    100: Invalid gameId
    101: Invalid achievement type identifier
    102: Achievement type already present
    103: Achievement level missing
    106: Non-accretive achievements can only have one level
    999: General error

## addData
Increases the data for a specific achievement for a hero or user.
### Parameters
 - `string achievementTypeIdentifier`: Unique string identifier for the achievement type
 - `int value`: value to be stored/increased
 - `int heroId`: hero ID who earns the achievement (optional parameter; leave blank or set to `0` if not needed)
 - `int userId`: user ID who earns the achievement
 - `int gameId`: game ID to which the achievement is bound
### Return values
Returns `{"success": true, "hasAchieved": true|false}` if the operation was successful.
The `hasAchieved` value declares if the hero earned a new achievement or a new level. 
### Exceptions
    100: Invalid gameId
    105: Achievement type does not exist for this game
    161: Invalid userId
    162: Invalid value
    999: General error

## getHeroAchievements
Fetches a list of achievements an hero of the user has earned for a specific game
### Parameters
 - `int heroId`: hero ID who earned the achievements
 - `int userId`: user ID who earned the achievements
 - `int gameId`: game ID to which the achievements are bound
### Return values
List of achievements, e.g.:
<pre>
[
    {
        "achievedTime": 1541082283,
        "achievementId": 38495,
        "achievementTypeId":9,
        "achievementTypeIdentifier": "FIGHTER",
        "category": 6,
        "gloryFetched": 1541168683,
        "gloryReward": 7,
        "isAchieved": true,
        "isVisible": true,
        "level": 1,
        "maxValue": 10,
        "value": 10,
    },
    {
        "achievedTime": 0,
        "achievementId": 44958,
        "achievementTypeId":17,
        "achievementTypeIdentifier": "FIGHTER",
        "category": 6,
        "gloryFetched": 0,
        "gloryReward": 12,
        "isAchieved": false,
        "isVisible": true,
        "level": 2,
        "maxValue": 25,
        "value": 14,
    },
    {
        "achievedTime": 1541082283,
        "achievementId": 33928,
        "achievementTypeId":26,
        "achievementTypeIdentifier": "DEFENDER",
        "category": 7,
        "gloryFetched": 0,
        "gloryReward": 4,
        "isAchieved": true,
        "isVisible": true,
        "level": 1,
        "maxValue": 100,
        "value": 100,
    }
]
</pre>

## getAchievements
Fetches a list of achievements the user has earned in a specific game which are not bound to any hero.
### Parameters
 - `int userId`: user ID who earned the achievements
 - `int gameId`: game ID to which the achievements are bound
### Return values
List of achievements (example see "getHeroAchievements")

## fetchGlory
Fetches glory from an earned achievement and grants it to the user.
### Parameters
 - `int achievementId`: Unique achievement ID given from "getAchievements" or "getHeroAchievements" 
 - `int userId`: user ID who earned the achievement
 - `int gameId`: game ID to which the achievement is bound
### Return values
Returns `{"success": true}` if the operation was successful.
### Exceptions
    110: Achievement not found
    150: Glory already fetched
    151: Cannot fetch glory
    170: Invalid params
    999: General error

## getGlory
Returns the glory amount a user earned for a specific game.
### Parameters
 - `int userId`: user ID who earned the glory points
 - `int gameId`: game ID to which the glory is bound
### Return values
Glory amount as `{"gloryAmount": [AMOUNT]}`


# Limitations
 - Values of `"category"`,`"gloryReward"` and `"gameId"` cannot be bigger than 65535
 - Values of `"level"` cannot be bigger than 255
 - There is also a maximum of 65535 achievement types overall

# Installation & setup
## Requirements
 - MySQL server (tested with v5.5.40)
 - PHP (tested with v5.5.38)
 - Webserver (tested with lighttpd/1.4.31)
## Example for webserver configuration
In this case we use [lighttpd](https://www.lighttpd.net/) for setting up a webserver with ASAAM.
The file /etc/lighttpd/lighttpd.conf could look like:
<pre>
server.modules = (
    "mod_expire",
    "mod_access",
    "mod_alias",
    "mod_compress",
    "mod_redirect",
    "mod_fastcgi",
    "mod_accesslog",
    "mod_rewrite",
)

fastcgi.server = ( ".php" => ((
    "bin-path" => "/usr/bin/php-cgi",
    "socket" => "/tmp/php.sock"
)))

# ...default lighttpd settings here...

$HTTP["host"] == "asaam.lan" {
    server.document-root = "/var/www/asaam/public"
    url.rewrite-if-not-file = ("^(.*)$" => "/index.php?q=$1")
}
</pre>
## Setup
 - Copy the code to your desired folder (e.g. `/var/www/asaam`)
 - Copy `config.ini.sample` to `config.ini` and modify the settings to your wishes and needs
   - For maximum security change definitly the settings in your production/live environment
     `ENVIRONMENT` to `"production"`, 
     `AUTH_ENABLED` to `1`,
     `AUTH_SECRET` to a new cryptic string value (e.g. use [Password generator](https://passwordsgenerator.net/))
     and `ALLOWED_CLIENTS` to the IP address you want to use as a client
 - Execute the init script: `cd /var/www/asaam/scripts/init && php -f init.php`
 - Adapt the setting of your webserver (see example above)  