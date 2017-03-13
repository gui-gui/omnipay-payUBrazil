<?php

namespace Omnipay\PayUBrazil\Message;

class CaptureRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('transactionReference');
        $this->validate('orderReference');

        $data = array();
        $data['transaction']['order']['id'] = $this->getOrderReference();
        $data['transaction']['type'] = 'CAPTURE';
        $data['transaction']['parentTransactionId'] = $this->getTransactionReference();

        return $data;
    }
}
