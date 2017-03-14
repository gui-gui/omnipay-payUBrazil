<?php

namespace Omnipay\PayUBrazil;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{

    public function getValidCard()
    {
        return array(
            'email' => 'email@email.com',
            'firstName' => 'John F',
            'lastName' => 'Doe',
            'number' => '4242424242424242',
            'expiryMonth' => rand(1, 12),
            'expiryYear' => gmdate('Y') + rand(1, 5),
            'cvv' => rand(100, 999),
            'billingAddress1' => 'Rua de Cobrança 70',
            'billingAddress2' => 'Apt 101',
            'billingCity' => 'Rio de Janeiro',
            'billingPostcode' => '12345678',
            'billingState' => 'RJ',
            'billingCountry' => 'BR',
            'billingPhone' => '(021)4444-4567',
            'shippingAddress1' => 'Rua de Entrega 170',
            'shippingAddress2' => 'Apt 102',
            'shippingCity' => 'Rio de Janeiro',
            'shippingPostcode' => '12345678',
            'shippingState' => 'RJ',
            'shippingCountry' => 'BR',
            'shippingPhone' => '(011) 99999-9999',
            'holderBusinessNumber' => '12.345.678/0001-11',
            'holderDocumentNumber' => '123.456.789-11',
        );
    }

    public function getValidRequest()
    {
        return  array(
                'amount' => '10.00',
                'card' => $this->getValidCard(),
                'paymentMethod' => 'VISA',
                'notifyUrl' => 'http://requestb.in/13hshws1',
                'installments' => 1,
                'description' => 'Order 1',
                'currency' => 'BRL',
                'language' => 'pt',
                'apiKey' => '4Vj8eK4rloUd272L48hsrarnUA',
                'apiLogin' => 'pRRXKOl8ikMmt9u',
                'transactionId' => '123456',
                'accountId' => '512327',
                'merchantId' => '508029',
                'clientIp' => '127.0.0.1',
                'testMode' => true,
                'transactionReference' => 'transactionReference123',
                'orderReference' => 'orderReference123',
                'reason' => 'refund reason'
                );
    }
	
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize($this->getValidRequest());
        
        $this->assertInstanceOf('Omnipay\PayUBrazil\Message\AuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture($this->getValidRequest());
        
        $this->assertInstanceOf('Omnipay\PayUBrazil\Message\CaptureRequest', $request);
        $this->assertSame('CAPTURE', $request->getData()['transaction']['type']);
        $this->assertSame('orderReference123', $request->getData()['transaction']['order']['id']);
        $this->assertSame('transactionReference123', $request->getData()['transaction']['parentTransactionId']);
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase($this->getValidRequest());
        
        $this->assertInstanceOf('Omnipay\PayUBrazil\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
        $this->assertSame('AUTHORIZATION_AND_CAPTURE', $request->getData()['transaction']['type']);
    }

    public function testRefund()
    {
        $request = $this->gateway->refund($this->getValidRequest());

        $this->assertInstanceOf('Omnipay\PayUBrazil\Message\RefundRequest', $request);
        $this->assertSame('REFUND', $request->getData()['transaction']['type']);
        $this->assertSame('orderReference123', $request->getData()['transaction']['order']['id']);
        $this->assertSame('refund reason', $request->getData()['transaction']['reason']);
    }
     
}