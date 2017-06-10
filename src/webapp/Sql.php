<?php

namespace analysiswebapp\webapp;

use analysiswebapp\webapp\models\User;
use \MongoDB\Driver\Manager;
class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up()
    {
        $q1 = "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, user VARCHAR(20), pass VARCHAR(20), first_name varchar(20), last_name varchar(20), email varchar(20), isadmin INTEGER, failed_logins INTEGER, first_failed_login INTEGER);";

        self::$pdo->exec($q1);

        print "[analysiswebapp] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
    }


    static function insertDummyUsers()
    {
        /*$hash1 = Hash::make(bin2hex(openssl_random_pseudo_bytes(2)));*/
        $hash1 = Hash::make('test');
        $hash2 = Hash::make('test1');

        $q3 = "INSERT INTO users(user, pass, first_name, last_name, email, isadmin) VALUES ('support', '$hash1', 'Support', 'Support', 'support@automaticanalysis.eu', 1)";
        $q4 = "INSERT INTO users(user, pass, first_name, last_name, email, isadmin) VALUES ('test1', '$hash2', 'Approv', 'Emails', 'inbox@automaticanalysis.eu', 0)";

        self::$pdo->exec($q3);
        self::$pdo->exec($q4);

        print "[analysiswebapp] Done inserting dummy users.".PHP_EOL;
    }

    static function down()
    {
		 try{
			$q8 = "DROP TABLE IF EXISTS users";

			self::$pdo->exec($q8);
	}catch(Exception $e){

	}

		$mongoDb = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
		$bulk = new \MongoDB\Driver\BulkWrite;
		$bulk->delete([]);
		$writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY);
		$result = $mongoDb->executeBulkWrite('grabemails.emails', $bulk, $writeConcern);

	        print "[analysiswebapp] Done deleting emails and all SQL tables. ".PHP_EOL;
    }
}
try {
    // Create (connect to) SQLite database
    Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo $e->getMessage();
    exit();
}
