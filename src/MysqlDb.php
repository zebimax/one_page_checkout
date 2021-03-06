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
     * @throws Exception
     */
    private static function checkInstance()
    {
        if (empty(self::$instance)) {
            throw new Exception('Instance is not initialized!');
        }
    }

    /**
     *
     */
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

    /**
     * @throws Exception
     */
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

    /**
     * @return array
     * @throws Exception
     */
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

    /**
     * @param $string
     * @return string
     * @throws Exception
     */
    public static function escape($string)
    {
        self::checkInstance();
        return self::$instance->getConnection()->real_escape_string($string);
    }

    public function startTransaction()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            $this->connection->begin_transaction();
        } else {
            $this->connection->autocommit(false);
        }
    }

    public function endTransaction()
    {
        if (!version_compare(PHP_VERSION, '5.5.0', '>=')) {
            $this->connection->autocommit(true);
        }
    }
}