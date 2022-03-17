<?php
/**
 * Purchase request.
 */
namespace Omnipay\WorldPayXML\Message;

use Omnipay\Common\CreditCard;

/**
 * Omnipay WorldPay XML Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @var \Guzzle\Plugin\Cookie\CookiePlugin
     *
     * @access protected
     */
    protected $cookiePlugin;

    /**
     * Get accept header
     *
     * @access public
     * @return string
     */
    public function getDynamic3ds()
    {
        return $this->getParameter('dynamic3ds');
    }

    /**
     * Set accept header
     *
     * @param string $value Accept header
     *
     * @access public
     * @return void
     */
    public function setDynamic3ds($value)
    {
        return $this->setParameter('dynamic3ds', $value);
    }

    /**
     * Get accept header
     *
     * @access public
     * @return string
     */
    public function getAcceptHeader()
    {
        return $this->getParameter('acceptHeader');
    }

    /**
     * Set accept header
     *
     * @param string $value Accept header
     *
     * @access public
     * @return void
     */
    public function setAcceptHeader($value)
    {
        return $this->setParameter('acceptHeader', $value);
    }

    /**
     * Get cookie plugin
     *
     * @access public
     * @return \Guzzle\Plugin\Cookie\CookiePlugin
     */
    public function getCookiePlugin()
    {
        return $this->cookiePlugin;
    }

    /**
     * Get pa response
     *
     * @access public
     * @return string
     */
    public function getPaResponse()
    {
        return $this->getParameter('pa_response');
    }

    /**
     * Set pa response
     *
     * @param string $value Pa response
     *
     * @access public
     * @return void
     */
    public function setPaResponse($value)
    {
        return $this->setParameter('pa_response', $value);
    }

    /**
     * Get redirect echo
     *
     * @access public
     * @return string
     */
    public function getRedirectEcho()
    {
        return $this->getParameter('redirect_echo');
    }

    /**
     * Set redirect echo
     *
     * @param string $value Password
     *
     * @access public
     * @return void
     */
    public function setRedirectEcho($value)
    {
        return $this->setParameter('redirect_echo', $value);
    }

    /**
     * Get session
     *
     * @access public
     * @return string
     */
    public function getSession()
    {
        return $this->getParameter('session');
    }

    /**
     * Set session
     *
     * @param string $value Session
     *
     * @access public
     * @return void
     */
    public function setSession($value)
    {
        return $this->setParameter('session', $value);
    }

    /**
     * Get term url
     *
     * @access public
     * @return string
     */
    public function getTermUrl()
    {
        return $this->getParameter('termUrl');
    }

    /**
     * Set term url
     *
     * @param string $value Term url
     *
     * @access public
     * @return void
     */
    public function setTermUrl($value)
    {
        return $this->setParameter('termUrl', $value);
    }

    /**
     * Get user agent header
     *
     * @access public
     * @return string
     */
    public function getUserAgentHeader()
    {
        return $this->getParameter('userAgentHeader');
    }

    /**
     * Set user agent header
     *
     * @param string $value User agent header
     *
     * @access public
     * @return void
     */
    public function setUserAgentHeader($value)
    {
        return $this->setParameter('userAgentHeader', $value);
    }

    /**
     * Get data
     *
     * @access public
     * @return \SimpleXMLElement
     */
    public function getData()
    {
        $this->validate('amount', 'card');
        $this->getCard()->validate();

        $data = $this->getBase();

        $order = $data->addChild('submit')->addChild('order');
        $order->addAttribute('orderCode', $this->getTransactionId());
        $installationId = $this->getInstallation();
        if (!empty($installationId)) {
            $order->addAttribute('installationId', $installationId);
        }

        $order->addChild('description', $this->getDescription());

        $amount = $order->addChild('amount');
        $amount->addAttribute('value', $this->getAmountInteger());
        $amount->addAttribute('currencyCode', $this->getCurrency());
        $amount->addAttribute('exponent', $this->getCurrencyDecimalPlaces());

        $payment = $order->addChild('paymentDetails');

        $codes = [
            CreditCard::BRAND_AMEX => 'AMEX-SSL',
            CreditCard::BRAND_DANKORT => 'DANKORT-SSL',
            CreditCard::BRAND_DINERS_CLUB => 'DINERS-SSL',
            CreditCard::BRAND_DISCOVER => 'DISCOVER-SSL',
            CreditCard::BRAND_JCB => 'JCB-SSL',
            CreditCard::BRAND_LASER => 'LASER-SSL',
            CreditCard::BRAND_MAESTRO => 'MAESTRO-SSL',
            CreditCard::BRAND_MASTERCARD => 'ECMC-SSL',
            CreditCard::BRAND_SWITCH => 'MAESTRO-SSL',
            CreditCard::BRAND_VISA => 'VISA-SSL',
        ];

        $card = $payment->addChild($codes[$this->getCard()->getBrand()]);
        $card->addChild('cardNumber', $this->getCard()->getNumber());

        $expiry = $card->addChild('expiryDate')->addChild('date');
        $expiry->addAttribute('month', $this->getCard()->getExpiryDate('m'));
        $expiry->addAttribute('year', $this->getCard()->getExpiryDate('Y'));

        $card->addChild('cardHolderName', trim($this->getCard()->getName()));

        if (
            $this->getCard()->getBrand() == CreditCard::BRAND_MAESTRO
            || $this->getCard()->getBrand() == CreditCard::BRAND_SWITCH
        ) {
            $start = $card->addChild('startDate')->addChild('date');
            $start->addAttribute('month', $this->getCard()->getStartDate('m'));
            $start->addAttribute('year', $this->getCard()->getStartDate('Y'));

            $card->addChild('issueNumber', $this->getCard()->getIssueNumber());
        }

        $card->addChild('cvc', $this->getCard()->getCvv());

        $address = $card->addChild('cardAddress')->addChild('address');
        $address->addChild('street', $this->getCard()->getAddress1());
        $address->addChild('postalCode', $this->getCard()->getPostcode());
        $address->addChild('countryCode', $this->getCard()->getCountry());

        $clientIp = $this->getClientIP();
        $sessionId = $this->getSession();

        if (!empty($clientIp) && !empty($sessionId)) {
            $session = $payment->addChild('session');
            $session->addAttribute('shopperIPAddress', $clientIp);
            $session->addAttribute('id', $sessionId);
        }

        $paResponse = $this->getPaResponse();

        if (!empty($paResponse)) {
            $info3DSecure = $payment->addChild('info3DSecure');
            $info3DSecure->addChild('paResponse', $paResponse);
        }

        $shopper = $order->addChild('shopper');

        $email = $this->getCard()->getEmail();

        if (!empty($email)) {
            $shopper->addChild(
                'shopperEmailAddress',
                $this->getCard()->getEmail()
            );
        }

        $shippingAddress = $order->addChild('shippingAddress');
        $address = $shippingAddress->addChild('address');
        $address->addChild('firstName', $this->getCard()->getShippingFirstName());
        $address->addChild('lastName', $this->getCard()->getShippingLastName());
        $address->addChild('street', $this->getCard()->getShippingAddress1());
        $address->addChild('postalCode', $this->getCard()->getShippingPostcode());
        $address->addChild('city', $this->getCard()->getShippingCity());
        $address->addChild('countryCode', $this->getCard()->getShippingCountry());
        $address->addChild('telephoneNumber', $this->getCard()->getshippingPhone());

        $browser = $shopper->addChild('browser');
        $browser->addChild('acceptHeader', $this->getAcceptHeader());
        $browser->addChild('userAgentHeader', $this->getUserAgentHeader());

        $echoData = $this->getRedirectEcho();

        if (!empty($echoData)) {
            $order->addChild('echoData', $echoData);
        }

        if ($this->getDynamic3ds()) {
            $dynamic3ds = $order->addChild('dynamic3DS');
            $dynamic3ds->addAttribute('overrideAdvice', $this->getDynamic3ds());
        }

        return $data;
    }

    /**
     * Return a value to indicate the transaction type.
     * @return integer
     */
    public function getTransactionType()
    {
        return static::PAYMENT_REQUEST;
    }
}
