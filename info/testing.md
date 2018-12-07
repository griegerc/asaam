# Testing protocol

## Authentication
- disable auth security `[passed]`
- enable auth security  `[passed]`
    - wrong credentials `[passed]`
    - valid credentials `[passed]`
    - timeout test      `[passed]`

## API
### getAchievementTypes `[passed]`
- wrong gameIDs         `[passed]`
- valid gameID          `[passed]`
- return format (JSON)  `[passed]`

### addAchievementType                  `[passed]`
- add "GET_ALL_ACHIEVEMENTS" type       `[passed]`
- add new achievement types             `[passed]`
    - with 1 level                      `[passed]`
    - with 2 levels                     `[passed]`
    - with category                     `[passed]`
    - without category                  `[passed]`
    - with isAccretive                  `[passed]`
    - without isAccretive               `[passed]`
    - with isVisible                    `[passed]`
    - without isVisible                 `[passed]`
- add existing achievement type         `[passed]`
- invalid gameID                        `[passed]`
- invalid achievement type identifier   `[passed]`
- accretive must not have >1 levels     `[passed]`
- return format (JSON)                  `[passed]`

### addData
- wrong parameters            `[passed]`
- add with value < maxValue   `[passed]`
    - until achieved          `[passed]`
- add with value = maxValue   `[passed]`
- non-accretive achievement   `[passed]`
- more levels (in some calls) `[passed]`
- more levels (in one call)   `[passed]`
- return format (JSON)        `[passed]`

### getHeroAchievements `[passed]`
- invalid parameters    `[passed]`
- valid parameters      `[passed]`
- return format (JSON)  `[passed]`

### getAchievements    `[passed]`
- invalid parameters   `[passed]`
- valid parameters     `[passed]`
- return format (JSON) `[passed]`

### fetchGlory         `[passed]`
- invalid achievement  `[passed]`
- already fetched      `[passed]`
- valid fetching       `[passed]`
- return format (JSON) `[passed]`

### getGlory                      `[passed]`
- wrong parameters (not existing) `[passed]`
- valid parameters                `[passed]`
- return format (JSON)            `[passed]`