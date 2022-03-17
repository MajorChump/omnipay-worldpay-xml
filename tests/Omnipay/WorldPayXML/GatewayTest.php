<?php

namespace Omnipay\WorldPayXML;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /**
     * The WorldPayXML gateway
     * @var \Omnipay\WorldPayXML\Gateway
     */
    public $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }

    public function testGetName()
    {
        $this->assertEquals('WorldPayXML', $this->gateway->getName());
    }

    public function testGetShortName()
    {
        $this->assertEquals('WorldPayXML', $this->gateway->getShortName());
    }

    public function testGetDefaultParameters()
    {
        $defaultParameters = $this->gateway->getDefaultParameters();

        $this->assertInternalType('array', $defaultParameters);
        $this->assertArrayHasKey('installation', $defaultParameters);
        $this->assertArrayHasKey('merchant', $defaultParameters);
        $this->assertArrayHasKey('password', $defaultParameters);
        $this->assertArrayHasKey('testMode', $defaultParameters);
    }

    public function testPurchaseSuccess()
    {
        $options = array(
            'amount' => '10.00',
            'card' => new CreditCard(
                array(
                    'firstName' => 'Example',
                    'lastName' => 'User',
                    'number' => '4111111111111111',
                    'expiryMonth' => '12',
                    'expiryYear' => '2016',
                    'cvv' => '123',
                )
            ),
            'transactionId' => 'T0211010',
        );

        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $response = $this->gateway->purchase($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }

    public function testPurchaseError()
    {
        $options = array(
            'amount' => '10.00',
            'card' => new CreditCard(
                array(
                    'firstName' => 'Example',
                    'lastName' => 'User',
                    'number' => '4111111111111111',
                    'expiryMonth' => '12',
                    'expiryYear' => '2016',
                    'cvv' => '123',
                )
            ),
            'transactionId' => 'T0211010',
        );

        $this->setMockHttpResponse('PurchaseFailure.txt');

        $response = $this->gateway->purchase($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('CARD EXPIRED', $response->getMessage());
    }

    public function testAuthorisationSuccess()
    {
        $this->setMockHttpResponse('AuthorisationSuccess.txt');

        $options = [
            'orderCode' => 'T0211010',
            'authorisationCode' => 'abc123',
        ];

        $response = $this->gateway->authorise($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }

    public function testBackOfficeCodeSuccess()
    {
        $this->setMockHttpResponse('BackOfficeCodeSuccess.txt');

        $options = [
            'orderCode' => 'T0211010',
            'backOfficeCode' => 'abc123',
        ];

        $response = $this->gateway->backOfficeCode($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }

    public function testCancelSuccess()
    {
        $this->setMockHttpResponse('CancelSuccess.txt');

        $options = [
            'orderCode' => 'T0211010',
        ];

        $response = $this->gateway->cancel($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }

    public function testCaptureSuccess()
    {
        $this->setMockHttpResponse('CaptureSuccess.txt');

        $options = [
            'orderCode' => 'T0211010',
            'amount' => 12.34,
            'currencyCode' => 'EUR',
            'debitCreditIndicator' => 'credit',
        ];

        $response = $this->gateway->capture($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }

    public function testCaptureFailure()
    {
        $this->setMockHttpResponse('CaptureFailure.txt');

        $options = [
            'orderCode' => 'T0211010',
            'amount' => 12.34,
            'currencyCode' => 'EUR',
            'debitCreditIndicator' => 'credit',
        ];

        $response = $this->gateway->capture($options)->send();

        $this->assertFalse($response->isSuccessful());
    }

    public function testIncreasAuthorisationSuccess()
    {
        $this->setMockHttpResponse('IncreaseAuthorisationSuccess.txt');

        $options = [
            'orderCode' => 'T0211010',
            'amount' => 12.34,
            'currencyCode' => 'EUR',
            'debitCreditIndicator' => 'credit',
        ];

        $response = $this->gateway->increaseAuthorisation($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }

    public function testIncreaseAuthorisationFailure()
    {
        $this->setMockHttpResponse('IncreaseAuthorisationFailure.txt');

        $options = [
            'orderCode' => 'T0211010',
            'amount' => 12.34,
            'currencyCode' => 'EUR',
            'debitCreditIndicator' => 'credit',
        ];

        $response = $this->gateway->increaseAuthorisation($options)->send();

        $this->assertFalse($response->isSuccessful());
    }

    public function testInquirySuccess()
    {
        $this->setMockHttpResponse('InquirySuccess.txt');

        $options = [
            'orderCode' => 'T0211010',
        ];

        $response = $this->gateway->inquiry($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }

    public function testInquiryFailure()
    {
        $this->setMockHttpResponse('InquiryFailure.txt');

        $options = [
            'orderCode' => 'T0211010',
        ];

        $response = $this->gateway->inquiry($options)->send();

        $this->assertFalse($response->isSuccessful());
    }

    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $options = [
            'orderCode' => 'T0211010',
            'amount' => 12.34,
            'amountCurrencyCode' => 'GBP',
            'debitOrCredit' => 'credit',
        ];

        $response = $this->gateway->void($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }

    public function testRefundFailure()
    {
        $this->setMockHttpResponse('RefundFailure.txt');

        $options = [
            'orderCode' => 'T0211010',
            'amount' => 12.34,
            'amountCurrencyCode' => 'GBP',
            'debitOrCredit' => 'xxxxx',
        ];

        $response = $this->gateway->void($options)->send();

        $this->assertFalse($response->isSuccessful());
    }

    public function testVoidSuccess()
    {
        $this->setMockHttpResponse('VoidSuccess.txt');

        $options = [
            'orderCode' => 'T0211010',
        ];

        $response = $this->gateway->void($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('T0211010', $response->getTransactionReference());
    }
}
