<?php

namespace Omnipay\PayUBrazil\Message;

use Omnipay\Tests\TestCase;

class FetchTransactionRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new FetchTransactionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionId' => 111111
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        
        $this->assertSame(111111, $data['details']['referenceCode']);
    }

    public function testTestEndpoint()
    {
        $this->request->setTestMode(true);
        $this->assertSame('https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi', $this->request->getEndpoint());
    }
    public function testProductionEndpoint()
    {
        $this->request->setTestMode(false);
        $this->assertSame('https://api.payulatam.com/reports-api/4.0/service.cgi', $this->request->getEndpoint());
    }

}