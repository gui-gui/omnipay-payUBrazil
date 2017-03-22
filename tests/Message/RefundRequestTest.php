<?php

namespace Omnipay\PayUBrazil\Message;

use Mockery;
use Omnipay\Tests\TestCase;

class RefundRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\PayUBrazil\Message\RefundRequest')->makePartial();
        $this->request->initialize(
            array(
                'transactionReference' => 111111,
                'orderReference' => 123123
            )
        );
    }

    public function testCreateResponse()
    {        
        $response = $this->request->createResponse(array());
        $this->assertInstanceOf('Omnipay\PayUBrazil\Message\RefundResponse', $response);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame(123123, $data['transaction']['order']['id'] );
        $this->assertSame(111111, $data['transaction']['parentTransactionId'] );
        $this->assertSame('Omnipay Refund', $data['transaction']['reason'] );
        $this->assertSame('REFUND', $data['transaction']['type'] );

    }

}