<?php

namespace Omnipay\PayUBrazil\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData()
    {
        $data = parent::getData();
        $data['transaction']['type'] = 'AUTHORIZATION_AND_CAPTURE';
        return $data;
    }
}
