<?php

namespace Omnipay\PayUBrazil\Message;

use Omnipay\Tests\TestCase;

class ResponseTest extends TestCase
{
 
    public function testPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseSuccess.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->json());

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('508cbdfb-ed85-48df-854a-92fcb649bf62', $response->getTransactionReference());
        $this->assertSame(840624437, $response->getOrderReference());
        $this->assertNull($response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseFailure.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->json());

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertStringStartsWith('O número do cartão de crédito não é válido', $response->getMessage());
    }
    
}

