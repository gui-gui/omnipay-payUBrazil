<?php

namespace Omnipay\PayUBrazil\Message;

class RefundRequest extends AbstractRequest
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
        $this->validate('transactionReference', 'orderReference');
        
        $data = array();

        $data['transaction']['type'] = 'REFUND';
        $data['transaction']['order']['id'] = $this->getOrderReference();
        $data['transaction']['parentTransactionId'] = $this->getTransactionReference();        
        $data['transaction']['reason'] = $this->getReason() ?: 'Omnipay Refund';
     
        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new RefundResponse($this, $data);
    }

}
