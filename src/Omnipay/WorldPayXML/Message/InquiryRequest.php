<?php
/**
 * Inquiry request.
 */
namespace Omnipay\WorldPayXML\Message;

/**
 * Class InquiryRequest
 * @package Omnipay\WorldPayXML\Message
 */
class InquiryRequest extends AbstractRequest
{
    /**
     * Get the data for the request.
     *
     * @return \SimpleXMLElement
     */
    public function getData()
    {
        $data = $this->getBase();
        $inquiry = $data->addChild('inquiry');
        $orderInquiry = $inquiry->addChild('orderInquiry');
        $orderInquiry->addAttribute('orderCode', $this->getTransactionId());

        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     *
     * @return integer
     */
    public function getTransactionType()
    {
        return static::INQUIRY_REQUEST;
    }
}
