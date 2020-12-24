<?php

/******************************************************************************************************************\
 *File:    database.php                                                                                          *
 *Author:  Christopher Perault                                                                                     *
 *Project: Roofmasters CMS (Customer Management System)                                                            *
 *Date:    August 8th, 2019                                                                                        *
 *Purpose: This class will set up the connection to MySql database                                                 *
\******************************************************************************************************************/

class Database
{
    private static $dsn = DB_DSN;
    private static $username = DB_UN;
    private static $password = DB_UP;
    private static $db;
    private function __construct()
    {
    }
    public static function getDB()
    {
        if (!isset(self::$db)) {
            try {
                self::$db = new PDO(self::$dsn, self::$username, self::$password);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $error_message = $e->getMessage();
                exit($error_message);
            }
        }
        return self::$db;
    }
}
