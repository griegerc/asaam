<?php

class Gpf_DatabaseStmt
{
    /**
     * Retrieves a prepared db statement
     * @param string $type
     * @throws Exception
     * @return string
     */
    public static function getStatement($type)
    {
        $sqlStatements = array(
            'getConfigValue' =>
                'SELECT `value` FROM `config` WHERE `key` = ?;',

            'setConfigValue' =>
                'REPLACE INTO `config` SET `key` = ?, `value`= ?;'
        );

        // merging app specific db statements
        $appStatementClass = Gpf_Config::get('DEFAULT_DB_STATEMENTS');
        $appStatements = $appStatementClass::$sqlStatements;
        $sqlStatements = array_merge($appStatements, $sqlStatements);

        if (isset($sqlStatements[$type])) {
            return $sqlStatements[$type];
        } else {
            throw new Exception('SQL statement ['.$type.'] not found.');
        }
    }
}
