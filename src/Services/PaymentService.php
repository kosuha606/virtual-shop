<?php

namespace kosuha606\VirtualShop\Services;

use kosuha606\VirtualShop\Model\PaymentVm;

class PaymentService
{
    /**
     * @param $id
     * @return PaymentVm
     * @throws \Exception
     */
    public function findPaymentById($id)
    {
        $payment = PaymentVm::one([
            'where' => [
                ['=', 'id', $id]
            ]
        ]);

        return $payment;
    }
}
