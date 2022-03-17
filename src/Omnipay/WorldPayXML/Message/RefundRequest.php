<?php
/**
 * Refund request.
 */

namespace Omnipay\WorldPayXML\Message;

/**
 * Omnipay WorldPay XML Refund Request
 */
class RefundRequest extends ModifyRequest
{

    public function getData()
    {
        $data = $this->getBase();
        $refund = $data->modify->orderModification->addChild('refund');
        $amount = $refund->addChild('amount');
        $amount->addAttribute('value', $this->getAmountInteger());
        $amount->addAttribute('currencyCode', $this->getCurrency());
        $amount->addAttribute('exponent', $this->getCurrencyDecimalPlaces());
        $amount->addAttribute('debitCreditIndicator', $this->getDebitCreditIndicator());

        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     *
     * @return integer
     */
    public function getTransactionType()
    {
        return static::REFUND_REQUEST;
    }
}
