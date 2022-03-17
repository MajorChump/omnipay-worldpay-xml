<?php
/**
 * Abstract class that all Requests must extend.
 */
namespace Omnipay\WorldPayXML\Message;

use Guzzle\Plugin\Cookie\Cookie;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;

/**
 * Class AbstractRequest
 * @package Omnipay\WorldPayXML\Message
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * The base URL for the live service.
     */
    const EP_HOST_LIVE = 'https://secure.worldpay.com';

    /**
     * The base URL for the sandbox service.
     */
    const EP_HOST_TEST = 'https://secure-test.worldpay.com';

    /**
     * The service URI
     */
    const EP_PATH = '/jsp/merchant/xml/paymentService.jsp';

    /**
     * The curent Worldpay XML APi version.
     */
    const VERSION = '1.4';

    /**
     * Constants used to indicate the type of request being made.
     */

    /**
     * Payment request
     */
    const PAYMENT_REQUEST = 1;

    /**
     * Payment modification
     */
    const PAYMENT_MODIFY = 2;

    /**
     * Cancel request
     */
    const CANCEL_REQUEST = 3;

    /**
     * Capture request
     */
    const CAPTURE_REQUEST = 4;

    /**
     * Refund request
     */
    const REFUND_REQUEST = 5;

    /**
     * Backoffice code request
     */
    const BACK_OFFICE_CODE_REQUEST = 6;

    /**
     * Authorisation code request
     */
    const AUTHORISATION_CODE_REQUEST = 7;

    /**
     * Inquiry request
     */
    const INQUIRY_REQUEST = 8;

    /**
     * Increase Authorisation request
     */
    const INCREASE_AUTHORISATION_REQUEST = 9;

    /**
     * Void (Cancel or Refund dependant on current payment status) request.
     */
    const VOID_REQUEST = 10;

    /**
     * Observers. Used to report requests and responses to.
     *
     * @var array
     */
    private $observers = [];

    /**
     * Get merchant
     *
     * @access public
     * @return string
     */
    public function getMerchant()
    {
        return $this->getParameter('merchant');
    }

    /**
     * Set merchant
     *
     * @param string $value Merchant
     *
     * @access public
     * @return void
     */
    public function setMerchant($value)
    {
        $this->setParameter('merchant', $value);
    }

    /**
     * Get password
     *
     * @access public
     * @return string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * Set password
     *
     * @param string $value Password
     *
     * @access public
     * @return void
     */
    public function setPassword($value)
    {
        $this->setParameter('password', $value);
    }

    /**
     * Get redirect cookie
     *
     * @access public
     * @return string
     */
    public function getRedirectCookie()
    {
        return $this->getParameter('redirect_cookie');
    }

    /**
     * Set redirect cookie
     *
     * @param string $value Password
     *
     * @access public
     * @return void
     */
    public function setRedirectCookie($value)
    {
        $this->setParameter('redirect_cookie', $value);
    }

    /**
     * Set up the base SimpleXMLElelment for the request with items common to all requests.
     *
     * @return \SimpleXMLElement
     */
    protected function getBase()
    {
        $data = new \SimpleXMLElement('<paymentService />');
        $data->addAttribute('version', self::VERSION);
        $data->addAttribute('merchantCode', $this->getMerchant());

        return $data;
    }

    /**
     * Get installation
     *
     * @access public
     * @return string
     */
    public function getInstallation()
    {
        return $this->getParameter('installation');
    }

    /**
     * Set installation
     *
     * @param string $value Installation
     *
     * @access public
     * @return void
     */
    public function setInstallation($value)
    {
        $this->setParameter('installation', $value);
    }

    /**
     * Send data
     *
     * @param \SimpleXMLElement $data Data
     *
     * @access public
     * @return \Omnipay\Common\Message\ResponseInterface
     */
    public function sendData($data)
    {
        $implementation = new \DOMImplementation();

        $dtd = $implementation->createDocumentType(
            'paymentService',
            '-//WorldPay//DTD WorldPay PaymentService v1//EN',
            'http://dtd.worldpay.com/paymentService_v1.dtd'
        );

        $document = $implementation->createDocument(null, '', $dtd);
        $document->encoding = 'utf-8';

        $node = $document->importNode(dom_import_simplexml($data), true);
        $document->appendChild($node);

        $authorisation = base64_encode(
            $this->getMerchant() . ':' . $this->getPassword()
        );

        $headers = [
            'Authorization' => 'Basic ' . $authorisation,
            'Content-Type' => 'text/xml; charset=utf-8',
        ];

        $cookieJar = new ArrayCookieJar();

        $redirectCookie = $this->getRedirectCookie();

        if (!empty($redirectCookie)) {
            $url = parse_url($this->getEndpoint());

            $cookieJar->add(
                new Cookie(
                    [
                        'domain' => $url['host'],
                        'name' => 'machine',
                        'path' => '/',
                        'value' => $redirectCookie,
                    ]
                )
            );
        }

        $this->cookiePlugin = new CookiePlugin($cookieJar);

        $this->httpClient->addSubscriber($this->cookiePlugin);

        $xml = $document->saveXML();

        $this->notify(
            [
                'request' => preg_replace(
                    '#\<cvc\>([0-9]{3,4})\<\/cvc\>#',
                    '<cvc>***</cvc>',
                    preg_replace(
                        '#\<cardNumber\>([0-9]{10,})\<\/cardNumber\>#',
                        '<cardNumber>**** **** **** ****</cardNumber>',
                        $xml
                    )
                ),
            ]
        );
        /*error_log(preg_replace(
            '#\<cvc\>([0-9]{3,4})\<\/cvc\>#',
            '<cvc>***</cvc>',
            preg_replace(
                '#\<cardNumber\>([0-9]{10,})\<\/cardNumber\>#',
                '<cardNumber>**** **** **** ****</cardNumber>',
                $xml
            )
        ));*/
        $httpResponse = $this->httpClient
            ->post($this->getEndpoint(), $headers, $xml)
            ->send();

        //error_log((string)$httpResponse->getBody());
        $this->notify(['response' => (string)$httpResponse->getBody()]);

        return $this->response = Response::make(
            $this,
            $httpResponse->getBody()
        );
    }

    /**
     * Get endpoint
     *
     * Returns endpoint depending on test mode
     *
     * @access protected
     * @return string
     */
    protected function getEndpoint()
    {
        if ($this->getTestMode()) {
            return self::EP_HOST_TEST . self::EP_PATH;
        }

        return self::EP_HOST_LIVE . self::EP_PATH;
    }

    /**
     * Attach an observer.
     *
     * @param Observer $observer
     */
    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * Detach an attached observer.
     *
     * @param Observer $observer
     */
    public function detach(Observer $observer)
    {
        $this->observers = array_filter(
            $this->observers,
            function ($a) use ($observer) {
                return (!($a === $observer));
            }
        );
    }

    /**
     * Notify all observers.
     *
     * @param $data
     */
    public function notify($data)
    {
        foreach ($this->observers as $observer) {
            $observer->update($this, $data);
        }
    }

    /**
     * Return a value to indicate the transaction type.
     * @return integer
     */
    abstract public function getTransactionType();
}
