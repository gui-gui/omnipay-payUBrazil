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

    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');
        $response = $this->request->send();

        $this->assertSame('SUCCESS', $response->getRefundState());
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getMessage());
        $this->assertSame('CANCELLED', $response->getCode());
        $this->assertSame('e8421426-8519-4150-9f00-b22737b85719', $response->getTransactionReference());

    }

    public function testRefundPending()
    {
        $this->setMockHttpResponse('RefundPending.txt');
        $response = $this->request->send();

        $this->assertSame('PENDING', $response->getRefundState());
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isPending());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getMessage());
        $this->assertSame('PENDING', $response->getCode());
        $this->assertNull($response->getTransactionReference());
    }

    public function testRefundNonResolved()
    {
        $this->setMockHttpResponse('RefundNonResolved.txt');
        $response = $this->request->send();

        $this->assertSame('NONRESOLVED', $response->getRefundState());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getMessage());
        $this->assertSame('CAPTURED', $response->getCode());
        $this->assertSame('8366e912-11ac-41cd-8413-a4955ab44713', $response->getTransactionReference());
    }

    public function testRefundDeclined()
    {
        $this->setMockHttpResponse('RefundDeclined.txt');
        $response = $this->request->send();

        $this->assertSame('FAILED', $response->getRefundState());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getMessage());
        $this->assertSame('CAPTURED', $response->getCode());
        $this->assertSame('e8421426-8519-4150-9f00-b22737b85720', $response->getTransactionReference());

    }

    public function testRefundError()
    {
        $this->setMockHttpResponse('RefundFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertFalse($response->isRedirect());
        $this->assertStringStartsWith('Invalid request', $response->getMessage());
        $this->assertSame('ERROR', $response->getCode());
        $this->assertNull($response->getTransactionReference());

    }
}