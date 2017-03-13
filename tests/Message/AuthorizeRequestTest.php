<?php

namespace Omnipay\PayUBrazil\Message;

use Mockery;
use Omnipay\PayUBrazil\CreditCard;
use Omnipay\Tests\TestCase;
use DateTime;
use DateInterval;


class AuthorizeRequestTest extends TestCase
{

    public function getValidCard()
    {
        return array(
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
            'billingDocumentNumber' => '12.345.678/0001-11',
            'shippingDocumentNumber' => '123.456.789-11',
        );
    }

    public function setUp()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        
        $this->request->initialize(
            array(
                'amount' => '12.00',
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
                'testMode' => true
                )
            );    

    }

    public function testGetData()
    {   
        $card = array(
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
            'billingDocumentNumber' => '12.345.678/0001-11',
            'shippingDocumentNumber' => '123.456.789-11',
        );

        $this->request->setSignature();
        $this->request->setCard($card);

        $data = $this->request->getData();

        $this->assertSame('512327', $data['transaction']['order']['accountId']);  
        $this->assertSame('123456', $data['transaction']['order']['referenceCode']);  
        $this->assertSame('pt', $data['transaction']['order']['language']);  
        $this->assertSame($this->request->getSignature(), $data['transaction']['order']['signature']);  
        $this->assertStringStartsWith('http://requestb.in/', $data['transaction']['order']['notifyUrl']);

        $this->assertSame('12.00', $data['transaction']['order']['additionalValues']['TX_VALUE']['value']);
        $this->assertSame('BRL', $data['transaction']['order']['additionalValues']['TX_VALUE']['currency']);
       
        $this->assertSame('AUTHORIZATION', $data['transaction']['type']);
        $this->assertSame('BR', $data['transaction']['paymentCountry']);
        $this->assertSame('VISA', $data['transaction']['paymentMethod']);
        $this->assertSame(1, $data['transaction']['extraParameters']['INSTALLMENTS_NUMBER']);
        
        $this->assertSame('email@email.com', $data['transaction']['order']['buyer']['emailAddress']);
        $this->assertSame('Rua de Entrega 170', $data['transaction']['order']['buyer']['shippingAddress']['street1']);
        $this->assertSame('Apt 102', $data['transaction']['order']['buyer']['shippingAddress']['street2']);        
        $this->assertSame('Rio de Janeiro', $data['transaction']['order']['buyer']['shippingAddress']['city']);        
        $this->assertSame('RJ', $data['transaction']['order']['buyer']['shippingAddress']['state']);        
        $this->assertSame('BR', $data['transaction']['order']['buyer']['shippingAddress']['country']);        
        $this->assertSame('12345-678', $data['transaction']['order']['buyer']['shippingAddress']['postalCode']);        
        $this->assertSame('(11)999999999', $data['transaction']['order']['buyer']['shippingAddress']['phone']);        
        $this->assertSame('123.456.789-11', $data['transaction']['order']['buyer']['dniNumber']);        
        
        // $this->assertSame('Rua de Cobrança 70', $data['transaction']['order']['payer']['billingAddress']['street1']);
        // $this->assertSame('Apt 101', $data['transaction']['order']['payer']['billingAddress']['street2']);
        // $this->assertSame('Rio de Janeiro', $data['transaction']['order']['payer']['billingAddress']['city']);        
        // $this->assertSame('RJ', $data['transaction']['order']['payer']['billingAddress']['state']);        
        // $this->assertSame('BR', $data['transaction']['order']['payer']['billingAddress']['country']);        
        // $this->assertSame('12345-678', $data['transaction']['order']['payer']['billingAddress']['postalCode']);        
        // $this->assertSame('(21)44444567', $data['transaction']['order']['payer']['billingAddress']['phone']);  
        // $this->assertSame('12345678000111', $data['transaction']['order']['payer']['cnpj']);        

        $this->assertSame('4242424242424242', $data['transaction']['creditCard']['number']);  
        $this->assertSame($card['cvv'], $data['transaction']['creditCard']['securityCode']);  
        $this->assertSame($card['expiryYear'] . '/' . sprintf('%02d',$card['expiryMonth']), $data['transaction']['creditCard']['expirationDate']);  
        $this->assertSame('John F Doe', $data['transaction']['creditCard']['name']);  

    }

    public function testGetDataUsingCardToken()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '12.00',
                'paymentMethod' => 'VISA',
                'card' => $this->getValidCard(),
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
                'token' => 'TOKEN123',
            )
        );
        $data = $this->request->getData();

        $this->assertSame('TOKEN123', $data['transaction']['creditCardTokenId']);
        $this->assertArrayNotHasKey('creditCard', $data['transaction']);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('AuthorizeSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(840624432, $response->getOrderReference());
        $this->assertSame('APPROVED', $response->getCode());
        $this->assertSame('8d184190-460f-4b06-a33a-20f2ce5e81e3', $response->getTransactionReference());
        $this->assertNull($response->getMessage());

    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('AuthorizeFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isRedirect());
        $this->assertSame(840624431, $response->getOrderReference());
        $this->assertSame('DECLINED_TEST_MODE_NOT_ALLOWED', $response->getCode());
        $this->assertSame('24cd47be-4d0d-4fd1-b733-a54778ede89d', $response->getTransactionReference());
        $this->assertNull($response->getMessage());

    }

    public function testSendError()
    {
        $this->setMockHttpResponse('AuthorizeError.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getData()['transactionResponse']);
        $this->assertSame('Credenciais inválidas', $response->getMessage());
    }

}