<?php

namespace Omnipay\PayUBrazil\Message;

use Mockery;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\PayUBrazil\Message\AbstractRequest')->makePartial();
        $this->request->initialize();
    }
    
    public function testGetEndpoint()
    {
        $this->request->setTestMode(true);
        $this->assertSame('https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi', $this->request->getEndpoint());
        $this->request->setTestMode(false);
        $this->assertSame('https://api.payulatam.com/payments-api/4.0/service.cgi', $this->request->getEndpoint());
    }
    

    public function testInsertBaseRequestData()
    {
        $data = array();
        $data['mergeTest'] = true;
       
        $this->request->setApiKey('4Vj8eK4rloUd272L48hsrarnUA');
        $this->request->setApiLogin('pRRXKOl8ikMmt9u');     
        $this->request->setTestMode(true);
        
        $data = $this->request->insertBaseRequestData($data);

        $this->assertArrayHasKey('mergeTest', $data);
        $this->assertTrue($data['mergeTest']);
        $this->assertSame('4Vj8eK4rloUd272L48hsrarnUA', $data['merchant']['apiKey']);
        $this->assertSame('pRRXKOl8ikMmt9u', $data['merchant']['apiLogin']);
        $this->assertSame('pt', $data['language']);
        $this->assertSame('SUBMIT_TRANSACTION', $data['command']);
        $this->assertTrue($data['test']);
    }

    public function testGetBaseRequestData()
    {
        $this->request->setTestMode(true);
        $data = $this->request->getBaseRequestData();
        
        $this->assertTrue($data['test']);
        $this->assertSame('SUBMIT_TRANSACTION', $data['command']);
        $this->assertSame('pt', $data['language']);
    }

    public function testGetSignature()
    {
        $this->request->setApiKey('4Vj8eK4rloUd272L48hsrarnUA');
        $this->request->setMerchantId('508029');
        $this->request->setTransactionId('TestPayU');
        $this->request->setAmount('3.00');
        $this->request->setCurrency('BRL');

        $this->request->setSignature();
        $result = $this->request->getSignature();
        
        $this->assertSame('eb2d8bb2ded48adf59dd26f8b9cca25a', $result);
    }


    public function testFormatPhone()
    {
        $result1 = $this->request->formatPhone('019992989946');
        $result2 = $this->request->formatPhone('01632522869');
        $result3 = $this->request->formatPhone('9 9198-9956');
        $result4 = $this->request->formatPhone('3261-2749');
        $result5 = $this->request->formatPhone(15991989953);
        $result6 = $this->request->formatPhone('(021) 93261-2749');
        
        $this->assertsame('(19)992989946', $result1);
        $this->assertSame('(16)32522869', $result2);
        $this->assertsame('(00)991989956', $result3);
        $this->assertsame('(00)32612749', $result4);
        $this->assertsame('(15)991989953', $result5);    
        $this->assertsame('(21)932612749', $result6);  
    } 

    public function testInvalidFormatPhone()
    {
        $result1 = $this->request->formatPhone('');
        $result2 = $this->request->formatPhone('869');
        $result3 = $this->request->formatPhone(null);
        
        $this->assertsame('(00)00000000', $result1);
        $this->assertSame('(00)00000000', $result2);
        $this->assertsame('(00)00000000', $result3);
    } 

    public function testFormatAddress()
    {
        $result1 = $this->request->formatAddress(' Rua da Quitanda, 12 , sala 101 ');
        $result2 = $this->request->formatAddress(' Rua da Quitanda 12 , sala 101 ');
        $result3 = $this->request->formatAddress(' Rua da Quitanda 12 sala 101 ');
        $result4 = $this->request->formatAddress('Rua Alfonso F, 25, Torre A, Alphaville ');
        
        $this->assertSame('Rua da Quitanda 12', $result1['street1']);
        $this->assertSame('sala 101', $result1['street2']);

        $this->assertSame('Rua da Quitanda 12', $result2['street1']);
        $this->assertSame('sala 101', $result2['street2']);

        $this->assertSame('Rua da Quitanda 12 sala 101', $result3['street1']);
        $this->assertSame('', $result3['street2']);

        $this->assertSame('Rua Alfonso F 25 Torre A', $result4['street1']);
        $this->assertSame('Alphaville', $result4['street2']);
    }

    public function testGetBuyerData()
    {
        $card = $this->getValidCard();
        $card['postCode'] = '12345678';

        $this->request->setCard($card);
        $data = $this->request->getBuyerData();

        $phone = $this->request->formatPhone($this->request->getCard()->getShippingPhone());
        $address = $this->request->formatAddress($this->request->getCard()->getShippingAddress1());

        $this->assertSame($this->request->getCard()->getShippingName(), $data['fullName']);
        $this->assertSame($this->request->getCard()->getEmail(), $data['emailAddress']);
        $this->assertSame($phone, $data['contactPhone']);
        $this->assertSame($this->request->getCard()->getShippingCity(), $data['shippingAddress']['city']);
        $this->assertSame($this->request->getCard()->getShippingState(), $data['shippingAddress']['state']);
        $this->assertSame($this->request->getCard()->getShippingCountry(), $data['shippingAddress']['country']);
        $this->assertSame('12345-678', $data['shippingAddress']['postalCode']);
        $this->assertSame($phone, $data['shippingAddress']['phone']);
    }
    

    // public function testGetPayerData()
    // {
    //     $card = $this->getValidCard();
    //     $card['postCode'] = '12345678';

    //     $this->request->setCard($card);
    //     $data = $this->request->getPayerData();

    //     $phone = $this->request->formatPhone($this->request->getCard()->getBillingPhone());
    //     $address = $this->request->formatAddress($this->request->getCard()->getBillingAddress1());

    //     $this->assertSame($this->request->getCard()->getName(), $data['fullName']);
    //     $this->assertSame($this->request->getCard()->getEmail(), $data['emailAddress']);
    //     $this->assertSame($phone, $data['contactPhone']);
    //     $this->assertSame($this->request->getCard()->getBillingCity(), $data['billingAddress']['city']);
    //     $this->assertSame($this->request->getCard()->getBillingState(), $data['billingAddress']['state']);
    //     $this->assertSame($this->request->getCard()->getBillingCountry(), $data['billingAddress']['country']);
    //     $this->assertSame('12345-678', $data['billingAddress']['postalCode']);
    //     $this->assertSame($phone, $data['billingAddress']['phone']);
    // }

        
    public function testCardData()
    {
        $card = $this->getValidCard();
        $this->request->setCard($card);
        $data = $this->request->getCardData();

        $this->assertSame($card['number'], $data['number']);
        $this->assertSame($this->request->getCard()->getName(), $data['name']);
        $this->assertSame($card['expiryYear'] . '/' . sprintf('%02d',$card['expiryMonth']), $data['expirationDate']);
        $this->assertSame($card['cvv'], $data['securityCode']);
    }

    public function testCreateResponse()
    {
        $response = $this->request->createResponse(array());
        $this->assertInstanceOf('Omnipay\PayUBrazil\Message\Response', $response);
    }

}