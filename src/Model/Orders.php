<?php
/**
 * Created by PhpStorm.
 * User: Zebimax
 * Date: 16.02.15
 * Time: 11:44
 */

namespace Model;


class Orders extends Model
{
    public function __construct(\MysqlDb $db)
    {
        parent::__construct($db, self::ORDERS_TABLE);
    }

    public function makeOrder(array $data, array $paymentData)
    {
        $this->startTransaction();
        $this->query(sprintf('INSERT INTO %s () VALUES()', $this->table));
        $orderId = $this->db->getConnection()->insert_id;
        if (!$orderId) {
            $this->rollback();
            return false;
        }
        $this->query(sprintf(
            'INSERT INTO %s(
                      order_id,
                      first_name,
                      last_name,
                      email,
                      phone,
                      profession,
                      post_code,
                      house_number,
                      street,
                      location,
                      quantity,
                      country_id
                      ) VALUES (%d, "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", %d, %d)',
            self::ORDER_INFO_TABLE,
            $orderId,
            $this->escape($data['first_name']),
            $this->escape($data['last_name']),
            $this->escape($data['email']),
            $this->escape($data['phone']),
            $this->escape($data['profession']),
            $this->escape($data['post_code']),
            $this->escape($data['house_number']),
            $this->escape($data['street']),
            $this->escape($data['location']),
            $data['quantity'],
            $this->getCountryIdByCode($data['country'])
        ));
        if ($error = $this->db->getConnection()->error) {
            $this->rollback();
            return false;
        }
        $this->query(sprintf(
            'INSERT INTO %s (order_id, payment_method_id, data) VALUES (%d, %d, "%s")',
            self::PAYMENT_ORDER_TABLE,
            $orderId,
            $this->getPaymentMethodIdByCode($data['payment_method']),
            $this->escape(json_encode($paymentData))
        ));
        if ($error = $this->db->getConnection()->error) {
            $this->rollback();
            return false;
        }
        $this->commit();
        return $orderId;
    }

}