<?php

namespace Omnipay\PayUBrazil\Message;

class RefundRequest extends AuthorizeRequest
{
    public function getReason()
    {
        return $this->getParameter('reason');
    }    
    
    public function setReason($value)
    {
        return $this->setParameter('reason', $value);
    }

    public function getData()
    {
        $data = parent::getData();
        $data['transaction']['type'] = 'REFUND';
        $data['transaction']['reason'] = $this->getReason() ?: 'Omnipay refund';
        return $data;
    }
}
