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
    protected $db;
    protected $table;
    private $tables = [];
    const ORDERS_TABLE = 'orders';
    const ORDER_INFO_TABLE = 'order_info';
    const PAYMENT_METHODS_TABLE = 'orders';
    const PAYMENT_ORDER_TABLE = 'payment_order_info';
    const COUNTRIES_TABLE = 'countries';

    public function __construct(\MysqlDb $db, $table = null)
    {
        $this->db = $db;
        $this->setTable($table);
    }

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

    public function getRow($sql)
    {
        $result = $this->db->getConnection()->query($sql);
        if ($result) {
            return $result->fetch_assoc();
        }
        return false;
    }

    public function tableSelect(array $fields = [], array $join = [], array $where = [])
    {
        if (!$this->table) {
            throw new \Exception('table is not set!');
        }
        return $this->getRows(sprintf(
            'SELECT %s %s FROM %s %s',
            $this->makeFields($fields),
            $this->makeJoins($join),
            $this->table,
            $this->makeWhere($where)

        ));
    }

    public function escape($string)
    {
        return $this->db->escape($string);
    }

    protected function query($sql)
    {
        return $this->db->getConnection()->query($sql);
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

    private function makeWhere(array $where)
    {
        $whereSql = '';
        foreach ($where as $condition) {
            $whereSql .= "AND {$condition}";
        }
        return $whereSql ? ' WHERE' . ltrim($whereSql, 'AND') : '';
    }

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

    protected function startTransaction()
    {
        return $this->db->getConnection()->begin_transaction();
    }

    protected function commit()
    {
        return $this->db->getConnection()->commit();
    }

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

    protected function getCountryIdByCode($code)
    {
        $row = $this->getRow(sprintf(
                'SELECT id FROM %s WHERE iso3_code = "%s"'),
            Model::COUNTRIES_TABLE,
            $this->escape($code)
        );
        return $row ? $row['id'] : false;
    }

    protected function getPaymentMethodIdByCode($code)
    {
        $row = $this->getRow(sprintf(
                'SELECT id FROM %s WHERE code = "%s"'),
            Model::PAYMENT_METHODS_TABLE,
            $this->escape($code)
        );
        return $row ? $row['id'] : false;
    }
}