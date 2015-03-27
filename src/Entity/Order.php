<?php
namespace Quartet\BaseApi\Entity;

use Quartet\BaseApi\Entity\Subset\ContactInformation;

class Order extends ContactInformation implements EntityInterface
{
    public $unique_key;
    public $ordered;
    public $payment;
    public $shipping_fee;
    public $cod_fee;
    public $total;
    public $remark;
    public $add_comment;
    public $terminated;

    /**
     * @var Subset\OrderReceiver
     */
    public $order_receiver;

    /**
     * @var Subset\OrderDiscount
     */
    public $order_discount;

    /**
     * @var Subset\CCPaymentTransaction
     */
    public $c_c_payment_transaction;

    /**
     * @var Subset\CvsPaymentTransaction
     */
    public $cvs_payment_transaction;

    /**
     * @var Subset\BtPaymentTransaction
     */
    public $bt_payment_transaction;

    /**
     * @var Subset\OrderItem[]
     */
    public $order_items;
}
