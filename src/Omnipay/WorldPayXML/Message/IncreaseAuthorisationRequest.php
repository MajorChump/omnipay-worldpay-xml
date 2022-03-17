<?php
/**
 * Increase authorisation request
 */
namespace Omnipay\WorldPayXML\Message;

/**
 * Class IncreaseAuthorisationRequest
 * @package Omnipay\WorldPayXML\Message
 */
class IncreaseAuthorisationRequest extends ModifyRequest
{
    /**
     * Get the data for the request.
     *
     * @return \SimpleXMLElement
     */
    public function getData()
    {
        $data = $this->getBase();
        $authorise = $data->modify->orderModification->addChild('increaseAuthorisation');
        $amount = $authorise->addChild('amount');
        $amount->addAttribute('value', $this->getAmountInteger());
        $amount->addAttribute('currencyCode', $this->getCurrency());
        $amount->addAttribute('exponent', $this->getCurrencyDecimalPlaces());

        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     * @return integer
     */
    public function getTransactionType()
    {
        return static::INCREASE_AUTHORISATION_REQUEST;
    }
}
