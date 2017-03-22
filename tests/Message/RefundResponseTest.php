<?php

namespace Omnipay\PayUBrazil\Message;

use Omnipay\Tests\TestCase;

class RefundResponseTest extends TestCase
{
    public function setUp()
    {
        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionReference' => 'ab752190-84db-4dd4-b010-3c72a619363b',
                'orderReference' => 123123
            )
        );
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');
        $response = $this->request->send();

        $this->assertSame('REFUNDED', $response->getRefundState());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        // $this->assertSame('e8421426-8519-4150-9f00-b22737b85719', $response->getTransactionReference());

    }

    public function testSendPending()
    {
        $this->setMockHttpResponse('RefundPending.txt');
        $response = $this->request->send();

        $this->assertSame('PENDING', $response->getRefundState());
        $this->assertTrue($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('RefundFailure.txt');
        $response = $this->request->send();

        $this->assertSame('FAILED', $response->getRefundState());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isRedirect());
        // $this->assertSame('e8421426-8519-4150-9f00-b22737b85719', $response->getTransactionReference());

    }

    // public function testSendError()
    // {
    //     $this->setMockHttpResponse('CaptureFailure.txt');
    //     $response = $this->request->send();

    //     $this->assertFalse($response->isSuccessful());
    //     $this->assertFalse($response->isRedirect());
    //     $this->assertStringStartsWith('Internal payment provider error.', $response->getMessage());
    // }
}