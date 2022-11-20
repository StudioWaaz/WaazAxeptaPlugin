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
    private RequestStack $requestStack;

    private ?string $hmacKey = null;

    private ?string $merchantId = null;

    private ?string $blowfishKey = null;


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
        $axepta = new Axepta();
        $axepta->setSecretKey($hmacKey);
        $axepta->setMsgVer();
        return $axepta;
    }

    /**
     * {@inheritDoc}
     */
    public function paymentVerification()
    {
        // @Todo
        if ($this->isPostMethod()) {

            $queryData = $this->getQueryData();
            $paymentResponse = $this->createAxepta($this->hmacKey);
            $paymentResponse->setCryptKey($this->blowfishKey);
            $paymentResponse->setResponse($queryData);
            
            return ($paymentResponse->isValid() && $paymentResponse->isSuccessful());
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isPostMethod()
    {

        $currentRequest = $this->requestStack->getCurrentRequest();

        return $currentRequest->isMethod('POST');
    }

    /**
     * @return array
     */
     public function getQueryData()
     {
       $currentRequest = $this->requestStack->getCurrentRequest();

       return $currentRequest->request->all();
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
