<?php

class MysqlDb
{
    /** @var MysqlDb $instance */
    private static $instance;
    protected $connection;

    private $host;
    private $port = 3601;
    private $user;
    private $password = false;
    private $db;

    private static function checkInstance()
    {
        if (empty(self::$instance)) {
            throw new Exception('Instance is not initialized!');
        }
    }

    private function __clone() {}

    /**
     * @param array $params
     */
    private function __construct(array $params = array())
    {
        $this->setParams($params);
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->db);
        $this->connection->set_charset("utf8");
    }

    /**
     * @param array $params
     * @return MysqlDb
     */
    public static function getInstance(array $params = array())
    {
        if (empty(self::$instance)) {
            self::$instance = new MysqlDb($params);
        }

        return self::$instance;
    }

    /**
     * @return mysqli
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param array $params
     */
    private function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'host':
                    $this->host = $value;
                    break;
                case 'port':
                    $this->port = $value;
                    break;
                case 'user':
                    $this->user = $value;
                    break;
                case 'password':
                    $this->password = $value;
                    break;
                case 'db':
                    $this->db = $value;
                    break;
                default:
                    break;
            }
        }
        $this->checkParams();
    }

    private function checkParams()
    {
        $errors = array();

        if (!$this->host) {
            $errors[] = 'Not defined mysql server';
        }
        if (!$this->user) {
            $errors[] = 'Not defined mysql user';
        }
        if ($this->password === false) {
            $errors[] = 'Not defined mysql password';
        }
        if (!$this->db) {
            $errors[] = 'Not defined mysql db';
        }
        if (!empty($errors)) {
            throw new \Exception(implode(PHP_EOL, $errors));
        }
    }

    public static function getAllTables()
    {
        self::checkInstance();
        $mysqli_result = self::$instance->getConnection()->query('SHOW TABLES');
        $tables = [];
        while ($row = $mysqli_result->fetch_row()) {
            $tables[] = $row[0];
        }
        return $tables;
    }

    public static function escape($string)
    {
        self::checkInstance();
        return self::$instance->real_escape_string($string);
    }
}