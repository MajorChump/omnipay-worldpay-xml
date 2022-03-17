<?php
/**
 * Response for a modification request, with the exception of an Increase Authorisation request.
 */

namespace Omnipay\WorldPayXML\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * Class ModifyResponse
 * @package Omnipay\WorldPayXML\Message
 */
class ModifyResponse extends Response
{
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
        $this->request = $request;

        $this->data = $data;
    }

    /**
     * Get is successful
     *
     * @access public
     * @return boolean
     */
    public function isSuccessful()
    {
        if (isset($this->data->ok)) {
            return true;
        }

        return false;
    }

    /**
     * Get the message from the response.
     *
     * @return string
     */
    public function getMessage()
    {
        if ($this->isSuccessful()) {
            return 'SUCCESS';
        }

        return (string)$this->data->error;
    }

    /**
     * Get transaction reference
     *
     * @access public
     * @return string
     */
    public function getTransactionReference()
    {
        if ($this->data->ok instanceof \SimpleXMLElement) {
            foreach ($this->data->ok->children() as $child) {
                if ($child instanceof \SimpleXMLElement) {
                    $attributes = $child->attributes();

                    if (isset($attributes['orderCode'])) {
                        return $attributes['orderCode'];
                    }
                }
            }
        }

        return null;
    }
}
