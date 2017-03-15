<?php

namespace Omnipay\PayUBrazil\Message;

class FetchTransactionRequest extends AbstractRequest
{

    protected $liveEndpoint = 'https://api.payulatam.com/reports-api/4.0/service.cgi';
    protected $testEndpoint = 'https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi';

    public function getData()
    {
        $data = array();
        $this->validate('transactionId');

        $data['command'] = 'ORDER_DETAIL_BY_REFERENCE_CODE';
        $data['details']['referenceCode'] = $this->getTransactionId();                

        return $data;
    }
}
