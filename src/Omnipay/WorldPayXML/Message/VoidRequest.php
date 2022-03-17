<?php
/**
 * Refund or cancel depending on current status.
 */

namespace Omnipay\WorldPayXML\Message;

/**
 * Omnipay WorldPay XML Refund or Cancel Request
 */
class VoidRequest extends ModifyRequest
{

    /**
     * Retrieve the data for the request.
     *
     * @return \SimpleXMLElement
     */
    public function getData()
    {
        $data = $this->getBase();
        $data->modify->orderModification->addChild('cancelOrRefund');

        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     * @return integer
     */
    public function getTransactionType()
    {
        return static::VOID_REQUEST;
    }
}
