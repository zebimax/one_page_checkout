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

    public function __construct(\MysqlDb $db)
    {
        parent::__construct($db, self::PAYMENT_ORDER_TABLE);
    }

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

}