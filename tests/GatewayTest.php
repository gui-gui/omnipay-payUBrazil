<?php

namespace Omnipay\PayUBrazil;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
	
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }


    public function testAuthorize()
    {
        $request = $this->gateway->authorize(array('amount' => '10.00'));
        
        $this->assertInstanceOf('Omnipay\PayUBrazil\Message\AuthorizeRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }
        
}