<?php

namespace Omnipay\PayUBrazil;

use Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'PayU Brazil';
    }
    
    /**
     * Get the gateway parameters
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '508029',
            'accountId' => '512327',
            'apiLogin' => '',
            'apiKey' => '',
            'testMode' => false,
        );
    }
    
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }
    
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getApiLogin()
    {
        return $this->getParameter('apiLogin');
    }
    
    public function setApiLogin($value)
    {
        return $this->setParameter('apiLogin', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }
    
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getAccountId()
    {
        return $this->getParameter('accountId');
    }
    
    public function setAccountId($value)
    {
        return $this->setParameter('accountId', $value);
    }
    
    public function getTestMode()
    {
        return $this->getParameter('testMode');
    }
    
    public function setTestMode($value)
    {
        return $this->setParameter('testMode', $value);
    }
    

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayUBrazil\Message\AuthorizeRequest', $parameters);
    }
    
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayUBrazil\Message\CaptureRequest', $parameters);
    }
    
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayUBrazil\Message\PurchaseRequest', $parameters);
    }
    
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PayUBrazil\Message\RefundRequest', $parameters);
    }
    

    // public function void(array $parameters = array())
    // {
    //     return $this->createRequest('\Omnipay\PayUBrazil\Message\VoidRequest', $parameters);
    // }
    
    // public function calculateInstallments(array $parameters = array())
    // {
    //     return $this->createRequest('\Omnipay\PayUBrazil\Message\InstallmentsRequest', $parameters);
    // }
    
    // public function fetchTransaction(array $parameters = array())
    // {
    //     return $this->createRequest('\Omnipay\PayUBrazil\Message\FetchTransactionRequest', $parameters);
    // }
}
