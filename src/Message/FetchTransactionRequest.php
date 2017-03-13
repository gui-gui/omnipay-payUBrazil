<?php

namespace Omnipay\PayUBrazil\Message;

class FetchTransactionRequest extends AbstractRequest
{
    public function getData()
    {
        $data = array();
        $this->validate('transactionReference');

        $data['command'] = 'ORDER_DETAIL_BY_REFERENCE_CODE';
        $data['details']['referenceCode'] = $this->getTransactionId();                

        return $data;
    }
}
