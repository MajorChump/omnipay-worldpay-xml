<?php

namespace Omnipay\WorldPayXML\Message;

use Omnipay\Tests\TestCase;
use Mockery;

class ResponseTest extends TestCase
{
    /**
     * @expectedException \Omnipay\Common\Exception\InvalidResponseException
     */
    public function testConstructEmpty()
    {
        $response = Response::make($this->getMockRequest(), '');
    }

    public function testAuthorisationSuccess()
    {
        $authorisationRequest = Mockery::mock('Omnipay\WorldPayXML\Message\AuthorisationRequest');
        $httpResponse = $this->getMockHttpResponse('AuthorisationSuccess.txt');
        $response = Response::make(
            $authorisationRequest,
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('SUCCESS', $response->getMessage());
    }

    public function testBackOfficeCodeSuccess()
    {
        $backOfficeCodeRequest = Mockery::mock('Omnipay\WorldPayXML\Message\BackOfficeCodeRequest');
        $httpResponse = $this->getMockHttpResponse('BackOfficeCodeSuccess.txt');
        $response = Response::make(
            $backOfficeCodeRequest,
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('SUCCESS', $response->getMessage());
    }

    public function testCancelSuccess()
    {
        $cancelRequest = Mockery::mock('Omnipay\WorldPayXML\Message\CancelRequest');
        $httpResponse = $this->getMockHttpResponse('CancelSuccess.txt');
        $response = Response::make(
            $cancelRequest,
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('SUCCESS', $response->getMessage());
    }

    public function testCaptureSuccess()
    {
        $captureRequest = Mockery::mock('Omnipay\WorldPayXML\Message\CaptureRequest');
        $httpResponse = $this->getMockHttpResponse('CaptureSuccess.txt');
        $response = Response::make(
            $captureRequest,
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('SUCCESS', $response->getMessage());
    }

    public function testCaptureFailure()
    {
        $captureRequest = Mockery::mock('Omnipay\WorldPayXML\Message\CaptureRequest');
        $httpResponse = $this->getMockHttpResponse('CaptureFailure.txt');
        $response = Response::make(
            $captureRequest,
            $httpResponse->getBody()
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('Attribute "debitCreditIndicator" with value "xxxx" must have a value from the list "debit credit ".', $response->getMessage());
    }

    public function testInquirySuccess()
    {
        $inquiryRequest = Mockery::mock('Omnipay\WorldPayXML\Message\InquiryRequest');
        $httpResponse = $this->getMockHttpResponse('InquirySuccess.txt');
        $response = Response::make(
            $inquiryRequest,
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('AUTHORISED', $response->getMessage());
    }

    public function testInquiryFailure()
    {
        $inquiryRequest = Mockery::mock('Omnipay\WorldPayXML\Message\InquiryRequest');
        $httpResponse = $this->getMockHttpResponse('InquiryFailure.txt');
        $response = Response::make(
            $inquiryRequest,
            $httpResponse->getBody()
        );

        $this->assertFalse($response->isSuccessful());
    }

    public function testRefundSuccess()
    {
        $refundRequest = Mockery::mock('Omnipay\WorldPayXML\Message\RefundRequest');
        $httpResponse = $this->getMockHttpResponse('RefundSuccess.txt');
        $response = Response::make(
            $refundRequest,
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('SUCCESS', $response->getMessage());
    }

    public function testRefundFailure()
    {
        $refundRequest = Mockery::mock('Omnipay\WorldPayXML\Message\RefundRequest');
        $httpResponse = $this->getMockHttpResponse('RefundFailure.txt');
        $response = Response::make(
            $refundRequest,
            $httpResponse->getBody()
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('Attribute "debitCreditIndicator" with value "xxxx" must have a value from the list "debit credit ".', $response->getMessage());
    }

    public function testVoidSuccess()
    {
        $voidRequest = Mockery::mock('Omnipay\WorldPayXML\Message\VoidRequest');
        $httpResponse = $this->getMockHttpResponse('RefundSuccess.txt');
        $response = Response::make(
            $voidRequest,
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('SUCCESS', $response->getMessage());
    }

    public function testIncreaseAuthorisationSuccess()
    {
        $increaseAuthorisationRequest = Mockery::mock('Omnipay\WorldPayXML\Message\IncreaseAuthorisationRequest');
        $httpResponse = $this->getMockHttpResponse('IncreaseAuthorisationSuccess.txt');
        $response = Response::make(
            $increaseAuthorisationRequest,
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('AUTHORISED', $response->getMessage());
    }

    public function testIncreaseAuthorisationFail()
    {
        $increaseAuthorisationRequest = Mockery::mock('Omnipay\WorldPayXML\Message\IncreaseAuthorisationRequest');
        $httpResponse = $this->getMockHttpResponse('IncreaseAuthorisationFailure.txt');
        $response = Response::make(
            $increaseAuthorisationRequest,
            $httpResponse->getBody()
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('ERROR: Could not find order', $response->getMessage());
    }

    public function testPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseSuccess.txt');
        $response = Response::make(
            $this->getMockRequest(),
            $httpResponse->getBody()
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('AUTHORISED', $response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseFailure.txt');
        $response = Response::make(
            $this->getMockRequest(),
            $httpResponse->getBody()
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('T0211234', $response->getTransactionReference());
        $this->assertSame('CARD EXPIRED', $response->getMessage());
    }

    public function testPurchaseReferral()
    {
        $httpResponse = $this->getMockHttpResponse('PurchaseReferral.txt');
        $response = Response::make(
            $this->getMockRequest(),
            $httpResponse->getBody()
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('T0211010', $response->getTransactionReference());
        $this->assertEquals('PENDING', $response->getMessage());
    }
}
