<?php
/**
 * Backoffice code request.
 */
namespace Omnipay\WorldPayXML\Message;

/**
 * Class BackOfficeCodeRequest
 * @package Omnipay\WorldPayXML\Message
 */
class BackOfficeCodeRequest extends ModifyRequest
{

    /**
     * Set the backoffice code parameter.
     *
     * @param string $value The value to set the backoffice code to
     *
     * @return $this
     */
    public function setBackOfficeCode($value)
    {
        return $this->setParameter('backOfficeCode', $value);
    }

    /**
     * Get the backoffice code
     *
     * @return string
     */
    public function getBackOfficeCode()
    {
        return $this->getParameter('backOfficeCode');
    }

    /**
     * Get the data for the request.
     *
     * @return \SimpleXMLElement
     */
    public function getData()
    {
        $data = $this->getBase();
        $addBackOfficeCode = $data->modify->orderModification->addChild('addBackOfficeCode');
        $addBackOfficeCode->addAttribute('backOfficeCode', $this->getBackOfficeCode());

        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     * @return integer
     */
    public function getTransactionType()
    {
        return static::BACK_OFFICE_CODE_REQUEST;
    }
}
