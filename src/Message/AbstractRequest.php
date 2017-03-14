<?php

namespace Omnipay\PayUBrazil\Message;

use Omnipay\PayUBrazil\CreditCard;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{

    protected $liveEndpoint = 'https://api.payulatam.com/payments-api/4.0/service.cgi';
    protected $testEndpoint = 'https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi';
    // protected $testEndpoint = 'http://requestb.in/13hshws1';

   
    public function getEndpoint()
    {
        if ($this->getTestMode()) {
            return $this->testEndpoint;
        }
        return $this->liveEndpoint;
    }

    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    public function getCard()
    {
        return $this->getParameter('card');
    }

    public function setCard($value)
    {
        if ($value && !$value instanceof CreditCard) {
            $value = new CreditCard($value);
        }

        return $this->setParameter('card', $value);
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
    
    public function getOrderReference()
    {
        return $this->getParameter('orderReference');
    }
    
    public function setOrderReference($value)
    {
        return $this->setParameter('orderReference', $value);
    }

    public function getSignature()
    {
        return $this->getParameter('signature');
    }   
    
    public function setSignature()
    {
        $signature = md5($this->getApiKey() . '~' . $this->getMerchantId() . '~' . $this->getTransactionId() . '~' .  $this->getAmount()  . '~' . $this->getCurrency() );

        return $this->setParameter('signature', $signature);
    }

    protected function insertBaseRequestData($data)
    {
        $result = array_merge($this->getBaseRequestData(), $data);

        return $result;
    }

    protected function getBaseRequestData()
    {
        $data = array();
        $data['language'] = 'pt';
        $data['command'] = 'SUBMIT_TRANSACTION';
        $data['test'] = $this->getTestMode();

        $data['merchant']['apiKey'] = $this->getApiKey();
        $data['merchant']['apiLogin'] = $this->getApiLogin();

        return $data;
    }

    // protected function getPayerData()
    // {
    //     $card = $this->getCard();
    //     $payer = array();

    //     $payer['fullName'] = $card->getName();
    //     $payer['emailAddress'] = $card->getEmail();
    //     $payer['contactPhone'] = $this->formatPhone($card->getPhone());
        
    //     if(strlen($card->getHolderDocumentNumber()) == 11)
    //     {
    //         $payer['dniNumber'] = $this->formatCpf($card->getHolderDocumentNumber());
    //     }
        
    //     if(strlen($card->getHolderBusinessNumber()) == 14)
    //     {
    //         $payer['cnpj'] = $card->getHolderBusinessNumber();
    //     }

    //     $arrayAddress = $this->formatAddress($card->getAddress1() . ',' . $card->getAddress2());
    //     if(!empty($arrayAddress['street1'])) 
    //     {
    //         $payer['billingAddress'] = $arrayAddress;
    //     }
    //     $payer['billingAddress']['city'] = $card->getCity();
    //     $payer['billingAddress']['state'] = $card->getState();
    //     $payer['billingAddress']['country'] = $card->getCountry();
    //     $payer['billingAddress']['postalCode'] = $this->formatPostCode($card->getPostCode());
    //     $payer['billingAddress']['phone'] = $this->formatPhone($card->getPhone());

    //     return $payer;
    // }

    protected function getBuyerData()
    {
        $card = $this->getCard();
        $buyer = array();

        $buyer['fullName'] = $card->getName();
        $buyer['emailAddress'] = $card->getEmail();
        $buyer['contactPhone'] = $this->formatPhone($card->getShippingPhone());

        if(strlen($card->getHolderDocumentNumber()) == 11)
        {
            $buyer['dniNumber'] = $this->formatCpf($card->getHolderDocumentNumber());
        }
        
        if(strlen($card->getHolderBusinessNumber()) == 14)
        {
            $buyer['cnpj'] = $card->getHolderBusinessNumber();
        }

        $arrayAddress = $this->formatAddress($card->getShippingAddress1() . ',' . $card->getShippingAddress2());
        if(!empty($arrayAddress['street1'])) 
        {
            $buyer['shippingAddress'] = $arrayAddress;
        }
        $buyer['shippingAddress']['city'] = $card->getShippingCity();
        $buyer['shippingAddress']['state'] = $card->getShippingState();
        $buyer['shippingAddress']['country'] = $card->getShippingCountry();
        $buyer['shippingAddress']['postalCode'] = $this->formatPostCode($card->getShippingPostCode());
        $buyer['shippingAddress']['phone'] = $this->formatPhone($card->getShippingPhone());

        return $buyer;
    }

    protected function getCardData()
    {
        $card = $this->getCard();
        $data = array();
        
        $card->validate();

        $data['number'] = $card->getNumber();
        $data['securityCode'] = $card->getCvv();
        $data['expirationDate'] = $card->getExpiryYear() . '/' . sprintf('%02d',$card->getExpiryMonth());
        $data['name'] = $card->getName();

        return $data;
    }

    protected function formatAddress($address)
    {
        $result = array();
        $explode = array_map('trim', explode(',', $address));
        
        if(count($explode) >= 3)
        {
            $result['street1'] = implode(' ', array_slice($explode, 0, -1) );
            $result['street2'] = end($explode);
        }
        else 
        {
            $result['street1'] = $explode[0];
            $result['street2'] = isset($explode[1]) ? $explode[1] : '';
        }

        return $result;
    }

    protected function formatPostCode($postCode)
    {
        return vsprintf("%s%s%s%s%s-%s%s%s", str_split(preg_replace("/[^0-9]/", "", $postCode)));
    }

    // CPF formatter
    protected function formatCpf($number)
    {
        return vsprintf("%s%s%s.%s%s%s.%s%s%s-%s%s", str_split(preg_replace("/[^0-9]/", "", $number)));
    }

    protected function formatPhone($phoneNumber)
    {
        $arrayPhone = array();
        $phone = preg_replace("/[^0-9]/", "", $phoneNumber);
        if(substr( $phone, 0, 1 ) === "0"){
            $arrayPhone['ddd'] = substr($phone, 1, 2);
            $arrayPhone['number'] = substr($phone, 3);
        } elseif (strlen($phone) < 10 ) {
            $arrayPhone['ddd'] = '00';
            $arrayPhone['number'] = $phone;
        } else {
            $arrayPhone['ddd'] = substr($phone, 0, 2);
            $arrayPhone['number'] = substr($phone, 2);
        }
        
        return '(' . $arrayPhone['ddd'] . ')' . (strlen($arrayPhone['number']) >= 8 ? $arrayPhone['number'] : '00000000');
    }
    
    public function getHttpMethod()
    {
        return 'POST';
    }

    public function sendData($data)
    {
        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            null,
            json_encode($this->insertBaseRequestData($data)),
            $this->getOptions()
        );

        $httpRequest->setHeader('Content-Type', 'application/json');
        $httpRequest->setHeader('Accept', 'application/json');

        $httpResponse = $httpRequest->send();

        return $this->response = new Response($this, $httpResponse->json());
    }

    protected function getOptions()
    {
        return array();
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }


}