<?php
/**
 * Credit Card class
 */

namespace Omnipay\PayUBrazil;

use Omnipay\Common\CreditCard as Card;


class CreditCard extends Card
{
    /**
     * Get Document number (CPF or CNPJ).
     *
     * @return string
     */
    public function getShippingDocumentNumber()
    {
        return $this->getParameter('shippingDocumentNumber');
    }

    public function setShippingDocumentNumber($value)
    {
        // strip non-numeric characters
        return $this->setParameter('shippingDocumentNumber', preg_replace("/[^0-9]/", '', $value));
    }    
    
    public function getBillingDocumentNumber()
    {
        return $this->getParameter('billingDocumentNumber');
    }

    public function setBillingDocumentNumber($value)
    {
        // strip non-numeric characters
        return $this->setParameter('billingDocumentNumber', preg_replace("/[^0-9]/", '', $value));
    }
}