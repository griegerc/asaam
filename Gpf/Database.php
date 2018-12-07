<?php

class Gpf_Database
{
    /** @var PDO */
    private $_db;

    /** @var int */
    private $_lastAffectedRowCount = 0;

    /** @var Gpf_Database */
    private static $_instance = NULL;

    /** @var int */
    private $_transactionStack = 0;

    /**
     * Instance of the database
     * @return Gpf_Database
     */
    public static function getInstance()
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self(
                Gpf_Config::get('MYSQL_HOST'),
                Gpf_Config::get('MYSQL_USERNAME'),
                Gpf_Config::get('MYSQL_PASSWORD'),
                Gpf_Config::get('MYSQL_DATABASE'));
        }
        return self::$_instance;
    }

    /**
     * Connect to an ODBC database using driver invocation.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     */
    private function __construct($host, $username, $password, $database)
    {
        $dsn = 'mysql:dbname='.$database.';host='.$host.';charset=utf8';
        $this->_db = new PDO($dsn, $username, $password);
        $this->_db->exec("SET NAMES 'utf8';");
        $this->_db->exec("SET SESSION sql_mode=''STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
    }

    /**
     * @return void
     */
    private function __clone() {}

    /**
     * Shutdown the database connection and release resources.
     * @return void
     */
    public function __destruct()
    {
        $this->_db = NULL;
    }

    /**
     * Prepares, execute and fetch an SQL-statement by a given type.
     * @param string $queryType
     * @param array|bool|false|string $params
     * @param bool $firstEntryOnly
     * @param bool $doPrepare Be careful: Unquoted query parameters
     * @return array
     */
    public function inquiry($queryType, $params = false, $firstEntryOnly = false, $doPrepare = true)
    {
        if (!is_array($params)) {
            $params = array($params);
        }

        $sql = preg_replace("/\\s\\s+/", " ", Gpf_DatabaseStmt::getStatement($queryType));

        if ($doPrepare === true) {
            $sqlStatement = $this->_db->prepare($sql);
            $this->_lastAffectedRowCount = $sqlStatement->rowCount();
            if ($sqlStatement === false) {
                trigger_error('SQL-Statement could not prepared. Inquiry: "'.$queryType.'"', E_USER_ERROR);
            } else {
                $isBound = $sqlStatement->execute($params);
                $this->_lastAffectedRowCount = $sqlStatement->rowCount();
                if ($isBound === false) {
                    trigger_error('Execution of prepared statement failed. Inquiry: "'.$queryType.'"', E_USER_ERROR);
                }
            }
        } else {
            $sql = vsprintf($sql, $params);
            $sqlStatement = $this->_db->query($sql);
            $this->_lastAffectedRowCount = $sqlStatement->rowCount();
        }

        if ($firstEntryOnly === true) {
            return $sqlStatement->fetch(PDO::FETCH_ASSOC);
        } else {
            return $sqlStatement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Queries only one result from the database
     * @param string $queryType
     * @param array|bool|false|string $params
     * @param bool $doPrepare Be careful: Unquoted query parameters
     * @return array
     */
    public function inquiryOne($queryType, $params = false, $doPrepare = true)
    {
        return $this->inquiry($queryType, $params, true, $doPrepare);
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     * @param string $sql
     * @return bool|int
     */
    public function execute($sql)
    {
        return $this->_db->exec($sql);
    }

    /**
     * Executes an SQL statement, returning the result as an array
     * @param string $sql
     * @param bool $firstEntryOnly
     * @return mixed
     */
    public function query($sql, $firstEntryOnly=false)
    {
        /* @var PDOStatement $sqlStatement */
        $sqlStatement = $this->_db->query($sql);
        $this->_lastAffectedRowCount = $sqlStatement->rowCount();
        if ($firstEntryOnly === true) {
            return $sqlStatement->fetch(PDO::FETCH_ASSOC);
        } else {
            return $sqlStatement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Initiates a transaction
     * @return void
     */
    public function beginTransaction()
    {
        if ($this->_transactionStack == 0) {
            $this->_db->beginTransaction();
        }
        $this->_transactionStack++;
    }

    /**
     * Commits a transaction
     * @return void
     */
    public function commit()
    {
        if ($this->_transactionStack == 1) {
            $this->_db->commit();
        }
        $this->_transactionStack--;
    }

    /**
     * Rolls back a transaction
     * @return void
     */
    public function rollback()
    {
        if ($this->_transactionStack == 1) {
            $this->_db->rollBack();
        }
        $this->_transactionStack--;
    }

    /**
     * Retrieves the ID generated for an AUTO_INCREMENT column by the previous INSERT query
     * @return int
     */
    public function getLastInsertedId()
    {
        return (int)$this->_db->lastInsertId();
    }

    /**
     * Retrieves the last affected row count of the last query
     * @return int
     */
    public function getAffectedRows()
    {
        return $this->_lastAffectedRowCount;
    }

    /**
     * Fetches the version of the MySQL server
     * @return string
     */
    public function getVersion()
    {
        return $this->_db->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Preforms a database migration to the highest possible version
     * @return void
     */
    public function migrate()
    {
        $currentVersion = -1;
        $ver = Gpf_Config::getVar(Gpf_Config::DB_VERSION);
        if ($ver !== NULL) {
            $currentVersion = (int)$ver;
        }

        $path = GPF_BASEPATH . '/database/migration';
        if (!file_exists($path)) {
            return;
        }

        $handle = opendir($path);
        $files = array();
        while (($file = readdir($handle)) == true) {
            if (substr($file, -4) == '.sql') {
                if (intval($file) > $currentVersion) {
                    $files[$file] = intval($file);
                }
            }
        }

        // Sort newer sql files by version number
        asort($files);
        $maxver = 0;
        foreach ($files as $filename => $version) {
            $this->_importSQL($path . '/' . $filename);
            Gpf_Logger::info('Migrated DB to version #'.$version, 'SYSTEM');
            if ($version > $maxver) {
                $maxver = $version;
            }
        }

        if ($maxver > 0) {
            Gpf_Config::setVar(Gpf_Config::DB_VERSION, $maxver);
        }
    }

    /**
     * Imports a sql file into the database
     * @param string $filename
     * @return void
     */
    private function _importSQL($filename)
    {
        $import = file_get_contents($filename);
        $queries = preg_split("/[;]+/", $import);

        foreach ($queries as $query) {
            $query = trim($query).';';
            if ($query != ';') {
                $this->execute($query);
            }
        }
    }
}
