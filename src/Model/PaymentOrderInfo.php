<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 11:44
 */

namespace Model;


class PaymentOrderInfo extends Model
{
    const STATUS_ACCEPTED = 1;
    const STATUS_SUCCESS = 2;

    /**
     * @param \MysqlDb $db
     */
    public function __construct(\MysqlDb $db)
    {
        parent::__construct($db, self::PAYMENT_ORDER_TABLE);
    }

    /**
     * @param $orderId
     * @param $paymentOrderId
     * @return bool
     */
    public function setPaymentOrder($orderId, $paymentOrderId)
    {
        $this->query(sprintf(
            'UPDATE %s SET payment_order_id = "%s" WHERE order_id = %d LIMIT 1',
            $this->table,
            $this->escape($paymentOrderId),
            $orderId
        ));
        $affectedOrder = $this->db->getConnection()->affected_rows;
        if ($affectedOrder) {
            $updateStatus = $this->updateStatus($orderId, self::STATUS_ACCEPTED);
        } else {
            return false;
        }
        return $updateStatus ? $orderId : false;
    }

    /**
     * @param $paymentOrderId
     * @param array $paymentData
     * @return int
     */
    public function updatePaymentInfo($paymentOrderId, array $paymentData)
    {
        $paymentInfo = $this->getPaymentInfo($paymentOrderId);
        $this->query(sprintf(
            'UPDATE %s SET data = "%s" WHERE payment_order_id = "%s"',
            $this->table,
            $this->escape(json_encode(array_merge($paymentInfo, $paymentData))),
            $paymentOrderId
        ));
        return $this->db->getConnection()->affected_rows;
    }

    /**
     * @param $orderId
     * @param $status
     * @return bool
     */
    public function updateStatus($orderId, $status)
    {
        $this->query(sprintf(
            'UPDATE %s SET status = %d WHERE order_id = %d LIMIT 1',
            $this->table,
            $status,
            $orderId
        ));
        return $this->db->getConnection()->affected_rows ? $status : false;
    }

    /**
     * @param $paymentOrderId
     * @param $status
     * @return bool
     */
    public function updateStatusByPaymentOrderId($paymentOrderId, $status)
    {
        $this->query(sprintf(
            'UPDATE %s SET status = %d WHERE payment_order_id = "%s"',
            $this->table,
            self::STATUS_SUCCESS,
            $this->escape($paymentOrderId)
        ));
        return $this->db->getConnection()->affected_rows ? $status : false;
    }

    /**
     * @param $paymentOrderId
     * @return bool
     * @throws \Exception
     */
    public function getPaymentByPaymentOrderId($paymentOrderId)
    {
        $methodRows = $this->tableSelect(
            ['payment' => 'pm.code'],
            [sprintf('%s pm on pm.id = %s.payment_method_id', self::PAYMENT_METHODS_TABLE, $this->table)],
            [sprintf('%s.payment_order_id = "%s"', $this->table, $this->escape($paymentOrderId))],
            ['LIMIT 1']
        );
        if (isset($methodRows[0]['payment'])) {
            return $methodRows[0]['payment'];
        } else {
            return false;
        }
    }

    /**
     * @param $paymentOrderId
     * @return array|bool
     * @throws \Exception
     */
    public function getInfo($paymentOrderId)
    {
        return $this->tableSelect(
            [
                'payment_data' => $this->table . '.data',
                'payment_code' => 'pm.code',
                'payment_name' => 'pm.name',
                'first_name', 'last_name',
                'email', 'phone',
                'profession', 'post_code',
                'house_number', 'street',
                'location', 'quantity',
                'country_code' => 'c.iso1_code',
                'oi.order_id',
                'payment_order_id',
                'company',
                'city'
            ],
            [
                sprintf('%s pm on pm.id = %s.payment_method_id', self::PAYMENT_METHODS_TABLE, $this->table),
                sprintf('%s oi on oi.order_id = %s.order_id', self::ORDER_INFO_TABLE, $this->table),
                sprintf('%s c on c.id = %s.country_id', self::COUNTRIES_TABLE, 'oi')
            ],
            [sprintf('%s.payment_order_id = "%s"', $this->table, $this->escape($paymentOrderId))],
            ['LIMIT 1'],
            true
        );
    }

    /**
     * @param $paymentOrderId
     * @return array|mixed
     * @throws \Exception
     */
    public function getPaymentInfo($paymentOrderId)
    {
        $row = $this->tableSelect(
            ['data'], [], [sprintf('payment_order_id = "%s"', $this->escape($paymentOrderId))], ['LIMIT 1'], true
        );

        return (!empty($row['data']))
            ? (array)json_decode($row['data'], JSON_OBJECT_AS_ARRAY)
            : [];
    }

    public function getStatus($paymentOrderId)
    {
        $row = $this->tableSelect(
            ['status'],
            [],
            [sprintf('payment_order_id = "%s"', $this->escape($paymentOrderId))],
            ['LIMIT 1'],
            true
        );
        return isset($row['status']) ? $row['status'] : false;
    }
}