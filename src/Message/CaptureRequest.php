<?php

namespace Omnipay\PayUBrazil\Message;

class CaptureRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('transactionReference', 'orderReference');

        $data = array();
        
        $data['transaction']['type'] = 'CAPTURE';
        $data['transaction']['order']['id'] = $this->getOrderReference();
        $data['transaction']['parentTransactionId'] = $this->getTransactionReference();

        return $data;
    }
}
