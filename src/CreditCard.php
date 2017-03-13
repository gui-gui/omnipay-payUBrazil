<?php
/**
 * Credit Card class
 */

namespace Omnipay\PayUBrazil;

use Omnipay\Common\CreditCard as Card;


class CreditCard extends Card
{
    /**
     * Get Document number (CPF).
     *
     * @return string
     */
    public function getHolderDocumentNumber()
    {
        return $this->getParameter('holderDocumentNumber');
    }

    public function setHolderDocumentNumber($value)
    {
        return $this->setParameter('holderDocumentNumber', preg_replace("/[^0-9]/", '', $value));
    }    

    /**
     * Get Business number (CNPJ).
     *
     * @return string
    */
    public function getHolderBusinessNumber()
    {
        return $this->getParameter('holderBusinessNumber');
    }

    public function setHolderBusinessNumber($value)
    {
        return $this->setParameter('holderBusinessNumber', preg_replace("/[^0-9]/", '', $value));
    }    
}