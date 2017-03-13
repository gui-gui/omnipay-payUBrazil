<?php

namespace Omnipay\PayUBrazil\Message;

class AuthorizeRequest extends AbstractRequest
{

    public function getInstallments()
    {
        return $this->getParameter('installments');
    }

    public function setInstallments($value)
    {
        return $this->setParameter('installments', (int)$value);
    }

    public function getData()
    {
        $this->validate('amount', 'currency');
        
        $data = array();
        $data['transaction']['type'] = 'AUTHORIZATION';
        $data['transaction']['paymentMethod'] = $this->getPaymentMethod();
        $data['transaction']['paymentCountry'] = $this->getCard()->getCountry();
        $data['transaction']['ipAddress'] = $this->getClientIp();
        $data['transaction']['order']['accountId'] = $this->getAccountId();
        $data['transaction']['order']['referenceCode'] = $this->getTransactionId();
        $data['transaction']['order']['description'] = $this->getDescription();
        $data['transaction']['order']['language'] = 'pt';
        $data['transaction']['order']['signature'] = $this->getSignature();
        $data['transaction']['order']['notifyUrl'] = $this->getNotifyUrl();
        $data['transaction']['order']['additionalValues']['TX_VALUE']['value'] = $this->getAmount();
        $data['transaction']['order']['additionalValues']['TX_VALUE']['currency'] = $this->getCurrency();
        $data['transaction']['order']['buyer'] = $this->getBuyerData();
        // $data['transaction']['order']['payer'] = $this->getPayerData();
        // TODO: 
        // if token is defined
        // $data['transaction']['creditCardTokenId'] = $this->getToken();
        // else
        $data['transaction']['creditCard'] = $this->getCardData();
        // endif
        $data['transaction']['extraParameters']['INSTALLMENTS_NUMBER'] = $this->getInstallments();

        return $data;

    }

}