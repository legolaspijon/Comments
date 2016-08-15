<?php


class Db
{
    protected static $_instance = null;
    private $connection;

    private function __construct()
    {
        $paramsPath = ROOT . '/config/db_params.php';
        $params = include($paramsPath);
        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $this->connection = new PDO($dsn, $params['username'], $params['password'], $opt);
    }

    public static function getInstance(){
        if(self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function getConnection(){
        return $this->connection;
    }


    private function __clone(){}
    private function __wakeup(){}

}