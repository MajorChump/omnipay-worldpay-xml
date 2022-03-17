<?php
/**
 * Authorisation code request
 */

namespace Omnipay\WorldPayXML\Message;

/**
 * Class AuthorisationRequest
 * @package Omnipay\WorldPayXML\Message
 */
class AuthorisationRequest extends ModifyRequest
{

    /**
     * Set the authorisation code parameter.
     *
     * @param string $value Value to set the parameter to
     *
     * @return $this
     */
    public function setAuthorisation($value)
    {
        return $this->setParameter('authorisation', $value);
    }

    /**
     * Get the authorisation code.
     *
     * @return string
     */
    public function getAuthorisation()
    {
        return $this->getParameter('authorisation');
    }

    /**
     * Get the data for the request.
     *
     * @return \SimpleXMLElement
     */
    public function getData()
    {
        $data = $this->getBase();
        $authorise = $data->modify->orderModification->addChild('authorise');
        $authorise->addAttribute('authorisationCode', $this->getAuthorisation());

        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     * @return integer
     */
    public function getTransactionType()
    {
        return static::AUTHORISATION_CODE_REQUEST;
    }
}
