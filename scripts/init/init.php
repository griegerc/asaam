<?php

if (php_sapi_name() != "cli") {
    exit('Run this script only in CLI mode!');
}

print PHP_EOL;
print ' ---------- ASAAM initialization script ---------- ';
print PHP_EOL;
print PHP_EOL;

print 'Are you sure you want to do this?  Type "yes" to continue.';
print PHP_EOL;
$handle = fopen ('php://stdin', 'r');
$line = fgets($handle);
if(trim($line) != 'yes'){
    exit;
}

require dirname(dirname(dirname(__FILE__))) . '/Gpf/init.php';
$db = Gpf_Database::getInstance();

/**
 * Cleaning database
 */
print 'Cleaning database...';
$db->execute('DROP DATABASE IF EXISTS '.Gpf_Config::get('MYSQL_DATABASE').';');
$db->execute('CREATE DATABASE '.Gpf_Config::get('MYSQL_DATABASE').';');
$db->execute('USE '.Gpf_Config::get('MYSQL_DATABASE').';');
print 'ok'.PHP_EOL;

/**
 * Inserting init DDL
 */
print 'Creating database structure...';
$db->execute('SET SQL_MODE = "TRADITIONAL,NO_ENGINE_SUBSTITUTION";');
$db->execute('SET FOREIGN_KEY_CHECKS = 0;');
$ddl = file_get_contents(GPF_BASEPATH.'/database/init.sql');
$ddl = explode(';', $ddl);
foreach ($ddl as $query) {
    if (trim($query) != '') {
        $db->execute($query);
    }
}
$db->execute('SET FOREIGN_KEY_CHECKS = 1;');
print 'ok'.PHP_EOL;

/**
 * Migration database
 */
print 'Migration database...';
$db->migrate();
print 'ok'.PHP_EOL;


print PHP_EOL;
print ' ---------- Finished ---------- ';
print PHP_EOL;
print PHP_EOL;