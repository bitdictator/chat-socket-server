<?php

namespace Core;

use PDO;
use PDOException;

class Database
{

    private static string $hostname;
    private static string $databasename;
    private static string $username;
    private static string $password;
    private static PDO $pdo;


    function __construct(string $hostname, string $databasename, string $username, string $password)
    {
        self::$hostname = $hostname;
        self::$databasename = $databasename;
        self::$username = $username;
        self::$password = $password;

        self::connect();
    }

    /**
     * Connect to the database
     * 
     * @return bool successful connection
     */
    public static function connect(): bool
    {
        $dsn = "mysql:dbname=" . self::$databasename . ";host=" . self::$hostname . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_EMULATE_PREPARES  => false,
            PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION
        ];

        try {
            self::$pdo = new PDO($dsn, self::$username, self::$password, $options);

            // keep connection for 1 hour from last sql
            self::raw("SET SESSION wait_timeout=3600;");

            Console::out("Application successfully connected to the database", Console::COLOR_BLUE);
        } catch (PDOException $error) {
            Console::out("Database connection failed with error: {$error->getMessage()}", Console::COLOR_RED);
            return false;
        }

        return true;
    }

    /**
     * Ping the database server to check if connection is active
     * 
     * @return bool successful ping
     */
    public static function ping(): bool
    {
        try {
            self::$pdo->query("SELECT 1");
        } catch (PDOException $e) {
            self::connect();
        }

        return true;
    }

    /**
     * Checks if the connection with the database is active, if not then it reconencts
     * 
     * @return bool successful reconnection
     */
    public static function checkConenction(): bool
    {

        // if connection not active, reconnect
        if (!self::ping()) {
            return (self::connect()) ? true : false;
        }

        return true;
    }

    /**
     * Run raw sql query 
     * 
     * @param  string $sql       sql query
     * @return void
     */
    public static function raw($sql)
    {

        if (!self::checkConenction()) {
            return false;
        }

        self::$pdo->query($sql);
    }

    /**
     * Run sql query
     * 
     * @param  string $sql       sql query
     * @param  array  $args      params
     * @return object            returns a PDO object
     */
    public static function run($sql, $args = [])
    {
        if (!self::checkConenction()) {
            return false;
        }

        if (empty($args)) {
            return self::$pdo->query($sql);
        }

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($args);

        return $stmt;
    }

    /**
     * Get primary key of last inserted record
     */
    public static function lastInsertId()
    {
        if (!self::checkConenction()) {
            return false;
        }

        return self::$pdo->lastInsertId();
    }

    /**
     * Insert record
     * 
     * @param  string $table table name
     * @param  array $data  array of columns and values
     */
    public static function insert($table, $data)
    {

        //add columns into comma seperated string
        $columns = implode(',', array_keys($data));

        //get values
        $values = array_values($data);

        $placeholders = array_map(function ($val) {
            return '?';
        }, array_keys($data));

        //convert array into comma seperated string
        $placeholders = implode(',', array_values($placeholders));

        self::run("INSERT INTO $table ($columns) VALUES ($placeholders)", $values);

        return self::lastInsertId();
    }

    /**
     * Get arrray of records
     * 
     * @param  string $sql       sql query
     * @param  array  $args      params
     * @param  object $fetchMode set return mode ie object or array
     * @return object            returns single record
     */
    public static function row($sql, $args = [], $fetchMode = PDO::FETCH_OBJ)
    {
        return self::run($sql, $args)->fetch($fetchMode);
    }

    /**
     * Get arrrays of records
     * 
     * @param  string $sql       sql query
     * @param  array  $args      params
     * @param  object $fetchMode set return mode ie object or array
     * @return object            returns multiple records
     */
    public static function rows($sql, $args = [], $fetchMode = PDO::FETCH_OBJ)
    {
        return self::run($sql, $args)->fetchAll($fetchMode);
    }

    /**
     * Update record
     * 
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @param  array $where array of columns and values
     */
    public static function update($table, $data, $where)
    {
        //merge data and where together
        $collection = array_merge($data, $where);

        //collect the values from collection
        $values = array_values($collection);

        //setup fields
        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = ?,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        //setup where 
        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            $whereDetails .= $i == 0 ? "$key = ?" : " AND $key = ?";
            $i++;
        }

        $stmt = self::run("UPDATE $table SET $fieldDetails WHERE $whereDetails", $values);

        return $stmt->rowCount();
    }

    /**
     * Get number of records
     * 
     * @param  string $sql       sql query
     * @param  array  $args      params
     * @param  object $fetchMode set return mode ie object or array
     * @return integer           returns number of records
     */
    public static function count($sql, $args = [])
    {
        return self::run($sql, $args)->rowCount();
    }
}
