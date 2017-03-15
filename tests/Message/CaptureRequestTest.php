<?php

namespace Omnipay\PayUBrazil\Message;

use Omnipay\Tests\TestCase;

class CaptureRequestTest extends TestCase
{

    public function setUp()
    {
        $this->request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setTransactionReference(111111);
        $this->request->setOrderReference(123123);
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CaptureSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('66c7bff2-c423-42ed-800a-8be11531e7a1', $response->getTransactionReference());
        $this->assertSame(272601, $response->getOrderReference());
        $this->assertNull($response->getMessage());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('CaptureFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertStringStartsWith('Internal payment provider error.', $response->getMessage());
    }

}