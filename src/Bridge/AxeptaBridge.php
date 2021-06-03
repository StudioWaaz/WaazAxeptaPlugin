<?php

/**
 * This file was created by the developers from Waaz.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://www.studiowaaz.com and write us
 * an email on developpement@studiowaaz.com.
 */

namespace Waaz\AxeptaPlugin\Bridge;

use Waaz\AxeptaPlugin\Legacy\Axepta;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * @author Ibes Mongabure <developpement@studiowaaz.com>
 */
final class AxeptaBridge implements AxeptaBridgeInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $hmacKey;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $blowfishKey;


    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function createAxepta($hmacKey)
    {
        return new Axepta($hmacKey);
    }

    /**
     * {@inheritDoc}
     */
    public function paymentVerification()
    {
        if ($this->isGetMethod()) {

            $postdata = $this->getQueryData();

            return $postdata;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isGetMethod()
    {

        $currentRequest = $this->requestStack->getCurrentRequest();

        return $currentRequest->isMethod('GET');
    }

    /**
     * @return array
     */
     public function getQueryData()
     {
       $currentRequest = $this->requestStack->getCurrentRequest();

       return $currentRequest->query->all();
     }

    /**
     * @return string
     */
    public function getHmacKey()
    {
        return $this->hmacKey;
    }

    /**
     * @param string $hmacKey
     */
    public function setHmacKey($hmacKey)
    {
        $this->hmacKey = $hmacKey;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return string
     */
    public function getBlowfishKey()
    {
        return $this->blowfishKey;
    }

    /**
     * @param string $blowfishKey
     */
    public function setBlowfishKey($blowfishKey)
    {
        $this->blowfishKey = $blowfishKey;
    }

}
