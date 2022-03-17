<?php
/**
 * Abstract class which is extended by all modification request classes.
 */

namespace Omnipay\WorldPayXML\Message;

/**
 * Class ModifyRequest
 * @package Omnipay\WorldPayXML\Message
 */
abstract class ModifyRequest extends AbstractRequest
{

    /**
     * Set the debit/credit parameter.
     *
     * @param string $value Either 'debit' or 'credit'.
     *
     * @return $this
     */
    public function setDebitCreditIndicator($value)
    {
        return $this->setParameter('debitCreditIndicator', $value);
    }

    /**
     * Get the debit/credit parameter.
     *
     * @return string
     */
    public function getDebitCreditIndicator()
    {
        return $this->getParameter('debitCreditIndicator');
    }

    /**
     * Extends the base XML from the Abstract request class method to include common elements for all modififcation
     * requests.
     *
     * @return \SimpleXMLElement
     */
    protected function getBase()
    {
        $data = parent::getBase();
        $modify = $data->addChild('modify');
        $orderModification = $modify->addChild('orderModification');
        $orderModification->addAttribute('orderCode', $this->getTransactionId());

        return $data;
    }
}
