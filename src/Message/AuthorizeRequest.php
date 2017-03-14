<?php

namespace Omnipay\PayUBrazil\Message;

class AuthorizeRequest extends AbstractRequest
{

    // Todo : define consts for payment methods to avoid errors
    // VISA
    // AMEX
    // BOLETO_BANCARIO
    // MASTERCARD
    // ELO
    // HIPERCARD
    // DINERS

    public function getInstallments()
    {
        return $this->getParameter('installments');
    }

    public function setInstallments($value)
    {
        return $this->setParameter('installments', (int)$value);
    }

     /**
     * Get the boleto expiration date
     * 
     * @return string boleto expiration date
     */
    public function getBoletoExpirationDate($format = 'Y-m-d\TH:i:s')
    {
        $value = $this->getParameter('boletoExpirationDate');
        
        return $value ? $value->format($format) : null;
    }
    
    /**
     * Set the boleto expiration date
     * 
     * @param string $value defaults to atual date + 7 days
     * @return AuthorizeRequest provides a fluent interface
     */
    public function setBoletoExpirationDate($value)
    {
        if ($value) {
            $value = new \DateTime($value, new \DateTimeZone('UTC'));
            $value = new \DateTime($value->format('Y-m-d\T03:00:00'), new \DateTimeZone('UTC'));
        } else {
            $value = null;
        }
        
        return $this->setParameter('boletoExpirationDate', $value);
    }

    public function getData()
    {

        if(!$this->getSignature())
        {
            $this->setSignature();
        }

        $this->validate(
            'amount',
            'currency',
            'paymentMethod',
            'accountId',
            'merchantId',
            'signature',
            'transactionId',
            'description'
            );
        
        $data = array();
        $data['transaction']['type'] = 'AUTHORIZATION';
        $data['transaction']['paymentMethod'] = $this->getPaymentMethod();
        $data['transaction']['paymentCountry'] = 'BR';
        $data['transaction']['ipAddress'] = $this->getClientIp();
        $data['transaction']['order']['accountId'] = $this->getAccountId();
        $data['transaction']['order']['referenceCode'] = $this->getTransactionId();
        $data['transaction']['order']['description'] = $this->getDescription();
        $data['transaction']['order']['language'] = 'pt';
        $data['transaction']['order']['signature'] = $this->getSignature();
        if($this->getNotifyUrl())
        {
        $data['transaction']['order']['notifyUrl'] = $this->getNotifyUrl();
        }
        $data['transaction']['order']['additionalValues']['TX_VALUE']['value'] = $this->getAmount();
        $data['transaction']['order']['additionalValues']['TX_VALUE']['currency'] = $this->getCurrency();
        $data['transaction']['order']['buyer'] = $this->getBuyerData();
        // $data['transaction']['order']['payer'] = $this->getPayerData();
        if($this->getPaymentMethod() == 'BOLETO' || $this->getPaymentMethod() == 'BOLETO_BANCARIO')
        {
            if ( $this->getBoletoExpirationDate() ) {
                $data['transaction']['expirationDate'] = $this->getBoletoExpirationDate();
            }
            $data['transaction']['paymentMethod'] = 'BOLETO_BANCARIO';
        }
        else
        {
            $data['transaction']['extraParameters']['INSTALLMENTS_NUMBER'] = $this->getInstallments() ?: 1;
            
            if($this->getToken())
            {
                $data['transaction']['creditCardTokenId'] = $this->getToken();
            }
            else
            {
                $data['transaction']['creditCard'] = $this->getCardData();
            }
        }

        return $data;

    }

}