<?php
/**
 * WorldPay XML Response
 */
namespace Omnipay\WorldPayXML\Message;

use DOMDocument;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Class Response
 * @package Omnipay\WorldPayXML\Message
 */
class Response extends AbstractResponse
{
    const SENT_FOR_AUTHORISATION = 'SENT_FOR_AUTHORISATION';

    /**
     * @param RequestInterface $request
     * @param                  $data
     *
     * @return ModifyResponse|RedirectResponse|Response
     * @throws InvalidResponseException
     */
    public static function make(RequestInterface $request, $data)
    {
        if (empty($data)) {
            throw new InvalidResponseException();
        }

        //$data = str_replace(['<![CDATA[', ']]>'], ['', ''], $data);

        $responseDom = new DOMDocument;
        $responseDom->loadXML($data);

        if ($request instanceof \Omnipay\WorldPayXML\Message\ModifyRequest &&
            !($request instanceof \Omnipay\WorldPayXML\Message\IncreaseAuthorisationRequest)
        ) {
            $xmlData = simplexml_import_dom(
                $responseDom->documentElement->firstChild
            );

            return new ModifyResponse(
                $request,
                $xmlData
            );
        } else {
            $xmlData = simplexml_import_dom(
                $responseDom->documentElement->firstChild
            );

            if (!isset($xmlData->error)) {
                $xmlData = simplexml_import_dom(
                    $responseDom->documentElement->firstChild->firstChild
                );
            }
        }

        if ($xmlData->payment->lastEvent == 'SENT_FOR_AUTHORISATION') {

        }

        if ($xmlData->requestInfo) {
            return new RedirectResponse($request, $xmlData);
        } else {
            return new Response($request, $xmlData);
        }
    }

    /**
     * Constructor
     *
     * @param RequestInterface $request Request
     * @param string           $data    Data
     *
     * @access public
     */
    public function __construct(RequestInterface $request, $data)
    {
        if (empty($data)) {
            throw new InvalidResponseException();
        }

        $this->request = $request;

        $this->data = $data;
    }

    /**
     * Get message
     *
     * @access public
     * @return string
     */
    public function getMessage()
    {
        $codes = [
            0 => 'AUTHORISED',
            2 => 'REFERRED',
            3 => 'INVALID ACCEPTOR',
            4 => 'HOLD CARD',
            5 => 'REFUSED',
            8 => 'APPROVE AFTER IDENTIFICATION',
            12 => 'INVALID TRANSACTION',
            13 => 'INVALID AMOUNT',
            14 => 'INVALID ACCOUNT',
            15 => 'INVALID CARD ISSUER',
            17 => 'ANNULATION BY CLIENT',
            19 => 'REPEAT OF LAST TRANSACTION',
            20 => 'ACQUIRER ERROR',
            21 => 'REVERSAL NOT PROCESSED, MISSING AUTHORISATION',
            24 => 'UPDATE OF FILE IMPOSSIBLE',
            25 => 'REFERENCE NUMBER CANNOT BE FOUND',
            26 => 'DUPLICATE REFERENCE NUMBER',
            27 => 'ERROR IN REFERENCE NUMBER FIELD',
            28 => 'ACCESS DENIED',
            29 => 'IMPOSSIBLE REFERENCE NUMBER',
            30 => 'FORMAT ERROR',
            31 => 'UNKNOWN ACQUIRER ACCOUNT CODE',
            33 => 'CARD EXPIRED',
            34 => 'FRAUD SUSPICION',
            38 => 'SECURITY CODE EXPIRED',
            40 => 'REQUESTED FUNCTION NOT SUPPORTED',
            41 => 'LOST CARD',
            43 => 'STOLEN CARD, PICK UP',
            51 => 'LIMIT EXCEEDED',
            55 => 'INVALID SECURITY CODE',
            56 => 'UNKNOWN CARD',
            57 => 'ILLEGAL TRANSACTION',
            58 => 'TRANSACTION NOT PERMITTED',
            62 => 'RESTRICTED CARD',
            63 => 'SECURITY RULES VIOLATED',
            64 => 'AMOUNT HIGHER THAN PREVIOUS TRANSACTION AMOUNT',
            68 => 'TRANSACTION TIMED OUT',
            75 => 'SECURITY CODE INVALID',
            76 => 'CARD BLOCKED',
            80 => 'AMOUNT NO LONGER AVAILABLE, AUTHORISATION EXPIRED',
            85 => 'REJECTED BY CARD ISSUER',
            91 => 'CREDITCARD ISSUER TEMPORARILY NOT REACHABLE',
            92 => 'CREDITCARD TYPE NOT PROCESSED BY ACQUIRER',
            94 => 'DUPLICATE REQUEST ERROR',
            97 => 'SECURITY BREACH',
        ];

        $message = 'PENDING';

        if (isset($this->data->error)) {
            $message = 'ERROR: ' . $this->data->error;
        }

        if (isset($this->data->payment->ISO8583ReturnCode)) {
            $returnCode = $this->data->payment->ISO8583ReturnCode->attributes();

            foreach ($returnCode as $name => $value) {
                if ($name == 'code') {
                    $message = $codes[intval($value)];
                }
            }
        }

        if ($this->isSuccessful()) {
            $message = $codes[0];
        }

        return $message;
    }

    /**
     * Get transaction reference
     *
     * @access public
     * @return string
     */
    public function getTransactionReference()
    {
        if ($this->data instanceof \SimpleXMLElement) {
            $attributes = $this->data->attributes();

            if (isset($attributes['orderCode'])) {
                return (string)$attributes['orderCode'];
            }
        }

        return null;
    }

    /**
     * Get is redirect
     *
     * @access public
     * @return boolean
     */
    public function isRedirect()
    {
        if (isset($this->data->requestInfo->request3DSecure->issuerURL)) {
            return true;
        }

        return false;
    }

    /**
     * Get is successful
     *
     * @access public
     * @return boolean
     */
    public function isSuccessful()
    {
        if (isset($this->data->payment->lastEvent)) {
            if (strtoupper($this->data->payment->lastEvent) == 'AUTHORISED') {
                return true;
            }
        }

        return false;
    }
}
