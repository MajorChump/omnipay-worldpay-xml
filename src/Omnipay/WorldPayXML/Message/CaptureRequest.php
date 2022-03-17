<?php
/**
 * Capture request
 */

namespace Omnipay\WorldPayXML\Message;

/**
 * Class CaptureRequest
 * @package Omnipay\WorldPayXML\Message
 */
class CaptureRequest extends ModifyRequest
{
    /**
     * Get the data for the request.
     *
     * @return \SimpleXMLElement
     */
    public function getData()
    {
        $data = $this->getBase();
        $capture = $data->modify->orderModification->addChild('capture');
        $amount = $capture->addChild('amount');
        $amount->addAttribute('value', $this->getAmountInteger());
        $amount->addAttribute('currencyCode', $this->getCurrency());
        $amount->addAttribute('exponent', $this->getCurrencyDecimalPlaces());
        $amount->addAttribute('debitCreditIndicator', $this->getDebitCreditIndicator());

        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     * @return integer
     */
    public function getTransactionType()
    {
        return static::CAPTURE_REQUEST;
    }
}