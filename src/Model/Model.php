<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 11:46
 */

namespace Model;


class Model
{
    /** @var \MysqlDb */
    protected $db;
    protected $table;
    private $tables = [];
    const ORDERS_TABLE = 'orders';
    const ORDER_INFO_TABLE = 'order_info';
    const PAYMENT_METHODS_TABLE = 'payment_methods';
    const PAYMENT_ORDER_TABLE = 'payment_order_info';
    const COUNTRIES_TABLE = 'countries';

    /**
     * @param \MysqlDb $db
     * @param null $table
     * @throws \Exception
     */
    public function __construct(\MysqlDb $db, $table = null)
    {
        $this->db = $db;
        $this->setTable($table);
    }

    /**
     * @param $sql
     * @return array
     */
    public function getRows($sql)
    {
        $data = [];
        $result = $this->db->getConnection()->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * @param $sql
     * @return array|bool
     */
    public function getRow($sql)
    {
        $result = $this->db->getConnection()->query($sql);
        if ($result) {
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * @param array $fields
     * @param array $join
     * @param array $where
     * @param array $params
     * @param bool $oneRow
     * @return array|bool
     * @throws \Exception
     */
    public function tableSelect(array $fields = [], array $join = [], array $where = [], array $params = [], $oneRow = false)
    {
        if (!$this->table) {
            throw new \Exception('table is not set!');
        }
        $sql = sprintf(
            'SELECT %s FROM %s %s  %s %s',
            $this->makeFields($fields),
            $this->table,
            $this->makeJoins($join),
            $this->makeWhere($where),
            implode(' ', $params)

        );
        return $oneRow ? $this->getRow($sql) : $this->getRows($sql);
    }

    /**
     * @param $string
     * @return string
     */
    public function escape($string)
    {
        return $this->db->escape($string);
    }

    /**
     * @param $sql
     * @return bool|\mysqli_result
     */
    protected function query($sql)
    {
        return $this->db->getConnection()->query($sql);
    }


    /**
     * @param array $sqls
     * @return bool
     */
    protected function transaction(array $sqls)
    {
        $this->startTransaction();
        $success = true;
        foreach ($sqls as $sql) {
            $this->query($sql);
            if ($this->db->getConnection()->error) {
                $success = false;
                break;
            }
        }
        return $this->finishTransaction($success);
    }

    /**
     * @return bool
     */
    protected function startTransaction()
    {
        return $this->db->getConnection()->begin_transaction();
    }

    /**
     * @return bool
     */
    protected function commit()
    {
        return $this->db->getConnection()->commit();
    }

    /**
     * @return bool
     */
    protected function rollback()
    {
        return $this->db->getConnection()->rollback();
    }

    /**
     * @param $success
     * @return bool
     */
    protected function finishTransaction($success)
    {
        return $success ? $this->commit() : $this->rollback();
    }

    /**
     * @param $code
     * @return bool
     */
    protected function getCountryIdByCode($code)
    {
        $row = $this->getRow(sprintf(
            'SELECT id FROM %s WHERE iso3_code = "%s"',
            Model::COUNTRIES_TABLE,
            $this->escape($code)
        ));
        return $row ? $row['id'] : false;
    }

    /**
     * @param $code
     * @return bool
     */
    protected function getPaymentMethodIdByCode($code)
    {
        $row = $this->getRow(sprintf(
            'SELECT id FROM %s WHERE code = "%s"',
            Model::PAYMENT_METHODS_TABLE,
            $this->escape($code)
        ));
        return $row ? $row['id'] : false;
    }

    /**
     * @param $table
     * @throws \Exception
     */
    private function setTable($table)
    {
        if ($table) {
            if (empty($this->tables)) {
                $this->tables = $this->db->getAllTables();
            }
            if (!in_array($table, $this->tables)) {
                throw new \Exception(sprintf('table %s doesn\'t exists', $table));
            }
            $this->table = $table;
        }
    }

    /**
     * @param array $fields
     * @return string
     */
    private function makeFields(array $fields)
    {
        $fieldsSql = '';
        foreach ($fields as $key => $field) {
            $fieldsSql .= $field;
            if (is_string($key)) {
                $fieldsSql .= " {$key}";
            }
            $fieldsSql .= ',';
        }
        if ($fieldsSql) {
            return rtrim($fieldsSql, ',');
        } else {
            return '*';
        }
    }

    /**
     * @param array $joins
     * @return string
     */
    private function makeJoins(array $joins)
    {
        $joinsSql = '';
        foreach ($joins as $key => $join) {
            if (is_string($join)) {
                $joinsSql .= " LEFT JOIN {$join}" ;
            } elseif (is_array($join) && isset($join['type']) && isset($join['join'])) {
                switch ($join['type']) {
                    case 'left':
                        $joinsSql .= " LEFT JOIN {$join['join']}";
                    break;
                    case 'inner':
                        $joinsSql .= " INNER JOIN {$join['join']}";
                    break;
                    default:
                    break;
                }
            }
        }
        return $joinsSql;
    }

    /**
     * @param array $where
     * @return string
     */
    private function makeWhere(array $where)
    {
        $whereSql = '';
        foreach ($where as $condition) {
            $whereSql .= "AND {$condition} ";
        }
        return $whereSql ? 'WHERE ' . ltrim($whereSql, ' AND') : '';
    }
}