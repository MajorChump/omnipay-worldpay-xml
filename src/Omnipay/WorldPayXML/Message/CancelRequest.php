<?php
/**
 * Backoffice code request.
 */

namespace Omnipay\WorldPayXML\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * Class CancelRequest
 * @package Omnipay\WorldPayXML\Message
 */
class CancelRequest extends ModifyRequest implements RequestInterface
{
    /**
     * Get the data for the request.
     *
     * @return \SimpleXMLElement
     */
    public function getData()
    {
        $data = $this->getBase();
        $data->modify->orderModification->addChild('cancel');
        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     *
     * @return integer
     */
    public function getTransactionType()
    {
        return static::CANCEL_REQUEST;
    }
}
